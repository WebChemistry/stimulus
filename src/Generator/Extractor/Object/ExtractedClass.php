<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

class ExtractedClass implements ExtractedControllerParameter
{

	public function __construct(
		private string $name,
		private bool $required = false,
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

	public function withRequired(bool $required): self
	{
		$self = clone $this;
		$self->required = $required;

		return $self;
	}

	public function getType(): string
	{
		return 'string';
	}

	public function getCommentType(): ?string
	{
		return null;
	}

}
