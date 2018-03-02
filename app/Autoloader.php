<?php

namespace app;

class Autoloader
{

    protected static $base;

    static public function loader($className)
    {
        $filename = self::$base . DIRECTORY_SEPARATOR . str_replace('\\', '/', $className) . '.php';
        if (file_exists($filename)) {
            include_once($filename);
            if (class_exists($className)) {
                return true;
            }
        }
        return false;
    }

    function __construct(string $base = '')
    {
        self::$base = realpath($base);
        spl_autoload_register(array($this, 'loader'));
    }
}

;