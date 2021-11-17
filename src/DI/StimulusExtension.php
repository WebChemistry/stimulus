<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\DI;

use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use WebChemistry\Stimulus\Html\StimulusActionAttribute;
use WebChemistry\Stimulus\Html\StimulusControllerAttribute;
use WebChemistry\Stimulus\Html\StimulusTargetAttribute;
use WebChemistry\Stimulus\Latte\StimulusMacros;

final class StimulusExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('macros'))
			->setFactory(StimulusMacros::class);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$name = $builder->getByType(LatteFactory::class);
		if (!$name) {
			return;
		}

		$service = $builder->getDefinition($name);
		assert($service instanceof FactoryDefinition);

		$definition = $builder->getDefinition($this->prefix('macros'));

		$service->getResultDefinition()
			->addSetup(
				'?->onCompile[] = fn (Latte\Engine $engine) => ?->create($engine->getCompiler());',
				['@self', $definition]
			)
			->addSetup(
				sprintf(
					'?->addFunction("stimulusController", fn (...$args) => new %s(...$args))',
					StimulusControllerAttribute::class
				),
				['@self']
			)
			->addSetup(
				sprintf(
					'?->addFunction("stimulusAction", fn (...$args) => new %s(...$args))',
					StimulusActionAttribute::class
				),
				['@self']
			)
			->addSetup(
				sprintf(
					'?->addFunction("stimulusTarget", fn (...$args) => new %s(...$args))',
					StimulusTargetAttribute::class
				),
				['@self']
			);
	}

}
