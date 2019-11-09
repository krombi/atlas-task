<?php
namespace Helpers;

final class Url
{
    /**    
     * функция генерации ссылки на основе шаблона 
     */
    public static function make($hook = null, $data = []): ?string
    {

        $hooks = ToolsKeeper::get('routing', 'hooks');

        if (count($hooks) && isset($hook) && isset($hooks[$hook])) {

            $hook = $hooks[$hook];

            if (isset($hook['l'])) {

                return preg_replace_callback('/{([a-zA-Z\_\.\d]*)}/', function($matches) use (&$data) {
    
                    $key = array_pop($matches);
                    
                    if (isset($data[$key])) {
                        
                        return $data[$key];
    
                    }
    
                }, $hook['l']);

            }

        }

        return null;

    }

}