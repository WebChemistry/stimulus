<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use LogicException;
use Nette\Utils\Arrays;
use WebChemistry\Stimulus\Builder\AttributesBuilder;
use WebChemistry\Stimulus\Parser\ActionAttributeParser;

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

	public static function pushToStack(?array &$stack, array $controllers): void
	{
		if ($stack === null) {
			$stack = [];
		}

		array_push($stack, array_map(
			fn (string $controller) => AttributesBuilder::controllerName($controller),
			array_keys($controllers)
		));
	}

	public static function makeTargets(array $controllerStack, array $targets): string
	{
		$builder = new AttributesBuilder();
		$controller = Arrays::first(Arrays::last($controllerStack));

		$builder->addControllerDataAttribute($controller, 'target', implode(' ', $targets));

		return $builder->toString();
	}

	public static function makeActions(array $controllerStack, array $actions): string
	{
		$last = Arrays::last($controllerStack);

		$builder = new AttributesBuilder();
		$attribute = [];
		foreach ($actions as $action => $params) {
			$result = ActionAttributeParser::parse($action, $last ?? []);
			foreach ((array) $params as $name => $value) {
				$builder->addControllerDataAttribute($result->controller, $name . 'Param', $value);
			}

			$attribute[] = $result->attribute;
		}

		$builder->addDataAttribute('action', implode(' ', $attribute));

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
