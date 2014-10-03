<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SQL;

/**
 * Description of ParseFailure
 *
 * @author anru
 */
class ParseFailure extends \Exception {
    private $inputLine;
    private $inputPosition;
    private $inputLineNumber;
    public function __construct($message,
				$inputLine,
				$inputPosition, 
				$inputLineNumber)
    {
        parent::__construct($message);
	$this->inputPosition   = $inputPosition;
	$this->inputLine = $inputLine;
	$this->inputLineNumber = $inputLineNumber;    
    }
    
    public function getErrorReport()
    {	
        $str = "Line ";
        $str .= $this->inputLineNumber . ":\n";
        $str .= $this->inputLine;
        $str .= "\n";
        for ($i = 0; $i < $this->inputPosition; ++$i) {
            $str .= "_";
        }
        $str .= "^\n";
        return $str;
    }
}
