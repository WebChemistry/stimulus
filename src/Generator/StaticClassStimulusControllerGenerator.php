<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator;

use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpFile;
use Utilitte\Asserts\TypeAssert;
use WebChemistry\Stimulus\Generator\ClassName\ClassNameConverter;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedActionParameter;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedClass;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedControllerParameter;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedValue;
use WebChemistry\Stimulus\Generator\Extractor\StimulusExtractor;
use WebChemistry\Stimulus\Helper\ControllerHelper;
use WebChemistry\Stimulus\Type\StimulusAction;
use WebChemistry\Stimulus\Type\StimulusController;
use WebChemistry\Stimulus\Type\StimulusEvent;
use WebChemistry\Stimulus\Type\StimulusTarget;

final class StaticClassStimulusControllerGenerator implements StimulusControllerGenerator
{

	public function __construct(
		private StimulusExtractor $extractor,
		private ClassNameConverter $classNameConverter,
	)
	{
	}

	/**
	 * @return GeneratedController[]
	 */
	public function generate(): array
	{
		$generated = [];

		foreach ($this->extractor->getExtractedControllers() as $controller) {
			$className = $this->classNameConverter->convertToClassName($controller->getName());

			$file = new PhpFile();
			$file->setStrictTypes();

			$comment = 'NOTE: This class is auto generated';
			if (($source = $controller->getContext()['source'] ?? null) && is_string($source)) {
				$comment = sprintf('NOTE: This class is auto generated by file: %s', $source);
			}

			$file->addComment($comment);
			$file->addComment('Do not edit the class manually');

			$namespace = $file->addNamespace(Helpers::extractNamespace($className));

			$namespace->addUse(StimulusTarget::class);
			$namespace->addUse(StimulusAction::class);
			$namespace->addUse(StimulusController::class);

			$class = $namespace->addClass(Helpers::extractShortName($className))
				->setAbstract();

			$class->addConstant('identifier', $controller->getName())
				->setFinal(PHP_VERSION_ID >= 80100)
				->setVisibility('public');

			if (($deprecated = $controller->getContext()['deprecated'] ?? null) !== null) {
				$class->addComment(trim(sprintf('@deprecated %s', TypeAssert::string($deprecated))));
			}

			$valuesAndClasses = $this->sortValuesAndClassesByRequired($controller->getValues(), $controller->getClasses());

			$constructor = $class->addMethod('construct')
				->setStatic()
				->setReturnType(StimulusController::class);

			$constructor->addBody(
				sprintf(
					'return new %s(self::identifier, %s, %s);',
					$namespace->simplifyType(StimulusController::class),
					ControllerHelper::argumentMap(
						array_map(fn (ExtractedValue $value) => $value->getName(), $controller->getValues()),
						indent: 1,
					),
					ControllerHelper::argumentMap(
						array_map(fn (ExtractedClass $class) => $class->getName(), $controller->getClasses()),
						indent: 1,
					),
				)
			);

			foreach ($valuesAndClasses as $value) {
				$this->addTypeAndDefaultToParameter(
					$constructor->addParameter($value->getName()),
					$value->getType(),
					$value->isRequired(),
				);

				if ($comment = $value->getCommentType()) {
					$constructor->addComment(sprintf('@param %s $%s', $comment, $value->getName()));
				}
			}

			foreach ($controller->getActions() as $action) {
				$method = $class->addMethod($action->getName() . 'Action')
					->setStatic()
					->setReturnType(StimulusAction::class);

				$method->addBody(
					sprintf(
						'return new %s(self::identifier, ?, %s);',
						$namespace->simplifyType(StimulusAction::class),
						ControllerHelper::argumentMap(
							array_map(fn (ExtractedActionParameter $parameter) => $parameter->getName(), $action->getParameters()),
							'Param',
							1,
						),
					),
					[$action->getName()]
				);

				foreach ($action->getParameters() as $parameter) {
					$type = $parameter->getType();
					if (!$parameter->isRequired()) {
						$type = $this->makeTypeNullable($type);
					}

					$param = $method->addParameter($parameter->getName())
						->setType($type);

					if (!$parameter->isRequired()) {
						$param->setDefaultValue(null);
					}

					if ($comment = $parameter->getCommentType()) {
						$method->addComment(sprintf('@param %s $%s', $comment, $parameter->getName()));
					}
				}
			}

			foreach ($controller->getTargets() as $target) {
				$namespace->addUse(StimulusTarget::class);

				$class->addMethod($target->getName())
					->addBody(sprintf('return new %s(self::identifier, ?);', $namespace->simplifyType(StimulusTarget::class)), [$target->getName()])
					->setStatic()
					->setReturnType(StimulusTarget::class);
			}

			foreach ($controller->getEvents() as $event) {
				$namespace->addUse(StimulusEvent::class);

				$class->addMethod($event->getName() . 'Event')
					->addBody(sprintf('return new %s(self::identifier, ?);', $namespace->simplifyType(StimulusEvent::class)), [$event->getName()])
					->setStatic()
					->setReturnType(StimulusEvent::class);
			}

			$generated[] = new GeneratedController($file, $controller, $className);
		}

		return $generated;
	}

	private function addTypeAndDefaultToParameter(Parameter $parameter, string $type, bool $isRequired): void
	{
		if (!$isRequired) {
			$type = $this->makeTypeNullable($type);
			$parameter->setDefaultValue(null);
		}

		$parameter->setType($type);
	}

	private function makeTypeNullable(string $type): string
	{
		if (str_contains($type, '|')) {
			if (!str_contains($type, '|null') && !str_contains($type, 'null|')) {
				return $type . '|null';
			}
		} elseif (!str_contains($type, '?')) {
			return '?' . $type;
		}

		return $type;
	}

	/**
	 * @param ExtractedControllerParameter[] $values
	 * @param ExtractedControllerParameter[] $classes
	 * @return ExtractedControllerParameter[]
	 */
	private function sortValuesAndClassesByRequired(array $values, array $classes): array
	{
		$required = [];

		foreach ($values as $key => $value) {
			if ($value->isRequired()) {
				$required[] = $value;

				unset($values[$key]);
			}
		}

		foreach ($classes as $key => $class) {
			if ($class->isRequired()) {
				$required[] = $class;

				unset($classes[$key]);
			}
		}

		return array_merge($required, array_values($values), array_values($classes));
	}

}
