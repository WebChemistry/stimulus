<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte\Extension\Node;

use Generator;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;
use WebChemistry\Stimulus\Latte\StimulusMacroService;

final class StimulusNode extends StatementNode
{

	public ArrayNode $arguments;

	public static function create(Tag $tag): self
	{
		$tag->expectArguments();

		$node = new self();
		$node->arguments = $tag->parser->parseArguments();

		return $node;
	}

	public function print(PrintContext $context): string
	{
		return $context->format(
			sprintf('echo " " . %s::render(%%args);', StimulusMacroService::class),
			$this->arguments,
		);
	}

	public function &getIterator(): Generator
	{
		yield $this->arguments;
	}

}
