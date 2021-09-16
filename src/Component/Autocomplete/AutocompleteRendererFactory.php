<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Component\Autocomplete;

interface AutocompleteRendererFactory
{

	public function create(): AutocompleteRenderer;

}
