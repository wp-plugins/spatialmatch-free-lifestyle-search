<?php

class SpatialMatch_Util_CSS
{
    public static function checked ($b)
    {
        if ($b === true)
        {
            echo 'checked=\'checked\'';
        }
    }
    
    public static function selected ($b)
    {
        if ($b === true)
        {
            echo 'selected=\'selected\'';
        }
    }
}
