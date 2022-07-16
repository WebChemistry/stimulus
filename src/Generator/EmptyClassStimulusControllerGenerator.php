<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator;

use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpFile;
use Utilitte\Asserts\TypeAssert;
use WebChemistry\Stimulus\Generator\ClassName\ClassNameConverter;
use WebChemistry\Stimulus\Generator\Extractor\StimulusExtractor;

final class EmptyClassStimulusControllerGenerator implements StimulusControllerGenerator
{

	public function __construct(
		private StimulusExtractor $extractor,
		private ClassNameConverter $classNameConverter,
		private ClassNameConverter $originalClassNameConverter,
	)
	{
	}

	/**
	 * @return GeneratedController[]
	 */
	public function generate(): array
	{
		$generated = [];

		foreach ($this->extractor->getExtractedControllers() as $controller) {
			$className = $this->classNameConverter->convertToClassName($controller->getName());
			$originalClassName = $this->originalClassNameConverter->convertToClassName($controller->getName());

			$file = new PhpFile();
			$file->setStrictTypes();

			$namespace = $file->addNamespace(Helpers::extractNamespace($className));
			$namespace->addUse($originalClassName, 'ParentController');

			$class = $namespace->addClass(Helpers::extractShortName($className))
				->setExtends($originalClassName)
				->setFinal();

			if (($deprecated = $controller->getContext()['deprecated'] ?? null) !== null) {
				$class->addComment(trim(sprintf('@deprecated %s', TypeAssert::string($deprecated))));
			}

			$generated[] = new GeneratedController($file, $controller, $className);
		}

		return $generated;
	}

}
