<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

class ExtractedTarget
{

	public function __construct(
		private string $name,
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

}
