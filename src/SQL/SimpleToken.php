<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SQL;

/**
 * Description of SimpleToken
 *
 * @author anru
 */
class SimpleToken implements \SQL\Token {

    use \SQL\StringFuncs;
    private $pattern;

    public function __construct($pattern) {
        $this->pattern = $pattern;
    }

    public function match( $input, $offset )
    {
        $input = strtolower($input);
        return $this->startWith($input,$this->pattern,$offset);

    }

    public function lexeme()  { return $this->pattern; }
    public function __toString(){ return $this->pattern; }
}
