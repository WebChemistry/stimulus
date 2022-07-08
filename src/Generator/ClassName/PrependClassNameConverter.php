<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\ClassName;

final class PrependClassNameConverter implements ClassNameConverter
{

	public function __construct(
		private string $prepend,
		private ClassNameConverter $decorated,
	)
	{
	}

	/**
	 * @return class-string
	 */
	public function convertToClassName(string $name): string
	{
		return $this->prepend . $this->decorated->convertToClassName($name); // @phpstan-ignore-line
	}

}
