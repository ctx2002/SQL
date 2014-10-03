<?php
namespace SQL;
class RegexToken implements \SQL\Token {
   private $pattern;
   private $sub;
   public function __construct($pattern)
   {
       $this->pattern =$pattern;
   }

   public function match($input, $offset)
    {
        $this->sub = substr($input,$offset);
        $p = "%^".$this->pattern."%ism";
        return preg_match($p,$this->sub);
    }

    public function lexeme()
    {
        $p = "%^".$this->pattern."%ism";
        if ( preg_match($p,$this->sub,$m) ) {
            return $m[0];
        } else {
            return null;
        }
        /**
            this function should return a string
         *          */
    }
    public function __toString() { return $this->pattern; }
}
