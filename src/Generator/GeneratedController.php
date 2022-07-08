<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator;

use Nette\PhpGenerator\PhpFile;
use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedController;

final class GeneratedController
{

	/**
	 * @param class-string $className
	 */
	public function __construct(
		private PhpFile $file,
		private ExtractedController $controller,
		private string $className,
	)
	{
	}

	public function getFile(): PhpFile
	{
		return $this->file;
	}

	public function getClassName(): string
	{
		return $this->className;
	}

	public function getController(): ExtractedController
	{
		return $this->controller;
	}

}
