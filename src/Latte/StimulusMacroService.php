<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use WebChemistry\Stimulus\Html\StimulusAttributeRenderable;
use WebChemistry\Stimulus\Html\StimulusAttributeRenderer;
use WebChemistry\Stimulus\Renderer\HtmlRenderer;
use WebChemistry\Stimulus\Type\StimulusType;

final class StimulusMacroService
{

	/**
	 * @param StimulusAttributeRenderable|StimulusType|StimulusType[]|null ...$fragments
	 * @return string
	 */
	public static function render(StimulusAttributeRenderable|StimulusType|array|null ... $fragments): string
	{
		if (($fragments[0] ?? null) instanceof StimulusAttributeRenderable) { // backward compatability
			return StimulusAttributeRenderer::render(...$fragments); // @phpstan-ignore-line
		}

		return HtmlRenderer::render(...$fragments); // @phpstan-ignore-line
	}

}
