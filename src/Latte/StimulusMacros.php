<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\MacroTokens;
use Latte\PhpWriter;
use LogicException;

final class StimulusMacros
{

	private const PRIVATE_VAR = '$this->global->ʟ_controllers';
	private const LOCAL_VAR = '$ʟ_controllers';

	public function __construct(
		private string $controllerName = 's',
		private string $actionName = 's-action',
		private string $targetName = 's-target',
		private array $templates = [],
	)
	{
	}

	public function create(Compiler $compiler): MacroSet
	{
		$me = new MacroSet($compiler);

		$me->addMacro($this->controllerName, [$this, 'macroController'], sprintf('array_pop(%s);', self::PRIVATE_VAR));
		$me->addMacro($this->targetName, null, null, [$this, 'macroTarget']);
		$me->addMacro($this->actionName, null, null, [$this, 'macroAction']);

		foreach ($this->templates as $name => $template) {
			$me->addMacro($name, null, null, $this->stimulusTemplate($template));
		}

		return $me;
	}

	public function macroTarget(MacroNode $node, PhpWriter $writer): string
	{
		$words = [];
		while ($node->tokenizer->isNext()) {
			$words[] = $node->tokenizer->joinUntil(',');

			$node->tokenizer->nextValue();
		}

		$words = array_map('trim', $words);

		return $writer->write(
			'%node.line ' .
			sprintf('echo %s::makeTargets(%s, %%var);', StimulusMacroService::class, self::PRIVATE_VAR),
			$words
		);
	}

	public function macroAction(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write(
			'%node.line' .
			sprintf('echo %s::makeActions(%s, %%raw);', StimulusMacroService::class, self::PRIVATE_VAR),
			$this->formatStringFollowingWithOptionalArray($node, $writer),
		);
	}

	public function macroController(MacroNode $node, PhpWriter $writer): string
	{
		if ($node->prefix === null) {
			throw new LogicException('Macro stimulus can be used only as n:stimulus');
		}

		$node->attrCode =
			sprintf('<?php echo %s::makeControllers(%s); ?>', StimulusMacroService::class, self::LOCAL_VAR);

		return $writer->write(
			'%node.line' .
			sprintf('%s::pushToStack(%s, %s = %%raw);', StimulusMacroService::class, self::PRIVATE_VAR, self::LOCAL_VAR),
			$this->formatStringFollowingWithOptionalArray($node, $writer),
		);
	}

	private function stimulusTemplate(string $template): callable
	{
		return fn (MacroNode $node, PhpWriter $writer): string => $writer->write(
			sprintf('echo %s::makeControllers(%s);', StimulusMacroService::class, $template)
		);
	}

	private function formatStringFollowingWithOptionalArray(MacroNode $node, PhpWriter $writer): string
	{
		$arguments = [];
		$tokenizer = $node->tokenizer;
		while ($tokenizer->isNext()) {
			TokenizerUtility::skipWhitespaces($tokenizer);

			$name = $writer->formatWord($tokenizer->fetchWord());
			TokenizerUtility::skipWhitespaces($tokenizer);
			$array = TokenizerUtility::formatArray($writer, $tokenizer);

			$arguments[$name] = sprintf('%s => %s', $name, $array ?? 'null');
		}

		return '[' . implode(', ', $arguments) . ']';
	}

}
