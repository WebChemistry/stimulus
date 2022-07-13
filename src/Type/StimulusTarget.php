<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Type;

final class StimulusTarget implements StimulusType
{

	public function __construct(
		private string $controller,
		private string $name,
	)
	{
	}

	public function getController(): string
	{
		return $this->controller;
	}

	public function getName(): string
	{
		return $this->name;
	}

}
