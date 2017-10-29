<?php
namespace grovo;

spl_autoload_register(function ($class_name) {
    $filename =  str_replace('\\', '/', $class_name) . ".php";
    if (file_exists($filename)) {
        include($filename);
        if (class_exists($class_name)) {
            return TRUE;
        }
    }
    return FALSE;

});
