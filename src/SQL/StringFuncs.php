<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SQL;

/**
 * Description of NormalToken
 *
 * @author anru
 */
trait StringFuncs {
    public function startWith($string,$char,$offset=0)
    {
        return $string ==="" || strpos($string, $char,$offset) === 0;
    }

    public function endWith($string,$char)
    {
        return $string ==="" || strpos($string, $char) === (strlen($string)-1);
    }
}
