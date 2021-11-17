<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Html;

use WebChemistry\Stimulus\Builder\AttributesBuilder;

final class StimulusControllerAttribute implements StimulusAttributeRenderable
{

	/**
	 * @param array<string, string|int|float|bool|mixed[]|null> $values
	 * @param array<string, string|null> $classes
	 */
	public function __construct(
		private string $controller,
		private array $values = [],
		private array $classes = [],
	)
	{
	}

	public function renderAttribute(AttributesBuilder $builder): void
	{
		$builder->addController($this->controller);

		foreach ($this->values as $name => $value) {
			if ($value === null) {
				continue;
			}

			$builder->addValue($this->controller, $name, $value);
		}

		foreach ($this->classes as $name => $value) {
			if ($value === null) {
				continue;
			}

			$builder->addClass($this->controller, $name, $value);
		}
	}

}
