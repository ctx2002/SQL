<?php
namespace SQL;

class TokenSet implements IteratorAggregate {
    private $members = null;

    public function __construct()
    {
         $this->members = new \ArrayObject();
    }
    public function create($spec)
    {

        $start = 1;
        if( !$this->startWith($spec, "'"))
        {
                if( $this->containsMeta($spec) )
                {
                        $token = new RegexToken( $spec );
                        $this->members->append($token);
                        return $token;
                }

                --$start;	// don't compensate for leading quote

                // fall through to the "quoted-spec" case
        }

        $end = strlen($spec);

        if( $start==1 &&  $this->endWith($spec, "'") ) { // saw leading '
                --$end;
        }

        $charArray = array(",","(",")",".","*","/","=");
        $token = null;
        if (in_array($spec[$end - 1], $charArray)) {
            $token = new SimpleToken( substr($spec,$start,-1));
        } else {
            $token = new WordToken( substr($spec,$start,-1));
        }
        $this->members->append($token);
        return $token;
    }

    private function containsMeta($spec)
    {
        $p = '%[\\\\\[\]{}$\^*+?|()\-]%ism';
        $r = preg_match($p,$spec,$m);

        return  $r;
    }

    public  function getIterator()
    {
        return $this->members->getIterator();
    }

    private function startWith($string,$char)
    {
        return $string ==="" || strpos($string, $char) === 0;
    }

    private function endWith($string,$char)
    {
        return $string ==="" || strpos($string, $char) === (strlen($string)-1);
    }
}
