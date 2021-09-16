<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Builder;

final class StimulusControllerBuilder
{

	private AttributesBuilder $builder;

	public function __construct(
		private string $controller,
		?AttributesBuilder $builder = null,
	)
	{
		$this->builder = $builder ?? new AttributesBuilder();
		$this->builder->addControllerAttribute($this->controller);
	}

	public static function create(string $controller): self
	{
		return new self($controller);
	}

	public function addValue(string $name, mixed $value): self
	{
		if (!str_ends_with($name, 'Value')) {
			$name .= 'Value';
		}

		$this->builder->addControllerDataAttribute($this->controller, $name, $value);

		return $this;
	}

	public function addClass(string $name, mixed $value): self
	{
		if (!str_ends_with($name, 'Class')) {
			$name .= 'Class';
		}

		$this->builder->addControllerDataAttribute($this->controller, $name, $value);

		return $this;
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		return $this->builder->toArray();
	}

}
