<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Builder;

use DateTimeInterface;
use Latte\Runtime\Filters;
use Nette\Utils\Json;
use Nette\Utils\Strings;

final class AttributesBuilder
{

	/** @var array<string, string> */
	private array $attributes = [];

	public function addController(string $controller): void
	{
		$this->appendToAttribute('data-controller', self::controllerName($controller));
	}

	public function addTarget(string $controller, string $target): void
	{
		$this->appendToAttribute(
			$this->buildDataAttributeName(self::controllerName($controller), 'target'),
			$target,
		);
	}

	public function addValue(string $controller, string $valueName, string|int|float|bool|array|DateTimeInterface $value): void
	{
		$this->appendToAttribute(
			$this->buildDataAttributeName(self::controllerName($controller), self::camel2Dashed($valueName), 'value'),
			$value,
		);
	}

	public function addParameter(string $controller, string $paramName, string|int|float|bool|array|DateTimeInterface $value): void
	{
		$this->appendToAttribute(
			$this->buildDataAttributeName(self::controllerName($controller), self::camel2Dashed($paramName), 'param'),
			$value,
		);
	}

	public function addClass(string $controller, string $valueName, string $class): void
	{
		$this->appendToAttribute(
			$this->buildDataAttributeName(self::controllerName($controller), self::camel2Dashed($valueName), 'class'),
			$class,
		);
	}

	public function addAction(string $action): void
	{
		$this->appendToAttribute('data-action', $action);
	}

	private function buildDataAttributeName(string ... $names): string
	{
		$attr = 'data-';
		foreach ($names as $name) {
			if ($name) {
				$attr .= $name . '-';
			}
		}

		return substr($attr, 0, -1);
	}

	private function appendToAttribute(string $attribute, mixed $value): void
	{
		if (isset($this->attributes[$attribute])) {
			$this->attributes[$attribute] .= ' ' . $this->convertToString($value);
		} else {
			$this->attributes[$attribute] = $this->convertToString($value);
		}
	}

	private function escapeAttributeValue(string $value): string
	{
		return Filters::escapeHtmlAttr($value);
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

	public function toArray(): array
	{
		return $this->attributes;
	}

	public function toString(): string
	{
		$html = '';
		foreach ($this->attributes as $attribute => $value) {
			$html .= sprintf(' %s="%s"', $attribute, $this->escapeAttributeValue($value));
		}

		return substr($html, 1);
	}

}
