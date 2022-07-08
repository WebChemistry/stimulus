<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Renderer;

use WebChemistry\Stimulus\Renderer\Html\HtmlBuilder;
use WebChemistry\Stimulus\Type\StimulusAction;
use WebChemistry\Stimulus\Type\StimulusController;
use WebChemistry\Stimulus\Type\StimulusTarget;

final class HtmlRenderer
{

	public static function render(StimulusAction|StimulusTarget|StimulusController ... $types): string
	{
		$builder = new HtmlBuilder();

		foreach ($types as $type) {
			if ($type instanceof StimulusAction) {
				$builder->appendAttribute('data-action', self::renderAction($type));

				foreach ($type->getParameters() as $name => $value) {
					if ($value === null) {
						continue;
					}

					$builder->appendAttribute(
						sprintf('data-%s-%s', $type->getController(), $builder->camel2Dashed($name)),
						$value,
					);
				}
			} elseif ($type instanceof StimulusTarget) {
				$name = $type->getName();

				$builder->appendAttribute(
					sprintf('data-%s-target', $type->getController()),
					str_ends_with($name, 'Target') ? substr($name, 0, -6) : $name,
				);
			} else if ($type instanceof StimulusController) {
				$name = $type->getName();

				$builder->appendAttribute('data-controller', $name);

				foreach ($type->getValues() as $name => $value) {
					$builder->appendAttribute(
						sprintf('data-%s-%s', $type->getName(), $builder->camel2Dashed($name)),
						$value,
					);
				}

				foreach ($type->getClasses() as $name => $value) {
					$builder->appendAttribute(
						sprintf('data-%s-%s', $type->getName(), $builder->camel2Dashed($name)),
						$value,
					);
				}
			}
		}

		return $builder->toString();
	}

	private static function renderAction(StimulusAction $type): string
	{
		$build = ($event = $type->getEvent()) ? sprintf('%s->', $event) : '';

		$build .= sprintf('%s#%s', $type->getController(), $type->getName());

		if ($type->hasOptions()) {
			$build .= ':' . implode(':', $type->getOptions());
		}

		return $build;
	}

}
