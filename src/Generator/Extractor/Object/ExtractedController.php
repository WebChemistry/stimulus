<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor\Object;

class ExtractedController
{

	/**
	 * @param ExtractedValue[] $values
	 * @param ExtractedAction[] $actions
	 * @param ExtractedTarget[] $targets
	 * @param ExtractedClass[] $classes
	 * @param mixed[] $context
	 */
	public function __construct(
		private string $name,
		private array $values,
		private array $actions,
		private array $targets,
		private array $classes,
		private array $context = [],
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return ExtractedValue[]
	 */
	public function getValues(): array
	{
		return $this->values;
	}

	/**
	 * @return ExtractedAction[]
	 */
	public function getActions(): array
	{
		return $this->actions;
	}

	/**
	 * @return ExtractedTarget[]
	 */
	public function getTargets(): array
	{
		return $this->targets;
	}

	/**
	 * @return ExtractedClass[]
	 */
	public function getClasses(): array
	{
		return $this->classes;
	}

	/**
	 * @return mixed[]
	 */
	public function getContext(): array
	{
		return $this->context;
	}

}
