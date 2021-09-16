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

		$me->addMacro($this->controllerName, [$this, 'macroController'], 'array_pop($this->global->stimulusControllerStack);');
		$me->addMacro($this->targetName, null, null, [$this, 'macroTarget']);
		$me->addMacro($this->targetName . 's', null, null, [$this, 'macroTargets']);
		$me->addMacro($this->actionName, null, null, [$this, 'macroAction']);

		foreach ($this->templates as $name => $template) {
			$me->addMacro($name, null, null, $this->stimulusTemplate($template));
		}

		return $me;
	}

	public function macroTarget(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write(
			'%node.line ' .
			sprintf('$_tmp = %s::applyDefaultController(%%node.array, end($this->global->stimulusControllerStack));', StimulusMacroService::class) .
			sprintf('echo %s::makeTarget(...$_tmp);', StimulusMacroService::class)
		);
	}

	public function macroTargets(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write(
			'%node.line ' .
			sprintf('$_tmp = %s::applyDefaultControllerToArray(%%node.array, end($this->global->stimulusControllerStack));', StimulusMacroService::class) .
			'foreach ($_tmp as $_p) {' . "\n" .
			sprintf('echo %s::makeTarget(...$_p);', StimulusMacroService::class) . "\n" .
			'}' . "\n"
		);
	}

	public function macroAction(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write(
			'$_tmp = %node.array;' .
			'$_tmp["controller"] ??= end($this->global->stimulusControllerStack);' .
			sprintf('echo %s::makeAction(...$_tmp);', StimulusMacroService::class)
		);
	}

	public function macroController(MacroNode $node, PhpWriter $writer): string
	{
		if ($node->prefix === null) {
			throw new LogicException('Macro stimulus can be used only as n:stimulus');
		}

		$controllers = [];
		$tokenizer = $node->tokenizer;
		while ($tokenizer->isNext()) {
			TokenizerUtility::skipWhitespaces($tokenizer);

			$name = $writer->formatWord($tokenizer->fetchWord());
			TokenizerUtility::skipWhitespaces($tokenizer);
			$array = TokenizerUtility::formatArray($writer, $tokenizer);

			$controllers[$name] = sprintf('%s => %s', $name, $array ?? 'null');
		}

		$raw = '[' . implode(', ', $controllers) . ']';

		$controller = array_key_first($controllers) ?? 'null';
		$node->attrCode = $writer->write(
			sprintf('<?php echo %s::makeControllers(%%raw); ?>', StimulusMacroService::class),
			$raw
		);

		return $writer->write('$this->global->stimulusControllerStack[] = %raw;', $controller);
	}

	private function stimulusTemplate(string $template): callable
	{
		return fn (MacroNode $node, PhpWriter $writer): string => $writer->write(
			sprintf('echo %s::makeControllers(%s);', StimulusMacroService::class, $template)
		);
	}

}
