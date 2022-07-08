<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator;

interface StimulusControllerGenerator
{

	/**
	 * @return GeneratedController[]
	 */
	public function generate(): array;

}
