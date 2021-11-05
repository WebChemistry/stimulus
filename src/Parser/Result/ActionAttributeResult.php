<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Parser\Result;

final class ActionAttributeResult
{

	public function __construct(
		public string $attribute = '',
		public string $controller = '',
	)
	{
	}

}
