<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use LogicException;
use WebChemistry\Stimulus\Html\StimulusAttributeRenderer;
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
			sprintf('%%node.line echo " "; echo %s::render(%%node.args);', StimulusAttributeRenderer::class)
		);

		return $me;
	}

}
