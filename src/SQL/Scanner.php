<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SQL;

/**
 * Description of Scanner
 *
 * @author anru
 */
class Scanner {
    private $currentToken	= null;
    private $inputReader 	= null;
    private $inputLineNumber = 0;
    private $inputLine     	= null;
    private $inputPosition 	= 0;

    private $tokens;

    /** Create a Scanner for the indicated token set, which
     *  will get input from the indicated string.
     */
    public function __construct( TokenSet $tokens, $input )
    {	
        $this->currentToken = new BeginToken();
        $this->init( tokens, $input);	
    }
    
    public function init( TokenSet $tokens, $inputReader )
    {	
        $this->tokens 	 = tokens;
        $this->inputReader = $inputReader;
        loadLine();
    }
    
    private function loadLine()
    {
        
    }
    
    public function match( Token $candidate )
    {	
        return $this->currentToken->__toString() == $candidate->__toString();
    }
}
