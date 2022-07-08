<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Generator\Extractor;

use WebChemistry\Stimulus\Generator\Extractor\Object\ExtractedController;

interface StimulusExtractor
{

	/**
	 * @return ExtractedController[]
	 */
	public function getExtractedControllers(): array;

}
