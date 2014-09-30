<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SQL;

/**
 *
 * @author anru
 */
interface Token {
    public function lexeme();
    public function match($input,$offset);
}
