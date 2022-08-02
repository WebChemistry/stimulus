<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte\Extension\Node;

use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;
use WebChemistry\Stimulus\Latte\StimulusMacroService;

final class StimulusNode extends StatementNode
{

	private ArrayNode $arguments;

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

}
