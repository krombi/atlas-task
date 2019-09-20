<?php
/**
 * разделитель директорий
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * путь до ядра
 */
define('CORE_PATH', dirname(__DIR__));

/**
 * путь до корня сайта
 */
define('SITE_DIR', dirname(CORE_PATH));

/**
 * расширение файлов со вспомогательными значениями
 */
define('EXT_TOOLS', 'php');

/**
 * расширение файлов шаблонов
 */
define('EXT_TEMPLATES', 'php');

/**
 * константа хоста для подключения к базе данных
 */
define('DB_HOST', 'localhost');

/**
 * константа имени базы данных
 */
define('DB_NAME', 'base');

/**
 * константа имен пользователя базы данных
 */
define('DB_USER', 'user');

/**
 * константа пароля пользователя базы данных
 */
define('DB_PASS', 'pass');