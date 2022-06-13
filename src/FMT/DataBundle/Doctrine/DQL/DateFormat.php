<?php

namespace FMT\DataBundle\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class DateFormat extends FunctionNode
{
    private $dateTime;
    private $format;

    /**
     * @param SqlWalker $sqlWalker
     * @return string|void
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'DATE_FORMAT(%s, %s)',
            $this->dateTime->dispatch($sqlWalker),
            $this->format->dispatch($sqlWalker)
        );
    }

    /**
     * @param Parser $parser
     * @throws QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateTime = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->format = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
