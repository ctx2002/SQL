<?php
namespace SQL;


interface Token {
    public function lexeme();
    public function match($input,$offset);
}
