<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Parser;

use LogicException;
use Nette\Utils\Arrays;
use WebChemistry\Stimulus\Builder\AttributesBuilder;
use WebChemistry\Stimulus\Parser\Result\ActionAttributeResult;

final class ActionAttributeParser
{

	public static function parse(string $action, array $controllers): ActionAttributeResult
	{
		if (!$controllers) {
			throw new LogicException('Controllers stack is empty.');
		}

		$result = new ActionAttributeResult();

		$length = strlen($action);
		$pos = 0;
		$applyDefaultController = true;

		while ($length > $pos) {
			$char = $action[$pos];

			if ($char === '-' && ($action[$pos + 1] ?? '') === '>') {
				// found ->
				$applyDefaultController = false;
			} else if ($char === '#') {
				$applyDefaultController = false;
			} else if ($char === '?') {
				if (($action[$pos + 1] ?? '') === '?') {
					$result->attribute .= '?';
					$pos += 2;

					continue;
				}

				$applyDefaultController = false;

				$pos = self::replaceMark($controllers, $result, $action, $pos, $length);

				continue;
			}

			$result->attribute .= $char;

			$pos++;
		}

		if ($applyDefaultController) {
			$controller = Arrays::first($controllers);
			$result->controller = $controller;
			$result->attribute = sprintf('%s#%s', $controller, $action);
		}

		return $result;
	}

	private static function replaceMark(array $controllers, ActionAttributeResult $result, string $action, int $pos, int $length): int
	{
		$pos++;

		$number = '';
		while ($length > $pos) {
			$char = $action[$pos];
			if (is_numeric($char)) {
				$number .= $char;
			} else {
				break;
			}

			$pos++;
		}

		// 0 and ''
		if (!$number) {
			$number = 0;
		}

		if (!isset($controllers[$number])) {
			throw new LogicException(sprintf('Controller number %d does not exist in stack.', (int) $number));
		}

		$result->controller = $controllers[$number];
		$result->attribute .= $controllers[$number];

		return $pos;
	}

}
