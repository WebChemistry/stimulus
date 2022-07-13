<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\ClassName;

final class BaseClassNameConverter implements ClassNameConverter
{

	/**
	 * @param string[] $keywords
	 */
	public function __construct(
		private array $keywords = [],
	)
	{
	}

	/**
	 * @param (callable(string): string)|null $converter
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

			$names[] = ucfirst(preg_replace_callback('#-([a-zA-Z])#', function (array $matches): string {
				return ucfirst($matches[1]);
			}, $namespace));
		}

		return implode('\\', $names); // @phpstan-ignore-line
	}

}
