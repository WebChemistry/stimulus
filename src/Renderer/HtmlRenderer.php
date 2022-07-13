<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Renderer;

use DomainException;
use WebChemistry\Stimulus\Renderer\Html\HtmlBuilder;
use WebChemistry\Stimulus\Type\StimulusAction;
use WebChemistry\Stimulus\Type\StimulusController;
use WebChemistry\Stimulus\Type\StimulusTarget;
use WebChemistry\Stimulus\Type\StimulusType;

final class HtmlRenderer
{

	/**
	 * @param StimulusType|StimulusType[] ...$types
	 * @return array<string, string> attribute => value
	 */
	public static function toArray(StimulusType|array ... $types): array
	{
		return self::createBuilder($types)->toArray();
	}

	/**
	 * @param StimulusType|StimulusType[]|null ...$types
	 */
	public static function render(StimulusType|array|null ... $types): string
	{
		return self::createBuilder($types)->toString();
	}

	/**
	 * @param array<StimulusType|mixed[]|null> $types
	 */
	private static function createBuilder(array $types): HtmlBuilder
	{
		self::processTypes($builder = new HtmlBuilder(), $types);

		return $builder;
	}

	/**
	 * @param HtmlBuilder $builder
	 * @param array<StimulusType|mixed[]|null> $types
	 */
	private static function processTypes(HtmlBuilder $builder, array $types): void
	{
		foreach ($types as $type) {
			if ($type === null) {
				continue;
				
			} else if (is_array($type)) {
				self::processTypes($builder, $type);

			} else if (!$type instanceof StimulusType) {
				throw new DomainException(
					sprintf('Parameter must be type of %s, %s given.', StimulusType::class, get_debug_type($type))
				);

			} else {
				self::proccessType($builder, $type);

			}
		}
	}

	private static function proccessType(HtmlBuilder $builder, StimulusType $type): void
	{
		if ($type instanceof StimulusAction) {
			self::processAction($builder, $type);

		} elseif ($type instanceof StimulusTarget) {
			self::processTarget($builder, $type);

		} else if ($type instanceof StimulusController) {
			self::processController($builder, $type);

		} else {
			throw new DomainException(
				sprintf('%s class does not support stimulus type %s', static::class, $type::class)
			);

		}
	}

	private static function processAction(HtmlBuilder $builder, StimulusAction $action): void
	{
		$builder->appendAttribute('data-action', self::renderAction($action));

		foreach ($action->getParameters() as $name => $value) {
			if ($value === null) {
				continue;
			}

			$builder->appendAttribute(
				sprintf('data-%s-%s', $action->getController(), $builder->camel2Dashed($name)),
				$value,
			);
		}
	}

	private static function processController(HtmlBuilder $builder, StimulusController $controller): void
	{
		$name = $controller->getName();

		$builder->appendAttribute('data-controller', $name);

		foreach ($controller->getValues() as $name => $value) {
			if ($value === null) {
				continue;
			}

			$builder->appendAttribute(
				sprintf('data-%s-%s', $controller->getName(), $builder->camel2Dashed($name)),
				$value,
			);
		}

		foreach ($controller->getClasses() as $name => $value) {
			if ($value === null) {
				continue;
			}

			$builder->appendAttribute(
				sprintf('data-%s-%s', $controller->getName(), $builder->camel2Dashed($name)),
				$value,
			);
		}
	}

	private static function processTarget(HtmlBuilder $builder, StimulusTarget $target): void
	{
		$name = $target->getName();

		$builder->appendAttribute(
			sprintf('data-%s-target', $target->getController()),
			str_ends_with($name, 'Target') ? substr($name, 0, -6) : $name,
		);
	}

	private static function renderAction(StimulusAction $action): string
	{
		$build = ($event = $action->getEvent()) ? sprintf('%s->', $event) : '';

		$build .= sprintf('%s#%s', $action->getController(), $action->getName());

		if ($action->hasOptions()) {
			$build .= ':' . implode(':', $action->getOptions());
		}

		return $build;
	}

}
