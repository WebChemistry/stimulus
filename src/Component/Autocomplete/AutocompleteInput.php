<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Component\Autocomplete;

use LogicException;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

final class AutocompleteInput extends TextInput
{

	private string $url;

	private Html $wrapper;

	private Html $results;

	private Html $controlPart;

	public function __construct($label = null, int $maxLength = null)
	{
		parent::__construct($label, $maxLength);

		$this->setHtmlAttribute('autocomplete', false);
	}

	public function getResultsPart(): Html
	{
		if (!isset($this->results)) {
			$this->results = Html::el('div')
				->setAttribute('data-autocomplete-target', 'results');
		}

		return $this->results;
	}

	public function getWrapperPart(): Html
	{
		if (!isset($this->wrapper)) {
			if (!isset($this->url)) {
				throw new LogicException('Autocomplete url must be set.');
			}

			$this->wrapper = Html::el('div')
				->setAttribute('data-controller', 'autocomplete')
				->setAttribute('data-autocomplete-url-value', $this->url);
		}

		return $this->wrapper;
	}

	public function getControlPart(): ?Html
	{
		if (!isset($this->controlPart)) {
			$this->controlPart = parent::getControl();
			$this->controlPart->setAttribute('data-autocomplete-target', 'input');
		}

		return $this->controlPart;
	}

	public function getControl(): Html
	{
		return $this->getWrapperPart()
			->insert(null, $this->getControlPart())
			->insert(null, $this->getResultsPart());
	}

	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

}
