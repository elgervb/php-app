<?php
namespace validate;

use validate\checker\BooleanChecker;
use validate\checker\FloatChecker;
use validate\checker\IntegerChecker;

class Args {
    public static function bool($val, $name = 'Arg') {
        return new BooleanChecker($val, $name);
    }
    
    public static function float($val, $name = 'Arg') {
       return new FloatChecker($val, $name);
    }
    
    public static function int($val, $name = 'Arg') {
        return new IntegerChecker($val, $name);
    }      
}
