<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\DI;

use Latte\Engine;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\FactoryDefinition;
use WebChemistry\Stimulus\Latte\Extension\StimulusExtension as LatteStimulusExtension;

final class StimulusExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$this->loadLatte($this->getContainerBuilder());
	}

	private function loadLatte(ContainerBuilder $builder): void
	{
		if (version_compare(Engine::VERSION, '3', '<')) { // @phpstan-ignore-line
			$this->loadLatte2($builder);
		} else {
			$this->loadLatte3($builder);
		}
	}

	private function loadLatte2(ContainerBuilder $builder): void
	{
		$serviceName = $builder->getByType(LatteFactory::class);
		if (!$serviceName) {
			return;
		}

		$service = $builder->getDefinition($serviceName);
		assert($service instanceof FactoryDefinition);

		$definition = $builder->getDefinition($this->prefix('latte.macros'));

		$service->getResultDefinition()
			->addSetup(
				'?->onCompile[] = fn (Latte\Engine $engine) => ?->create($engine->getCompiler());',
				['@self', $definition]
			);
	}

	private function loadLatte3(ContainerBuilder $builder): void
	{
		$serviceName = $builder->getByType(LatteFactory::class);
		if (!$serviceName) {
			return;
		}

		$extension = $builder->addDefinition($this->prefix('latte.extension'))
			->setFactory(LatteStimulusExtension::class);

		$factory = $builder->getDefinition($serviceName);
		assert($factory instanceof FactoryDefinition);

		$factory->getResultDefinition()
			->addSetup('addExtension', [$extension]);
	}

}
