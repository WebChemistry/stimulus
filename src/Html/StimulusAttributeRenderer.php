<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Html;

use WebChemistry\Stimulus\Builder\AttributesBuilder;

final class StimulusAttributeRenderer
{

	public static function render(StimulusAttributeRenderable ... $fragments): string
	{
		$builder = new AttributesBuilder();

		foreach ($fragments as $fragment) {
			$fragment->renderAttribute($builder);
		}

		return $builder->toString();
	}

	public static function toArray(StimulusAttributeRenderable ... $fragments): array
	{
		$builder = new AttributesBuilder();

		foreach ($fragments as $fragment) {
			$fragment->renderAttribute($builder);
		}

		return $builder->toArray();
	}

}
