<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Renderer\Html;

use DateTimeInterface;
use Nette\Utils\Json;
use Nette\Utils\Strings;

final class HtmlBuilder
{

	/** @var array<string, string> */
	private array $attributes = [];

	public function toString(): string
	{
		$html = '';

		foreach ($this->attributes as $attribute => $value) {
			$html .= sprintf(' %s="%s"', $attribute, $this->escapeAttr($value));
		}

		return substr($html, 1);
	}

	public function appendAttribute(string $attribute, mixed $value): void
	{
		if (isset($this->attributes[$attribute])) {
			$this->attributes[$attribute] .= ' ' . $this->convertToString($value);
		} else {
			$this->attributes[$attribute] = $this->convertToString($value);
		}
	}

	public function camel2Dashed(string $string): string
	{
		return strtolower(preg_replace('#([a-zA-Z])(?=[A-Z])#', '$1-', Strings::firstLower($string)));
	}

	private function convertToString(mixed $value): string
	{
		if (is_bool($value)) {
			return $value ? '1' : '0';
		}

		if (is_string($value)) {
			return $value;
		}

		if ($value instanceof DateTimeInterface) {
			return $value->format(DateTimeInterface::ATOM);
		}

		return Json::encode($value);
	}

	private function escapeAttr(string $value): string
	{
		if (str_contains($value, '`') && strpbrk($value, ' <>"\'') === false) {
			$value .= ' ';
		}

		$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8');
		return str_replace('{', '&#123;', $value);
	}

}
