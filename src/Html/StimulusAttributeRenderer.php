<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Html;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use WebChemistry\Stimulus\Builder\AttributesBuilder;

final class StimulusAttributeRenderer
{

	public static function control(BaseControl $control, StimulusAttributeRenderable ...$fragments): BaseControl
	{
		foreach (self::toArray(...$fragments) as $name => $value) {
			$control->setHtmlAttribute($name, $value);
		}

		return $control;
	}

	public static function form(Form $form, StimulusAttributeRenderable ...$fragments): Form
	{
		foreach (self::toArray(...$fragments) as $name => $value) {
			$form->setHtmlAttribute($name, $value);
		}

		return $form;
	}

	public static function render(StimulusAttributeRenderable ... $fragments): string
	{
		$builder = new AttributesBuilder();

		foreach ($fragments as $fragment) {
			$fragment->renderAttribute($builder);
		}

		return $builder->toString();
	}

	/**
	 * @return array<string, string>
	 */
	public static function toArray(StimulusAttributeRenderable ... $fragments): array
	{
		$builder = new AttributesBuilder();

		foreach ($fragments as $fragment) {
			$fragment->renderAttribute($builder);
		}

		return $builder->toArray();
	}

}
