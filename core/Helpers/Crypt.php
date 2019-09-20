<?php
namespace Helpers;

final class Crypt
{
    
    const SALT = 'G,HTu*i!=Y:au1^t5k$vd.!ouilDp[Cmn<4yAo=}fp;IBLNY~J-nDmq!#ctsDR:';
    const PASS = 'uv6j-0$?ik6OBQ!-dhS~DkStlXCT;v';

    /**
     * метод кодирования числа
     */
    public static function encode(int $num, string $key = '011'): int
    {

        $dec = decbin($num);
        $shift = self::shift($dec);

        // добавляем ключ
        if (preg_match('/^[0,1]+$/', $key)) {
            
            $shift .= $key;

        }

        return bindec($shift);

    }

    /**
     * метод декодирования числа
     */
    public static function decode(int $num, string $key = '011'): ?int
    {

        $dec = decbin($num);

        if (preg_match('/^[0,1]+$/', $key)) {
            
            $key_count = strlen($key);
            $bin_key = substr($dec, -$key_count, $key_count);

            if ($bin_key === $key) {

                // убираем ключ
                $bin = substr($dec, 0, -$key_count);
                $shift = self::shift($bin, 'back');
                
                return bindec($shift);

            }

        }

        return null;

    }

    public static function encrypt(string $value, $pass = null): ?string
    {

        $cipher = self::scrambler($value, $pass);
        return base64_encode($cipher);

    }

    public static function decrypt(string $value, $pass = null): ?string
    {

        if ($cipher = base64_decode($value)) {

            return self::scrambler($cipher, $pass);

        } else {
            
            return false;

        }

    }

    /**
     * вспомогательный метод для сдвига значений числа в бинарном формате
     */
    private static function shift(string $bin, string $direction = 'forward', int $count = 11): string
    {

        $bins = str_split($bin);
        
        for ($i = 0; $i < $count; $i++) {

            switch ($direction) {
                case 'forward': {
                    $bins[] = array_shift($bins);
                    break;
                }
                case 'back': {
                    array_unshift($bins, array_pop($bins));
                    break;
                }
            }

        }

        $bins = implode("", $bins);

        return $bins;

    }

    /**
     * 
     */
    private static function scrambler(string $string, $pass)
    {

        $length = strlen($string);
        $gamma = '';
        $salt = self::SALT;
        $pass = (is_string($pass)) ? $pass : self::PASS;

        $n = $length > 100 ? 8 : 2;

        while(strlen($gamma) < $length) {

            $gamma .= substr(pack('H*', sha1($pass . $gamma . $salt)), 0, $n);
                        
        }

        return $string^$gamma;

    }

}