<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Html;

use WebChemistry\Stimulus\Builder\AttributesBuilder;

interface StimulusAttributeRenderable
{

	public function renderAttribute(AttributesBuilder $builder): void;

}
