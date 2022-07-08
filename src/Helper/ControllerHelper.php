<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Helper;

final class ControllerHelper
{

	/**
	 * @param string[] $names
	 */
	public static function argumentMap(array $names, string $appendToName = '', int $indent = 0): string
	{
		if (!$names) {
			return '[]';
		}

		$code = "[\n";

		foreach ($names as $name) {
			$code .= str_repeat("\t", $indent) . sprintf("'%s' => $%s,\n", $name . $appendToName, $name);
		}

		return substr($code, 0, -1) . "\n]";
	}

}
