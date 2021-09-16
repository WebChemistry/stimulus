<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Component\Autocomplete;

use Nette\Bridges\ApplicationLatte\LatteFactory;

final class AutocompleteRenderer
{

	/** @var AutocompleteItem[] */
	private array $items = [];

	private int $itemCount;

	public function __construct(
		private LatteFactory $latteFactory,
	)
	{
	}

	public function addItem(AutocompleteItem $item): void
	{
		$this->items[] = $item;
	}

	public function setItemCount(int $itemCount): void
	{
		$this->itemCount = $itemCount;
	}

	public function render(): void
	{
		$latte = $this->latteFactory->create();

		$latte->render(__DIR__ . '/templates/autocomplete.latte', [
			'items' => $this->items,
			'count' => count($this->items),
			'itemCount' => $this->itemCount ?? null,
		]);
	}

	public function toString(): string
	{
		$latte = $this->latteFactory->create();

		return $latte->renderToString(__DIR__ . '/templates/autocomplete.latte', [
			'items' => $this->items,
			'count' => count($this->items),
			'itemCount' => $this->itemCount ?? null,
		]);
	}

}
