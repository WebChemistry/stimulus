<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Object;

use JetBrains\PhpStorm\Immutable;

final class Target
{

	public function __construct(
		#[Immutable]
		public string $controller,
		#[Immutable]
		public string $name,
	)
	{
	}

}
