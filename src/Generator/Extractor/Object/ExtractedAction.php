<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

class ExtractedAction
{

	/**
	 * @param ExtractedActionParameter[] $parameters
	 */
	public function __construct(
		private string $name,
		private array $parameters = [],
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return ExtractedActionParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

}
