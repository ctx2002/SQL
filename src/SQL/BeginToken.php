<?php
namespace SQL;
class BeginToken implements Token
{
    public function  match($input,$offset)
    {
        return false;
    }
    public function lexeme()
    {
        return "";
    }
    public function toString()
    {
        return "BeginToken"; 
    }
}
