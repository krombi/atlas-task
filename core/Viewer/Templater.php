<?php
namespace Viewer;

final class Templater 
{

    const PATH = CORE_PATH . DS . 'templates';
    private static $ext = ['php'];

    /**
     * 
     */
    public static function gen(string $tpl, array $data = []): ?string
    {

        if (preg_match("/^[a-z\d\/\-\_]+$/", $tpl)) {

            $ext = defined('EXT_TEMPLATES') && in_array(EXT_TEMPLATES, self::$ext) ? EXT_TEMPLATES : 'php';
            $file = self::PATH . DS . $tpl . '.' . $ext;

            if (file_exists($file)) {

                ob_start();
                include($file);
                return ob_get_clean();

            }

        }

        return null;
        
    }

    /**
     * 
     */
    public static function create(
        string $selector, 
        array $attributes = [], 
        $body = null, 
        bool $cover = false
    ): string {

        $opening = [
            $selector
        ];

        $closing = $cover ? "</$selector>" : null;

        // сразу выбираем необходимые дата атрибуты
        $data = [];
        if (!empty($attributes['data'])) {

            $data = $attributes['data'];
            unset($attributes['data']);

        }

        // пробегаем по атрибутам и добавляем их к селектору
        foreach ($attributes as $attribute => $value) {

            if (preg_match("/^[a-z]+$/", $attribute)) {

                $opening[] = self::prepareAttr($attribute, $value);

            } else {

                $opening[] = $value;

            }

        }

        // пробегаем по дата атрибутам что бы добавить их к селектору
        if (is_array($data) && count($data)) {

            foreach ($data as $ident => $value) {

                $opening[] = self::prepareAttr("data-$ident", $value);
                
            }

        }

        // получаем внутренности открывающего тега со всеми атрибутами
        $opening = implode(' ', $opening);

        // получаем открывающий атрибут
        $opening = "<$opening>";

        // 
        $body = is_array($body) ? implode(PHP_EOL, $body) : $body;
        
        // подготавливаем тело тега
        $wrapper = [
            $opening,
            $body,
            $closing
        ];

        return implode("", $wrapper);

    }

    /**
     * 
     */
    private static function prepareAttr(string $attribute, $value = null)
    {

        if (is_array($value)) {

            $value = implode(' ', $value);

        }

        return "$attribute=\"$value\"";

    }

}