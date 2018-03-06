<?php

namespace app\helper;

class Form
{
    public static function checkFields(array $fields)
    {
        foreach ($fields as $field) {
            if(!array_key_exists($field, $_POST)) {
                return false;
            }
        }
        return true;
    }
}