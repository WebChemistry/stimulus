<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use Latte\Macros\MacroSet;

final class StimulusMacros
{

	public function create(Compiler $compiler, string $name = 'stimulus'): MacroSet
	{
		$me = new MacroSet($compiler);

		$me->addMacro(
			$name,
			null,
			null,
			sprintf('%%node.line echo " "; echo %s::render(%%node.args);', StimulusMacroService::class)
		);

		return $me;
	}

}
