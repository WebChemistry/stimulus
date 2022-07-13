<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor;

use LogicException;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use SplFileInfo;
use Utilitte\Asserts\TypeAssert;
use WebChemistry\SimpleJson\Exception\SimpleJsonSyntaxError;
use WebChemistry\SimpleJson\SimpleJsonParser;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedAction;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedActionParameter;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedClass;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedController;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedEvent;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedTarget;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedValue;
use WebChemistry\Stimulus\Generator\Extractor\Object\ResolvedType;
use WebChemistry\Stimulus\Parser\CommentSimpleParser;

final class JavascriptSourceExtractor implements StimulusExtractor
{

	/** @var ExtractedController[] */
	private array $controllers;

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

		/** @var SplFileInfo $file */
		foreach (Finder::findFiles('*_controller.js', '*-controller.js')->from($this->baseDir) as $file) {
			$source = strtr(substr($file->getPathname(), $offset), ['\\' => '/']);

			$files[] = [$file->getPathname(), $source, substr($source, 0, -14)];
		}

		return $files;
	}

	/**
	 * @return ExtractedController[]
	 */
	public function getExtractedControllers(): array
	{
		if (!isset($this->controllers)) {
			$this->controllers = [];

			foreach ($this->getFiles() as [$file, $source, $controllerName]) {
				$controllerName = strtr($controllerName, [
					'_' => '-',
					'/' => '--',
				]);

				$content = FileSystem::read($file);

				preg_match_all('#/\*\*.*?\*/\s*([a-zA-Z]\w+)?#s', $content, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

				$extract = false;
				$context = [
					'source' => $source,
				];
				$targets = $values = $classes = $actions = $events = [];

				foreach ($matches as $match) {
					$comment = $match[0];
					$functionName = $match[1];

					if (str_contains($comment, '@controller')) {
						$extract = true;

						// @property {0: type} {1: name} {2?: options}
						// @dispatch {0: name}
						// @deprecated {0?: description}
						foreach (CommentSimpleParser::parse($comment, ['property', 'dispatch', 'deprecated']) as [$annotation, $arguments]) {
							if ($annotation === 'property') {
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

							} else if ($annotation === 'dispatch') {
								if (!isset($arguments[0])) {
									continue; // missing name
								}

								$name = $arguments[0];

								$events[$name] = new ExtractedEvent($name);
							} else if ($annotation === 'deprecated') {
								$context['deprecated'] = $arguments[0] ?? '';
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
									$required = !in_array(
										$variable,
										$this->getArrayFromOptions($options, 'optional'),
										true,
									);
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
								} catch (SimpleJsonSyntaxError) {
									continue;
								}

								$options = [];
								if (str_starts_with($str = $arguments[2] ?? '', '{')) {
									$options = SimpleJsonParser::parse($str);
								}

								/**
								 * @var string $variableName
								 * @var string $type
								 */
								foreach ($variables as $variableName => $type) {
									if (str_ends_with($variableName, '?')) {
										$variableName = substr($variableName, 0, -1);
										$required = false;
									} else {
										$required = !in_array(
											$variableName,
											$this->getArrayFromOptions($options, 'optional'),
											true,
										);
									}

									$resolvedType = $this->resolveType($type);

									$params[$variableName] = new ExtractedActionParameter(
										$variableName,
										$resolvedType->getType(),
										$required,
										$resolvedType->getCommentType(),
									);
								}
							}
						}

						$actions[$functionName] = new ExtractedAction($functionName, array_values($params));
					}
				}

				if (!$extract) {
					continue;
				}

				$this->controllers[] = $extractedController = new ExtractedController(
					$controllerName,
					$values,
					$actions,
					$targets,
					$classes,
					$events,
					$context,
				);
			}
		}

		return $this->controllers;
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
	 * @return mixed[]
	 */
	private function getArrayFromOptions(array $options, string $index): array
	{
		if (!isset($options[$index])) {
			return [];
		}

		return is_array($options[$index]) ? $options[$index] : [];
	}

	/**
	 * @param mixed[] $options
	 */
	private function resolveType(string $type, array $options = []): ResolvedType
	{
		$commentType = TypeAssert::stringOrNull($options['commentType'] ?? null);

		if (isset($options['type'])) {
			return new ResolvedType(TypeAssert::string($options['type']), $commentType);
		}

		$type = trim(trim($type, '{}'));

		if (str_contains($type, '[')) {
			return match (strtolower($type)) {
				'string[]' => new ResolvedType('array', ($commentType ?? 'string[]')),
				'boolean[]', 'bool[]' => new ResolvedType('array', ($commentType ?? 'bool[]')),
				'number[]' => new ResolvedType('array', ($commentType ?? 'array<' . (TypeAssert::string($options['number'] ?? 'int|float')) .'>')),
				default => new ResolvedType('array', ($commentType ?? 'mixed[]')),
			};
		}

		return match (strtolower($type)) {
			'string' => new ResolvedType('string', $commentType ?? null),
			'boolean', 'bool' => new ResolvedType('bool', $commentType ?? null),
			'number' => new ResolvedType(TypeAssert::string($options['number'] ?? 'int|float'), $commentType ?? null),
			'object' => new ResolvedType('array', ($commentType ?? 'mixed[]')),
			default => new ResolvedType('mixed', $commentType ?? null),
		};
	}

}
