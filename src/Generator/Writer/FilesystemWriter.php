<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Writer;

use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\Printer;
use Nette\Utils\FileSystem;
use WebChemistry\Stimulus\Generator\GeneratedController;

final class FilesystemWriter implements StimulusWriter
{

	private Printer $printer;

	public function __construct(
		private string $baseDir,
		private string $baseNamespace = '',
		private bool $overwrite = true,
		?Printer $printer = null,
	)
	{
		$this->baseDir = rtrim($this->baseDir, '/');
		$this->printer = $printer ?? new Printer();
	}

	public function write(GeneratedController $generated): void
	{
		$className = $generated->getClassName();
		if (str_starts_with($className, $this->baseNamespace)) {
			$className = substr($className, strlen($this->baseNamespace));
		}

		$dir = $this->getDirectoryByClassName($className);
		
		$fileName = $dir . '/' . Helpers::extractShortName($className) . '.php';
 
		if (!$this->overwrite && file_exists($fileName)) {
			return;
		}
		
		FileSystem::createDir($dir);
		FileSystem::write($fileName, $this->printer->printFile($generated->getFile()));
	}

	private function getDirectoryByClassName(string $className): string
	{
		$namespace = Helpers::extractNamespace($className);
		if (!$namespace) {
			return $this->baseDir;
		}

		return $this->baseDir . '/' . strtr($namespace, ['\\' => '/']);
	}

}
