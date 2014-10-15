<?php
namespace SQL;

class WordToken implements \SQL\Token {
   private $pattern;

   public function __construct($pattern)
   {
       $this->pattern =  strtolower($pattern);
   }

   public function match($input, $offset)
    {
            // Check that the input matches the patter in a
            // case-insensitive way. If you don't want case
            // insenstivity, use the following, less complicated code:
            //
            // if( !input.toLowerCase().startsWith(pattern, offset) )
            //	  return false;

            if( (strlen($input) - $offset) < strlen($this->pattern) )
                    return false;

            $candidate = substr($input,$offset, $offset + strlen($this->pattern));
            
           if ( strncasecmp($candidate, $this->pattern,strlen($this->pattern)) !== 0 ) {
               return false;
           }
           // Return true if the lexeme is at the end of the
            // input string or if the character following the
            // lexeme is not a letter or digit.
            //
            //if the lexeme is at the end of the
            // input string
           $v =  ( (strlen($input) - $offset ) == strlen($this->pattern) );

            // OR if the character following the
            // lexeme is not a letter or digit
           $char = "";
           if (isset($input[ $offset + strlen($this->pattern) ])) {
               $char = $input[ $offset + strlen($this->pattern) ];
           }
           $t = !(ctype_alpha($char) || ctype_digit($char));
           return $v || $t;
            // Return true if the lexeme is at the end of the
            // input string or if the character following the
            // lexeme is not a letter or digit.
            /*
            return 	   ((input.length() - offset) == pattern.length())
                            || (!Character.isLetterOrDigit(
                                            input.charAt(offset + pattern.length()) ));*/
    }

   public function lexeme()  { return $this->pattern; }
    public function __toString(){ return $this->pattern; }
}
