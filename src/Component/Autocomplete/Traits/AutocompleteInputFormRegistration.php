<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Component\Autocomplete\Traits;

use WebChemistry\Stimulus\Component\Autocomplete\AutocompleteInput;

trait AutocompleteInputFormRegistration
{

	public function addAutocomplete(string $name, ?string $label = null, ?string $url = null): AutocompleteInput
	{
		$this[$name] = $control = new AutocompleteInput($label);

		if ($url) {
			$control->setUrl($url);
		}

		return $control;
	}

}
