<?php
namespace AppBundle\DoctrineExtensions;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
/**
 * DateDiffFunction ::= "TIMEDIFF" "(" ArithmeticPrimary "," ArithmeticPrimary ")"
 */
class TimeDiff extends FunctionNode
{
    /** @var \Doctrine\ORM\Query\AST\SimpleArithmeticExpression  */
    public $firstDateExpression = null;
    /** @var \Doctrine\ORM\Query\AST\SimpleArithmeticExpression  */
    public $secondDateExpression = null;
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstDateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondDateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'TIMEDIFF(' .
        $this->firstDateExpression->dispatch($sqlWalker) . ', ' .
        $this->secondDateExpression->dispatch($sqlWalker) .
        ')';
    }
}
