<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Object;

final class Action
{

	public function __construct(
		public string $action,
	)
	{
	}

	public function __toString(): string
	{
		return $this->action;
	}

}
