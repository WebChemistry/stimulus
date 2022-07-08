<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Type;

final class StimulusAction
{

	private bool $capture = false;

	private bool $once = false;

	private ?bool $passive = null;

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

	public function capture(bool $capture = true): self
	{
		$this->capture = $capture;

		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	public function isCapture(): bool
	{
		return $this->capture;
	}

	public function getEvent(): ?string
	{
		return $this->event;
	}

	public function hasOptions(): bool
	{
		return $this->capture || $this->once || $this->passive !== null;
	}

	/**
	 * @return string[]
	 */
	public function getOptions(): array
	{
		return array_filter([
			$this->capture ? 'capture' : null,
			$this->once ? 'once' : null,
			$this->passive === null ? null : ($this->passive ? '' : '!') . 'passive',
		]);
	}

}
