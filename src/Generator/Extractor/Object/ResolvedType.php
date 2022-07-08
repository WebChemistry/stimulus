<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

final class ResolvedType
{

	public function __construct(
		private string $type,
		private ?string $commentType = null,
	)
	{
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getCommentType(): ?string
	{
		return $this->commentType;
	}

}
