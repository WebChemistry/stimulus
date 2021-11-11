<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\DI;

use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use WebChemistry\Stimulus\Latte\StimulusMacros;
use WebChemistry\Stimulus\Latte\StimulusMacroService;

final class StimulusExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'names' => Expect::structure([
				'controller' => Expect::string('s'),
				'action' => Expect::string('s-action'),
				'target' => Expect::string('s-target'),
			]),
			'templates' => Expect::arrayOf(Expect::string()),
		]);
	}

	public function loadConfiguration(): void
	{
		/** @var stdClass $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('macros'))
			->setFactory(StimulusMacros::class, [
				$config->names->controller,
				$config->names->action,
				$config->names->target,
				$config->templates,
			]);
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
			->addSetup(sprintf('?->addFunction("sController", ["%s", "createController"])', StimulusMacroService::class), ['@self'])
			->addSetup(sprintf('?->addFunction("sTarget", ["%s", "createTarget"])', StimulusMacroService::class), ['@self'])
			->addSetup(sprintf('?->addFunction("sAction", ["%s", "createAction"])', StimulusMacroService::class), ['@self']);
	}

}
