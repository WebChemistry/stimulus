<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Object;

use JetBrains\PhpStorm\Immutable;

final class Controller
{

	public function __construct(
		#[Immutable]
		public string $controller,
		#[Immutable]
		public array $values = [],
		#[Immutable]
		public array $classes = [],
	)
	{
	}

	public function __toString(): string
	{
		return $this->controller;
	}

}
