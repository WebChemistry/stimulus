<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Html;

use WebChemistry\Stimulus\Builder\AttributesBuilder;
use WebChemistry\Stimulus\Object\Action;
use WebChemistry\Stimulus\Object\Controller;
use WebChemistry\Stimulus\Object\Target;

final class StimulusHtml
{

	/** @var Controller[] */
	private array $controllers = [];

	/** @var Action[] */
	private array $actions = [];

	/** @var Target[] */
	private array $targets = [];

	public function addController(Controller $controller): self
	{
		$this->controllers[] = $controller;

		return $this;
	}

	public function addTarget(Target $target): self
	{
		$this->targets[] = $target;

		return $this;
	}

	public function addAction(Action $action): self
	{
		$this->actions[] = $action;

		return $this;
	}

	public function render(): string
	{
		$builder = new AttributesBuilder();

		foreach ($this->targets as $target) {
			$builder->addControllerDataAttribute($target->controller, 'target', $target->name);
		}

		foreach ($this->controllers as $controller) {
			$builder->addControllerAttribute($controller->controller);
			foreach ($controller->values as $name => $value) {
				$builder->addControllerDataAttribute($controller->controller, $name . 'Value', $value);
			}

			foreach ($controller->classes as $name => $value) {
				$builder->addControllerDataAttribute($controller->controller, $name . 'Class', $value);
			}
		}

		if ($this->actions) {
			$builder->addDataAttribute('data-action', implode(' ', $this->actions));
		}

		return $builder->toString();
	}

}
