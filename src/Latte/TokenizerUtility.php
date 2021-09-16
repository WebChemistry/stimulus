<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Latte;

use Latte\MacroTokens;
use Latte\PhpWriter;

final class TokenizerUtility
{

	public static function skipUntil(MacroTokens $tokenizer, string|int ... $args): void
	{
		while ($tokenizer->isNext(...$args)) {
			$tokenizer->nextToken();
		}
	}

	public static function skipWhitespaces(MacroTokens $tokenizer): void
	{
		self::skipUntil($tokenizer, MacroTokens::T_WHITESPACE);
	}

	public static function nextUntilSameDepth(MacroTokens $tokens, string|int... $args): array
	{
		$depth = $tokens->depth;
		$res = [];
		do {
			$res = array_merge($res, $tokens->nextUntil(...$args));
			if ($tokens->depth === $depth) {
				return $res;
			}

			$res[] = $tokens->nextToken();
		} while (true);
	}

	public static function formatArray(PhpWriter $writer, MacroTokens $tokens): ?string
	{
		if (!$tokens->isNext('[')) {
			return null;
		}

		$tokens->nextToken();
		$parsed = new MacroTokens(self::nextUntilSameDepth($tokens, ']'));

		$tokens->nextToken(); // skip to ]

		self::skipWhitespaces($tokens);
		self::skipUntil($tokens, ',');
		self::skipWhitespaces($tokens);

		return $writer->formatArray($parsed);
	}

	public static function isCurrentWord(MacroTokens $tokenizer): bool
	{
		return $tokenizer->isCurrent(MacroTokens::T_VARIABLE, MacroTokens::T_STRING, MacroTokens::T_SYMBOL);
	}

	public static function isNextWord(MacroTokens $tokenizer): bool
	{
		return $tokenizer->isNext(MacroTokens::T_VARIABLE, MacroTokens::T_STRING, MacroTokens::T_SYMBOL);
	}

}
