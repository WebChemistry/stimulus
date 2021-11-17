<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Html;

use WebChemistry\Stimulus\Builder\AttributesBuilder;

final class StimulusTargetAttribute implements StimulusAttributeRenderable
{

	public function __construct(
		private string $controller,
		private string $target,
	)
	{
	}

	public function renderAttribute(AttributesBuilder $builder): void
	{
		$builder->addTarget($this->controller, $this->target);
	}

}
