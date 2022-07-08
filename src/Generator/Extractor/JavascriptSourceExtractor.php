<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor;

use LogicException;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use WebChemistry\SimpleJson\Exception\SimpleJsonSyntaxError;
use WebChemistry\SimpleJson\SimpleJsonParser;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedAction;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedActionParameter;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedClass;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedController;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedTarget;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedValue;
use WebChemistry\Stimulus\Generator\Extractor\Object\ResolvedType;
use WebChemistry\Stimulus\Parser\CommentSimpleParser;

final class JavascriptSourceExtractor implements StimulusExtractor
{

	public function __construct(
		private string $baseDir,
	)
	{
		$this->baseDir = FileSystem::normalizePath($this->baseDir);
	}

	/**
	 * @return array{string, string, string}[]
	 */
	public function getFiles(): array
	{
		$offset = strlen($this->baseDir) + 1;
		$files = [];

		foreach (Finder::findFiles('*_controller.js', '*-controller.js')->from($this->baseDir) as $file) {
			$source = strtr(substr($file->getPathname(), $offset), ['\\' => '/']);

			$files[] = [$file->getPathname(), $source, substr($source, 0, -14)];
		}

		return $files;
	}

	/**
	 * @inheritDoc
	 */
	public function getExtractedControllers(): array
	{
		$controllers = [];
		foreach ($this->getFiles() as [$file, $source, $controllerName]) {
			$controllerName = strtr($controllerName, [
				'_' => '-',
				'/' => '--',
			]);

			$content = FileSystem::read($file);

			preg_match_all('#/\*\*.*?\*/\s*([a-zA-Z]\w+)?#s', $content, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

			$targets = [];
			$values = [];
			$classes = [];
			$actions = [];

			foreach ($matches as $match) {
				$comment = $match[0];
				$functionName = $match[1];

				if (str_contains($comment, '@controller')) {

					// @property {0: type} {1: name} {2?: options}
					foreach (CommentSimpleParser::parse($comment, ['property']) as [, $arguments]) {
						if (!isset($arguments[0]) || !isset($arguments[1])) {
							continue; // missing type and name
						}

						$type = $arguments[0];
						$name = $arguments[1];
						$other = $arguments[2] ?? '';

						if (preg_match('#^has[A-Z]#', $name)) {
							continue;
						}

						$options = [];
						if (str_starts_with($other, '{')) {
							$options = SimpleJsonParser::parse($other);
						}

						if (str_ends_with($name, '?')) {
							$name = substr($name, 0, -1);
							$required = false;
						} else {
							$required = !in_array('optional', $options, true);
						}

						if (str_ends_with($name, 'Target')) {
							$targets[$name] = new ExtractedTarget($name);
						} elseif (str_ends_with($name, 'Targets')) {
							$name = substr($name, 0, -1);

							$targets[$name] = new ExtractedTarget($name);
						} elseif (str_ends_with($name, 'Value')) {
							$type = $this->resolveType($type, $options);

							$values[$name] = new ExtractedValue(
								$name,
								$type->getType(),
								$required,
								$type->getCommentType()
							);
						} elseif (str_ends_with($name, 'Class')) {
							$classes[$name] = new ExtractedClass($name, $required);
						} elseif (str_ends_with($name, 'Classes')) {
							$classes[$name] = new ExtractedClass($name, $required);
						}
					}

				} elseif (str_contains($comment, '@action')) {
					if (!$functionName) {
						throw new LogicException(sprintf('Cannot match function name of action in %s.', $source));
					}

					$params = [];

					foreach (CommentSimpleParser::parse($comment, ['action-param', 'param']) as [$name, $arguments]) {
						if ($name === 'action-param') {
							if (!isset($arguments[0]) || !isset($arguments[1])) {
								throw new LogicException(
									sprintf('@action-param must have type and variable name in @action in %s.', $source)
								);
							}

							$options = $this->parseOptions($arguments[2] ?? '');
							$variable = $arguments[1];
							$type = $this->resolveType($arguments[0], $options);

							if (str_ends_with($variable, '?')) {
								$variable = substr($variable, 0, -1);
								$required = false;
							} else {
								$required = !in_array($variable, $options['optional'] ?? [], true);
							}

							$params[$variable] = new ExtractedActionParameter(
								$variable,
								$type->getType(),
								$required,
								$type->getCommentType(),
							);
						} else {
							if (!isset($arguments[0]) || !isset($arguments[1])) {
								continue;
							}

							if (!str_starts_with($arguments[0], '{')) {
								continue;
							}

							if (!preg_match('#params\s*:\s*(\{.*?})#', $arguments[0], $matches)) {
								continue;
							}

							try {
								$variables = SimpleJsonParser::parse($matches[1]);
							} catch (SimpleJsonSyntaxError $e) {
								continue;
							}

							$options = [];
							if (str_starts_with($str = $arguments[2] ?? '', '{')) {
								$options = SimpleJsonParser::parse($str);
							}

							$optional = $options['optional'] ?? [];

							foreach ($variables as $variableName => $type) {
								if (str_ends_with($variableName, '?')) {
									$variableName = substr($variableName, 0, -1);
									$required = false;
								} else {
									$required = !in_array($variableName, $optional, true);
								}

								$type = $this->resolveType($type);

								$params[$variableName] = new ExtractedActionParameter(
									$variableName,
									$type->getType(),
									$required,
									$type->getCommentType(),
								);
							}
						}
					}

					$actions[$functionName] = new ExtractedAction($functionName, array_values($params));
				}
			}

			if (!$values && !$actions && !$targets && !$classes) {
				continue;
			}

			$controllers[] = new ExtractedController(
				$controllerName,
				$values,
				$actions,
				$targets,
				$classes,
				[
					'source' => $source,
				]
			);
		}

		return $controllers;
	}

	/**
	 * @return mixed[]
	 */
	private function parseOptions(string $other): array
	{
		$options = [];

		if (str_starts_with($other, '{')) {
			$options = SimpleJsonParser::parse($other);
		}

		return $options;
	}

	/**
	 * @param mixed[] $options
	 */
	private function resolveType(string $type, array $options = []): ResolvedType
	{
		if (isset($options['type'])) {
			return new ResolvedType($options['type'], $options['commentType'] ?? null);
		}

		$type = trim(trim($type, '{}'));

		if (str_contains($type, '[')) {
			return match (strtolower($type)) {
				'string[]' => new ResolvedType('array', 'string[]'),
				'boolean[]', 'bool[]' => new ResolvedType('array', 'bool[]'),
				'number[]' => new ResolvedType('array', 'array<' . ($options['number'] ?? 'int|float') .'>'),
				default => new ResolvedType('array', 'mixed[]'),
			};
		}

		return match (strtolower($type)) {
			'string' => new ResolvedType('string'),
			'boolean', 'bool' => new ResolvedType('bool'),
			'number' => new ResolvedType($options['number'] ?? 'int|float'),
			'object' => new ResolvedType('array', 'mixed[]'),
			default => new ResolvedType('mixed'),
		};
	}

}
