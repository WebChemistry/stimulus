<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Type;

final class StimulusController
{

	/**
	 * @param array<string, mixed> $values
	 * @param array<string, string|null> $classes
	 */
	public function __construct(
		private string $name,
		private array $values,
		private array $classes,
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getValues(): array
	{
		return $this->values;
	}

	/**
	 * @return array<string, string|null>
	 */
	public function getClasses(): array
	{
		return $this->classes;
	}

}
