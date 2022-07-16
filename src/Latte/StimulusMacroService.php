<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use WebChemistry\Stimulus\Renderer\HtmlRenderer;
use WebChemistry\Stimulus\Type\StimulusType;

final class StimulusMacroService
{

	/**
	 * @param StimulusType|StimulusType[]|null ...$fragments
	 * @return string
	 */
	public static function render(StimulusType|array|null ... $fragments): string
	{
		return HtmlRenderer::render(...$fragments);
	}

}
