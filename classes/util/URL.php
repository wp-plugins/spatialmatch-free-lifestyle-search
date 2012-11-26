<?php

class SpatialMatch_Util_URL
{
    public static function getParameter ($paramName, $defaultValue = '')
    {
        $value = null;
        
        if (isset($_POST[$paramName]))
        {
            $value = stripslashes(trim($_POST[$paramName]));
        }
        else if (isset($_GET[$paramName]))
        {
            $value = stripslashes(trim($_GET[$paramName]));
        }
        
        return (isset($value) && (strlen($value) > 0)) ? $value : $defaultValue;
    }
}
