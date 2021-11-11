<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use LogicException;
use Nette\Utils\Arrays;
use WebChemistry\Stimulus\Builder\AttributesBuilder;
use WebChemistry\Stimulus\Html\StimulusHtml;
use WebChemistry\Stimulus\Object\Action;
use WebChemistry\Stimulus\Object\Controller;
use WebChemistry\Stimulus\Object\Target;
use WebChemistry\Stimulus\Parser\ActionAttributeParser;

final class StimulusMacroService
{

	public static function createController(string $controller, array $values = [], array $classes = []): Controller
	{
		return new Controller($controller, $values, $classes);
	}

	public static function createTarget(Controller|string $controller, string $name): Target
	{
		return new Target((string) $controller, $name);
	}

	public static function createAction(string $action, Controller|string ... $controllers): Action
	{
		return new Action(
			sprintf($action, ...array_map(fn (Controller|string $controller) => (string) $controller, $controllers))
		);
	}

	public static function renderAttributes(Controller|Action|Target ... $attributes): string
	{
		$html = new StimulusHtml();

		foreach ($attributes as $attribute) {
			match (true) {
				$attribute instanceof Controller => $html->addController($attribute),
				$attribute instanceof Action => $html->addAction($attribute),
				$attribute instanceof Target => $html->addTarget($attribute),
			};
		}

		return ' ' . $html->render();
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

		return ' ' . $builder->toString();
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

		return ' ' . $builder->toString();
	}

	public static function makeControllers(array $controllers): string
	{
		$builder = new AttributesBuilder();

		foreach ($controllers as $controller => $parameters) {
			self::makeController($builder, $controller, $parameters ?? []);
		}

		return ' ' . $builder->toString();
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
