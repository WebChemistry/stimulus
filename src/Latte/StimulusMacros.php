<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use Latte\Compiler;
use Latte\Macros\MacroSet;
use WebChemistry\Stimulus\Html\StimulusHtml;
use WebChemistry\Stimulus\Html\StimulusHtmlRenderer;

final class StimulusMacros
{

	public function create(Compiler $compiler): MacroSet
	{
		$me = new MacroSet($compiler);

		$me->addMacro(
			'stimulus',
			null,
			null,
			sprintf('%%node.line echo " "; echo %s::render(%%node.args);', StimulusMacroService::class)
		);

		return $me;
	}

}
