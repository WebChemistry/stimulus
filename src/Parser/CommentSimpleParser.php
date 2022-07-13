<?php declare(strict_types = 1);

namespace WebChemistry\Stimulus\Parser;

use Nette\Utils\Strings;

final class CommentSimpleParser
{

	private int $pos = 0;

	/**
	 * @param string[] $annotations
	 */
	public function __construct(
		private string $string,
		private array $annotations,
	)
	{
	}

	/**
	 * @param string[] $annotations
	 * @return array{string, string[]}[]
	 */
	public static function parse(string $comment, array $annotations): array
	{
		$comment = Strings::replace(trim($comment, '/'), '#^\s*\*+#m', '');

		return (new self($comment, $annotations))->process();
	}

	/**
	 * @return array{string, string[]}[]
	 */
	private function process(): array
	{
		$annotations = [];

		while (true) {
			$forehead = $this->forehead();

			if ($forehead === null) {
				break;
			}

			if ($forehead === '@') {
				$this->next();

				$this->consumeWhitespaces();
				$annotation = $this->consumeSimpleValue();

				if (!in_array($annotation, $this->annotations, true)) {
					continue;
				}

				$this->consumeWhitespaces();

				$values = [];
				while (true) {
					$this->consumeWhitespaces();
					$value = $this->consumeValue();

					if (!$value) {
						break;
					}

					$values[] = $value;
				}

				$annotations[] = [$annotation, $values];
			} else {
				$this->next();
			}
		}

		return $annotations;
	}

	private function next(): ?string
	{
		return $this->string[$this->pos++] ?? null;
	}

	private function current(): ?string
	{
		return $this->string[$this->pos] ?? null;
	}

	private function forehead(): ?string
	{
		return $this->string[$this->pos + 1] ?? null;
	}

	private function consumeWhitespaces(): void
	{
		while (($forehead = $this->forehead()) && ctype_space($forehead)) {
			$this->next();
		}
	}

	private function consumeValue(): string
	{
		$value = '';

		while (($char = $this->forehead())) {
			if ($char === '{') {
				return $this->consumeObject();
			}

			if (!ctype_alpha($char) && !in_array($char, ['-', '_', '?'], true)) {
				break;
			}

			$value .= $char;

			$this->next();
		}

		return $value;
	}

	private function consumeSimpleValue(): string
	{
		$value = '';

		while (($char = $this->forehead())) {
			if (!ctype_alpha($char) && !in_array($char, ['-', '_'], true)) {
				break;
			}

			$value .= $char;

			$this->next();
		}

		return $value;
	}

	private function consumeObject(): string
	{
		$value = '';
		$depth = 0;

		while (($char = $this->forehead())) {
			if ($char === '{') {
				$depth++;
			}

			$value .= $char;

			if ($char === '}') {
				$depth--;
				if ($depth === 0) {
					$this->next();

					break;
				}
			}

			$this->next();
		}

		return $value;
	}

}
