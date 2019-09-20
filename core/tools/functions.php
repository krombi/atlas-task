<?php
if (!function_exists('val')) {

    /**
     * проверка эелементов выводимых в шаблон
     *
     * @param  array $data - массив с данными доступными шаблону
     * @param string $keys - перечислаение вложенности ключей через "."
     * 
     * @return string - в результате возвразаем либо значение переменной массива
     * либо пустую строку null
     */

    function val(array $data = array(), $keys = [], bool $stringify = true)
    {

        $return = null;

        $keys = is_array($keys) ? $keys : ($keys = explode('.', $keys)) ? $keys : [];

        if (count($keys)) {

            while (true) {

                $key = array_shift($keys);

                if (isset($data[$key])) {

                    if (is_array($data[$key]) && !empty($keys)) {

                        $data = $data[$key];

                    } else {

                        if (is_array($data[$key])) {

                            $return = $stringify ? implode(PHP_EOL, $data[$key]) : $data[$key];

                        } else {

                            $return = $data[$key];

                        }

                        break;
                    }

                } else {

                    break;
                    
                }

            }

        }

        return $return;

    }

}

if (!function_exists('randString')) {

    /**
     * функция генерации случайной строки
     * 
     * @param int $length - длина необходимой строки
     * 
     * @return string $string - возвращаемый результат в виде случайного набора символов
     */
    function randString(int $length = 32) 
    {
        
        // строка возможных допустимых символов
        $chars = 'abcdeghijknopqrsuvwxyzABDEFGHIKLNOPQTUVWXYZ123456790#$%&@';
        
        // формируем строку символов в массив и перемешиваем его
        $chars = str_split($chars);
        shuffle($chars);
        
        // определяем переменную с будущей рандомной строкой
        $string = '';
        
        // и собственно запускаем рандомность
        for ($i = 0; $i < $length; $i++) {
            
            $sk = array_rand($chars);
            $string .= $chars[$sk];
            
        }
        
        return $string;
        
    }

}