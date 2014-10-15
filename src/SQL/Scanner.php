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
    private $inputLine     	= "";
    private $inputPosition 	= 0;
    private $index = 0;

    private $currentStartPosition = 0;

    private $tokens;

    /** Create a Scanner for the indicated token set, which
     *  will get input from the indicated string.
     */
    public function __construct( TokenSet $tokens, $input )
    {
        $this->currentToken = new BeginToken();
        $this->init( $tokens, $input);
    }

    public function init( TokenSet $tokens, $inputReader )
    {
        $this->tokens 	 = $tokens;
        $this->inputReader = $inputReader;
        $this->loadLine();
    }

    private function loadLine()
    {
        //$index = 0;
        while(1) {
            if (isset($this->inputReader[$this->index])) {
                if ($this->inputReader[$this->index] == "\n") {
                    $this->index += 1;
                    break;
                }
            } else {
                break;
            }
            //$this->inputReader[$index] != "\n" && $this->inputReader[$index] != "\r"
            $this->index += 1;
        }
        
        $this->inputLine = substr($this->inputReader,$this->currentStartPosition,$this->index);
        $this->inputLine = trim($this->inputLine);
        //var_dump($this->inputLine);
        if ($this->inputLine) {
            $this->inputPosition = 0;
            $this->inputLineNumber += 1;
            $this->currentStartPosition += strlen($this->inputLine);
        }
        
        $b = !empty( $this->inputLine );
        return $b;
    }

    public function advance()
    {
        //var_dump($this->currentToken);
        if (!is_null( $this->currentToken)) {
                $this->inputPosition += strlen( $this->currentToken->lexeme());
                
                $this->currentToken   = null;
                
                if( $this->inputPosition == strlen( $this->inputLine) ) {
                    
                    if( !$this->loadLine() ) {
                        return null;
                    }
                }

                if (isset($this->inputLine[$this->inputPosition])) {
                    while(ctype_space($this->inputLine[$this->inputPosition] )) {
                        if( ++$this->inputPosition == strlen( $this->inputLine) ) {
                            if( !$this->loadLine() ) {
                                return null;
                            }
                        }
                    }
                }
                //var_dump($this->inputLine);
                foreach($this->tokens as $token) {
                    
                    if ($token->match($this->inputLine,$this->inputPosition)) {
                        //var_dump($this->inputLine, $token);
                        $this->currentToken = $token;
                        break;
                    }
                }
                
                if( is_null( $this->currentToken ) ) {
                    throw new \Exception("Unrecognized Input");
                }
        }
        
        return $this->currentToken;
    }
    

    public function matchAdvance( Token $candidate )
    {	
        if( $this->match($candidate) )
        {	
            $lexeme = $this->currentToken->lexeme();
            $this->advance();
            return $lexeme;
        }
            return null;
    }

  
    public function required( Token $candidate )
    {	$lexeme =	$this->matchAdvance($candidate);
            if( $lexeme == null ) {
                    throw new Exception(
                                    "\"" . $this->candidate->__toString() . "\" expected.");
            }
            return lexeme;
    }

    public function match( Token $candidate )
    {
        return $this->currentToken->__toString() == $candidate->__toString();
    }
}
