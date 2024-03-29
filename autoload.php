<?php

/**
 * @param $prefix - project-specific namespace prefix
 * @param $baseDir - base directory for the namespace prefix
 * @return Closure
 */
function autoload_register($prefix, $baseDir) {
    return function ($class) use ($prefix, $baseDir) {
        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        $relative_class = substr($class, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $baseDir . str_replace('\\', '/', $relative_class) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    };
}

spl_autoload_register( autoload_register(API_NAMESPACE,  API_DIR_CLASSES) );
spl_autoload_register( autoload_register(API_NAMESPACE,  API_DIR_CONTROLLERS) );