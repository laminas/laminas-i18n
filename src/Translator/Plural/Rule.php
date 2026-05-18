<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Plural;

use Laminas\I18n\Exception;

use function abs;
use function assert;
use function floor;
use function is_int;
use function preg_match;
use function sprintf;

/**
 * Plural rule evaluator
 */
final readonly class Rule
{
    private function __construct(
        private int $numPlurals,
        private Node $ast,
    ) {
    }

    /**
     * Evaluate a number and return the plural index.
     *
     * @throws Exception\RangeException
     */
    public function evaluate(int $number): int
    {
        $result = $this->evaluateAstPart($this->ast, abs($number));

        if ($result < 0 || $result >= $this->numPlurals) {
            throw new Exception\RangeException(
                sprintf('Calculated result %s is between 0 and %d', $result, $this->numPlurals - 1)
            );
        }

        return $result;
    }

    /**
     * Get number of possible plural forms.
     */
    public function getNumPlurals(): int
    {
        return $this->numPlurals;
    }

    /**
     * Evaluate a part of an ast.
     *
     * @throws Exception\ParseException
     */
    protected function evaluateAstPart(Node $ast, int $number): int
    {
        $first  = $ast->arguments[0] ?? null;
        $second = $ast->arguments[1] ?? null;
        $third  = $ast->arguments[2] ?? null;

        if ($ast->id === 'number') {
            assert(is_int($first));

            return $first;
        }

        if ($ast->id === 'n') {
            return $number;
        }

        assert($first instanceof Node);

        if ($ast->id === '!') {
            $result = $this->evaluateAstPart($first, $number);

            return $result === 0 ? 1 : 0;
        }

        assert($second instanceof Node);

        if ($ast->id === '?') {
            assert($third instanceof Node);

            return $this->evaluateAstPart($first, $number)
                ? $this->evaluateAstPart($second, $number)
                : $this->evaluateAstPart($third, $number);
        }

        return match ($ast->id) {
            '+' => $this->evaluateAstPart($first, $number)
                   + $this->evaluateAstPart($second, $number),
            '-' => $this->evaluateAstPart($first, $number)
                   - $this->evaluateAstPart($second, $number),
            // Integer division
            '/' => (int) floor(
                $this->evaluateAstPart($first, $number)
                / $this->evaluateAstPart($second, $number)
            ),
            '*' => $this->evaluateAstPart($first, $number)
                   * $this->evaluateAstPart($second, $number),
            '%' => $this->evaluateAstPart($first, $number)
                   % $this->evaluateAstPart($second, $number),
            '>' => $this->evaluateAstPart($first, $number)
                   > $this->evaluateAstPart($second, $number)
                   ? 1 : 0,
            '>=' => $this->evaluateAstPart($first, $number)
                   >= $this->evaluateAstPart($second, $number)
                   ? 1 : 0,
            '<' => $this->evaluateAstPart($first, $number)
                   < $this->evaluateAstPart($second, $number)
                   ? 1 : 0,
            '<=' => $this->evaluateAstPart($first, $number)
                   <= $this->evaluateAstPart($second, $number)
                   ? 1 : 0,
            '==' => $this->evaluateAstPart($first, $number)
                   === $this->evaluateAstPart($second, $number)
                   ? 1 : 0,
            '!=' => $this->evaluateAstPart($first, $number)
                   !== $this->evaluateAstPart($second, $number)
                   ? 1 : 0,
            '&&' => $this->evaluateAstPart($first, $number)
                   && $this->evaluateAstPart($second, $number)
                   ? 1 : 0,
            '||' => $this->evaluateAstPart($first, $number)
                   || $this->evaluateAstPart($second, $number)
                   ? 1 : 0,
            default => throw new Exception\ParseException(sprintf(
                'Unknown token: %s',
                $ast->id,
            )),
        };
    }

    /**
     * Create a new rule from a string.
     *
     * @throws Exception\ParseException
     */
    public static function fromString(string $string): Rule
    {
        if (! preg_match('(nplurals=(?P<nplurals>\d+))', $string, $match)) {
            throw new Exception\ParseException(sprintf(
                'Unknown or invalid parser rule: %s',
                $string
            ));
        }

        $numPlurals = (int) $match['nplurals'];

        if (! preg_match('(plural=(?P<plural>[^;\n]+))', $string, $match)) {
            throw new Exception\ParseException(sprintf(
                'Unknown or invalid parser rule: %s',
                $string
            ));
        }

        $tree = Parser::parse($match['plural']);
        $ast  = self::createAst($tree);

        return new self($numPlurals, $ast);
    }

    /**
     * Create an AST from a tree.
     *
     * Theoretically we could just use the given Symbol, but that one is not
     * so easy to serialize and also takes up more memory.
     */
    protected static function createAst(Symbol $symbol): Node
    {
        $args = [];

        switch ($symbol->id) {
            case 'n':
                break;

            case 'number':
                assert(is_int($symbol->value));
                $args[] = $symbol->value;
                break;

            case '!':
                assert($symbol->first !== null);
                $args[] = self::createAst($symbol->first);
                break;

            case '?':
                assert($symbol->first !== null);
                assert($symbol->second !== null);
                assert($symbol->third !== null);
                $args[] = self::createAst($symbol->first);
                $args[] = self::createAst($symbol->second);
                $args[] = self::createAst($symbol->third);
                break;

            default:
                assert($symbol->first !== null);
                assert($symbol->second !== null);
                $args[] = self::createAst($symbol->first);
                $args[] = self::createAst($symbol->second);
                break;
        }

        return new Node($symbol->id, $args);
    }
}
