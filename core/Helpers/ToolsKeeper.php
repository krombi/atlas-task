<?php
namespace Helpers;

final class ToolsKeeper
{

    const PATH = CORE_PATH . DS . 'tools';

    private static $keeper = [];
    private static $exts = ['php', 'json'];

    /**
     * 
     */
    public static function get(... $sections): ?array
    {

        if (is_null(val(self::$keeper, $sections, false))) {

            $ext = defined('EXT_TOOLS') && in_array(EXT_TOOLS, self::$exts) ? EXT_TOOLS : 'php';
            $path = implode(DS, $sections);
            $file = self::PATH . DS . $path . '.' . $ext;

            if (file_exists($file)) {

                $content = null;

                switch ($ext) {
                    case 'php': {
                        $content = include($file);
                        break;
                    }
                    case 'json': {
                        $content = json_decode(
                            file_get_contents($file),
                            true
                        );
                        break;
                    }
                }

                if (is_array($content)) {

                    self::updKeeper($sections, $content);

                }

            }

        }

        return val(self::$keeper, $sections, false);

    }

    /**
     * 
     */
    public static function set(array $data, ... $sections): void
    {

        self::updKeeper($sections, $data);

    }

    /**
     * 
     */
    private static function updKeeper(array $sections, array $data): void
    {

        $results = self::createNesting($sections, $data);
        self::$keeper = array_replace_recursive(self::$keeper, $results);

    }

    /**
     * 
     */
    private static function createNesting(array $list, array $data = []): array
    {

        $result = []; 

        if (count($list)) {

            $section = array_shift($list);

            if (!count($list)) {

                $result[$section] = $data;

            } else {

                $result[$section] = self::createNesting($list, $data);

            }

        }

        return $result;

    }

}