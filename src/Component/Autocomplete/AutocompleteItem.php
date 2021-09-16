<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Component\Autocomplete;

use Nette\Utils\Html;

final class AutocompleteItem
{

	public function __construct(
		public int|string $id,
		public string|Html $caption,
	)
	{
	}

}
