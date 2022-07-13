<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

final class ExtractedEvent
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
