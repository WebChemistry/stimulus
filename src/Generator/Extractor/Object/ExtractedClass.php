<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

class ExtractedClass implements ExtractedControllerParameter
{

	public function __construct(
		private string $name,
		private bool $required = false,
		private bool $multiple = false,
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

	public function isMultiple(): bool
	{
		return $this->multiple;
	}

	public function withRequired(bool $required): self
	{
		$self = clone $this;
		$self->required = $required;

		return $self;
	}

	public function getType(): string
	{
		return $this->multiple ? 'string|array' : 'string';
	}

	public function getCommentType(): ?string
	{
		return $this->multiple ? 'string|string[]' : null;
	}

}
