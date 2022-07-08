<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

class ExtractedValue implements ExtractedControllerParameter
{

	public function __construct(
		private string $name,
		private string $type,
		private bool $required = false,
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
		return $this->required;
	}

	public function getCommentType(): ?string
	{
		return $this->commentType;
	}

}
