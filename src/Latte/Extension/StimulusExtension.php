<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte\Extension;

use Latte\Extension;
use WebChemistry\Stimulus\Latte\Extension\Node\StimulusNode;

final class StimulusExtension extends Extension
{

	/**
	 * @return array<string, callable>
	 */
	public function getTags(): array
	{
		return [
			'n:stimulus' => [StimulusNode::class, 'create'],
		];
	}

}
