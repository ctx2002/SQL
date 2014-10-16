<?php
namespace SQL;

class Database {
    private $tokenSet;
    private static $COMMA;

    public function __construct()
    {
        $this->tokenSet = new TokenSet();
    }

    protected function init()
    {

    }

    private function setupTokens()
    {
        self::$COMMA =  $this->tokens->create( "'," 		); //{=Database.firstToken}
    }
}
