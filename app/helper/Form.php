<?php

namespace app\helper;

use Psr\Http\Message\RequestInterface;

class Form
{
    public static function checkFields(RequestInterface $request, array $fields)
    {
        foreach ($fields as $field) {
            if(!array_key_exists($field, $request->getParsedBody())) {
                return false;
            }
        }
        return true;
    }
}