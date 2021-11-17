<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Html;

use Utilitte\Php\Strings;
use WebChemistry\Stimulus\Builder\AttributesBuilder;

final class StimulusActionAttribute implements StimulusAttributeRenderable
{

	private string $controller;

	/**
	 * @param array<string, string|int|float|mixed[]|bool|null> $parameters
	 */
	public function __construct(
		private string $action,
		private array $parameters = [],
	)
	{
		$this->processAction();
	}

	private function processAction(): void
	{
		[$type, $action] = Strings::splitByNeedle($this->action, '->');

		[$controller, $method] = Strings::splitByNeedle($action, '#');

		if ($type) {
			$explode = explode(':', $type);
			if (count($explode) > 1) {
				$explode[0] = AttributesBuilder::controllerName($explode[0]);
			}

			$type = implode(':', $explode);
		}

		if ($controller) {
			$controller = AttributesBuilder::controllerName($controller);
		}

		$this->controller = $controller;

		$this->action = Strings::joinWith('#', Strings::joinWith('->', $type, $controller), $method);
	}

	public function renderAttribute(AttributesBuilder $builder): void
	{
		$builder->addAction($this->action);

		foreach ($this->parameters as $name => $value) {
			if ($value === null) {
				continue;
			}

			$builder->addParameter($this->controller, $name, $value);
		}
	}

}
