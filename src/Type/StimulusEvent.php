<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Type;

use Stringable;

final class StimulusEvent implements Stringable
{
	
	public function __construct(
		private string $controller,
		private string $eventName,
	)
	{
	}

	public function getController(): string
	{
		return $this->controller;
	}

	public function getEventName(): string
	{
		return $this->eventName;
	}

	public function __toString(): string
	{
		return sprintf('%s:%s', $this->controller, $this->eventName);
	}

}
