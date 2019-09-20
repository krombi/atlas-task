<?php
/**
 * автоматическое подключение классов
 */
spl_autoload_register(function($class_name) {

    // получаем имя класса без указания корня
    $name = trim($class_name, '/\\');

    // на основании пространства имен получаем путь до файла
    $way = str_replace("\\", "/", $name);
    
    // получаем путь до файла с классом
    $path = $way . '.php';

    if (defined('SITE_DIR')) {

        $file_path = [
            CORE_PATH,
            $path            
        ];

        $file = implode(DS, $file_path);

        if (file_exists($file)) {

            require_once($file);

        }

    }

});
