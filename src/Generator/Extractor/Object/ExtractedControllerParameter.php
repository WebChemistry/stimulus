<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

interface ExtractedControllerParameter
{

	public function getName(): string;

	public function getType(): string;

	public function isRequired(): bool;

	public function getCommentType(): ?string;

}
