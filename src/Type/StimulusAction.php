<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Type;

final class StimulusAction implements StimulusType
{

	/** @var array<string, string> */
	private array $options = [];

	private ?string $event = null;

	/**
	 * @param array<string, mixed> $parameters
	 */
	public function __construct(
		private string $controller,
		private string $name,
		private array $parameters,
	)
	{
	}

	public function getController(): string
	{
		return $this->controller;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function event(string $event): self
	{
		$this->event = $event;

		return $this;
	}

	/**
	 * { capture: true }
	 */
	public function capture(?bool $capture = true): self
	{
		$this->setOption('capture', $capture ?: null);

		return $this;
	}

	/**
	 * { once: true }
	 */
	public function once(?bool $once = true): self
	{
		$this->setOption('once', $once ?: null);

		return $this;
	}

	/**
	 * true = { passive: false }
	 * false = { passive: true }
	 */
	public function passive(?bool $passive = true): self
	{
		$this->setOption('passive', $passive);

		return $this;
	}

	/**
	 * Calls .stopPropagation() on the event before invoking the method
	 */
	public function stop(?bool $stop = true): self
	{
		$this->setOption('stop', $stop ?: null);

		return $this;
	}

	/**
	 * Calls .preventDefault() on the event before invoking the method
	 */
	public function prevent(?bool $prevent = true): self
	{
		$this->setOption('prevent', $prevent ?: null);

		return $this;
	}

	/**
	 * Only invokes the method if the event was fired by the element itself
	 */
	public function self(?bool $self = true): self
	{
		$this->setOption('self', $self ?: null);

		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	public function getEvent(): ?string
	{
		return $this->event;
	}

	public function hasOptions(): bool
	{
		return (bool) $this->options;
	}

	public function setOption(string $name, ?bool $value): self
	{
		if ($value === null) {
			unset($this->options[$name]);
		} else {
			$this->options[$name] = $value ? $name : '!' . $name;
		}

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getOptions(): array
	{
		return array_values($this->options);
	}

}
