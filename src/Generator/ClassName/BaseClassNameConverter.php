<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\ClassName;

final class BaseClassNameConverter implements ClassNameConverter
{

	/**
	 * @return class-string
	 */
	public function convertToClassName(string $name): string
	{
		$names = [];

		foreach (explode('--', $name) as $namespace) {
			$names[] = ucfirst(preg_replace_callback('#-([a-zA-Z])#', function (array $matches): string {
				return ucfirst($matches[1]);
			}, $namespace));
		}

		return implode('\\', $names); // @phpstan-ignore-line
	}

}
