<?php

namespace SQL;

class ThrowableContainer extends \RuntimeException
{	
    private $contents;
    
    public function __construct($contents )
    {	
        $this->contents = $contents;
    }
    public function contents()
    {	return $this->contents;
    }
}
