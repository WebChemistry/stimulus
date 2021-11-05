<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Builder;

use Nette\Utils\Json;
use Nette\Utils\Strings;

final class AttributesBuilder
{

	/** @var string[] */
	private array $attributes = [];

	public function addControllerAttribute(string $controller): void
	{
		$this->appendDataAttribute('controller', self::controllerName($controller));
	}

	public function addControllerDataAttribute(string $controller, string $name, mixed $value): void
	{
		$key = sprintf('%s-%s', self::controllerName($controller), self::camel2Dashed($name));

		$this->addDataAttribute($key, $value);
	}

	public function addDataAttribute(string $name, mixed $value): void
	{
		$this->attributes[sprintf('data-%s', $name)] = $this->convertToString($value);
	}

	public function appendDataAttribute(string $name, mixed $value): void
	{
		$key = sprintf('data-%s', $name);
		$value = $this->convertToString($value);

		if (isset($this->attributes[$key])) {
			$this->attributes[$key] .= ' ' . $value;
		} else {
			$this->attributes[$key] = $value;
		}
	}

	private function attributeValue(string $string): string
	{
		return '"' . htmlspecialchars($string, ENT_QUOTES) . '"';
	}

	public static function camel2Dashed(string $string): string
	{
		return strtolower(preg_replace('#([a-zA-Z])(?=[A-Z])#', '$1-', Strings::firstLower($string)));
	}

	public static function namespace2Dashed(string $string): string
	{
		return strtr($string, ['/' => '--']);
	}

	public static function controllerName(string $string): string
	{
		return self::namespace2Dashed(self::camel2Dashed($string));
	}

	private function convertToString(mixed $value): string
	{
		if (is_array($value)) {
			return Json::encode($value);
		}

		if ($value === false) {
			return '0';
		}

		return (string) $value;
	}

	public function toArray(): array
	{
		return $this->attributes;
	}

	public function toString(): string
	{
		$html = '';
		foreach ($this->attributes as $attribute => $value) {
			$html .= sprintf(' %s=%s', $attribute, $this->attributeValue($value));
		}

		return $html;
	}

}
