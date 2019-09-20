<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * запускаем сессию
 */
session_start();

/**
 *  Установка дефолтной кодировки в UTF-8 
 */
mb_internal_encoding("UTF-8");
header('charset=utf-8');

/**
 * подключаем файл с подключаемыми файлами
 */
include_once('plugged.php');

/**
 * подключаем роутер
 */
try {
        
    $router = new Routing\Router();
    $router->prepare();

} catch (Throwable $e) {}

/**
 * если роутер успешно подключен,
 * то запускаем генерацию страницы
 */
if (isset($router) && $router instanceof Routing\Router) {

    try {

        $router->build();

    } catch (Handlers\Exceptions\CustomException $e) {}
    
}

// var_dump(Core\Helpers\Coder::encode(15788));
// var_dump(Core\Helpers\Coder::decode(81323));