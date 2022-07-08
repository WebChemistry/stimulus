<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\ClassName;

interface ClassNameConverter
{

	/**
	 * @return class-string
	 */
	public function convertToClassName(string $name): string;

}
