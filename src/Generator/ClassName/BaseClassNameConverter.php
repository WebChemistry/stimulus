<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\ClassName;

use Nette\Utils\Strings;

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

		return implode('\\', $names); // @phpstan-ignore-line
	}

}
