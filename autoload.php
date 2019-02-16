<?php

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'Fal\\Windowing\\') && file_exists($file = __DIR__.'/src/'.substr(str_replace('\\', '/', $class), 4).'.php')) {
        require $file;

        return true;
    }
});
