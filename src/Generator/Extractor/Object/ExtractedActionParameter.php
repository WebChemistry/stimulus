<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

class ExtractedActionParameter
{

	public function __construct(
		private string $name,
		private string $type,
		private bool $isRequired = false,
		private ?string $commentType = null,
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function isRequired(): bool
	{
		return $this->isRequired;
	}

	public function getCommentType(): ?string
	{
		return $this->commentType;
	}

}
