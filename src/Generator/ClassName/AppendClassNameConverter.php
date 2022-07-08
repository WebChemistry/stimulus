<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\ClassName;

final class AppendClassNameConverter implements ClassNameConverter
{

	public function __construct(
		private ClassNameConverter $decorated,
		private string $append,
	)
	{
	}

	/**
	 * @return class-string
	 */
	public function convertToClassName(string $name): string
	{
		return $this->decorated->convertToClassName($name) . $this->append; // @phpstan-ignore-line
	}

}
