<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use WebChemistry\Stimulus\Html\StimulusAttributeRenderable;
use WebChemistry\Stimulus\Html\StimulusAttributeRenderer;
use WebChemistry\Stimulus\Renderer\HtmlRenderer;
use WebChemistry\Stimulus\Type\StimulusAction;
use WebChemistry\Stimulus\Type\StimulusController;
use WebChemistry\Stimulus\Type\StimulusTarget;

final class StimulusMacroService
{

	public static function render(StimulusAttributeRenderable|StimulusAction|StimulusTarget|StimulusController ... $fragments): string
	{
		if (($fragments[0] ?? null) instanceof StimulusAttributeRenderable) { // backward compatability
			return StimulusAttributeRenderer::render(...$fragments); // @phpstan-ignore-line
		}

		return HtmlRenderer::render(...$fragments); // @phpstan-ignore-line
	}

}
