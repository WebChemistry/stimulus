<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Writer;

use WebChemistry\Stimulus\Generator\GeneratedController;

interface StimulusWriter
{

	public function write(GeneratedController $generated): void;

}
