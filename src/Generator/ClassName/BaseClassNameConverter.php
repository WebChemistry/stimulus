<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\ClassName;

use Nette\Utils\Strings;

final class BaseClassNameConverter implements ClassNameConverter
{

	/** @var (callable(string): string)|null */
	private $classNameDecorator;

	/**
	 * @param string[] $keywords
	 * @param (callable(string): string)|null $classNameDecorator
	 */
	public function __construct(
		private array $keywords = [],
		callable $classNameDecorator = null,
	)
	{
		$this->classNameDecorator =$classNameDecorator;
	}

	/**
	 * @return class-string
	 */
	public function convertToClassName(string $name): string
	{
		$names = [];

		foreach (explode('--', $name) as $namespace) {
			if (isset($this->keywords[$namespace])) {
				$names[] = $this->keywords[$namespace];

				continue;
			}

			$names[] = ucfirst(Strings::replace($namespace, '#-([a-zA-Z])#', function (array $matches): string {
				return ucfirst($matches[1]);
			}));
		}

		if ($this->classNameDecorator && ($lastKey = array_key_last($names)) !== null) {
			$names[$lastKey] = ($this->classNameDecorator)($names[$lastKey]);
		}

		return implode('\\', $names); // @phpstan-ignore-line
	}

}
