<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use LogicException;
use WebChemistry\Stimulus\Builder\AttributesBuilder;

final class StimulusMacroService
{

	public static function applyDefaultControllerToArray(array $values, string $controller): array
	{
		foreach ($values as &$value) {
			$value = self::applyDefaultController($value, $controller);
		}

		return $values;
	}

	public static function applyDefaultController(array $values, string $controller): array
	{
		if (!isset($values['controller']) || !isset($values[1])) {
			$values['controller'] = $controller;
		}

		return $values;
	}

	public static function makeTarget(string $target, string $controller): string
	{
		$builder = new AttributesBuilder();
		$builder->addControllerDataAttribute($controller, 'target', $target);

		return $builder->toString();
	}

	public static function makeAction(string $method, string $controller, string $type = ''): string
	{
		$builder = new AttributesBuilder();

		if ($type) {
			$type .= '->';
		}

		$builder->addDataAttribute('action', sprintf('%s%s#%s', $type, $builder->componentName($controller), $method));

		return $builder->toString();
	}

	public static function makeControllers(array $controllers): string
	{
		$builder = new AttributesBuilder();

		foreach ($controllers as $controller => $parameters) {
			self::makeController($builder, $controller, $parameters ?? []);
		}

		return $builder->toString();
	}

	private static function makeController(AttributesBuilder $builder, string $controller, array $arguments): void
	{
		$builder->addControllerAttribute($controller);

		foreach ($arguments as $name => $value) {
			if (!str_ends_with($name, 'Class')) {
				$name .= 'Value';
			}

			$builder->addControllerDataAttribute($controller, $name, $value);
		}
	}

}
