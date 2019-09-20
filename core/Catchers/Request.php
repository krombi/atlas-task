<?php
namespace Catchers;

use Helpers\Crypt;
use Handlers\Exceptions\CustomException;

final class Request
{

    /**
     * хранилище ip адреса
     */
    public static $ip = null;

    /**
     * хранилище информации об агента посетителя
     */
    public static $agent = null;
    
    /**
     * хранилище данные из POST запроса
     */
    public static $post = [];

    /**
     * хранилище GET параметров страницы
     */
    public static $get = [];

    /**
     * хранилище куки
     */
    public static $cookie = [];

    /**
     * хранилище данных из $_SERVER
     */
    public static $server = [];

    /**
     * хранилище csrf токена
     */
    public static $csrf = null;

    /**
     * статус текущего запроса если POST
     */
    public static $is_post = false;

    /**
     * статус текущего запроса если AJAX
     */
    public static $is_ajax = false;

    /**
     * получение запросов и присвоение их в переменные
     * для дяльнейшей работы из различных частей приложения
     */
    public static function prepare(): void
	{

        // получаем данные из $_SERVER
        self::$server = $_SERVER ?? [];

        // получаем и очищаем от всякого хлама GET запрос
		if (!empty($_GET)) {

			self::$get = self::cleanData($_GET);

		}

        // получаем и очищаем от всякого хлама POST запрос
        if (!empty($_POST)) {

			self::$post = self::cleanData($_POST);
			
            // указываем что пришедший запрос именно POST
			self::$is_post = true;

        }

        // проверяем запрос на AJAX и если это так то отмечаем это
        if (
            !empty(self::$server['HTTP_X_REQUESTED_WITH']) 
            && strtolower(self::$server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            
            self::$is_ajax = true;
    
        }

        // получаем и очищаем от всякого хлама куки
        if (!empty($_COOKIE)) {

			self::$cookie = self::cleanData($_COOKIE);

        }

        // получаем IP адрес посетителя
        self::getIp();

        // получаем useragent посетителя
        self::uAgent();

        // проверяем и генерируем csrf токен
        self::csrfChecking();

    }

    /**
     * метод генерации csrf токена
     */
    private static function csrfChecking(): void
    {

        // print_r($_SESSION['token']);

        $user_hash = hash("sha1", self::$ip . self::$agent);

        // if (self::$is_post) {

        //     if (
        //         !empty(self::$post['csrf-token'])
        //         && $token = Crypt::decrypt(self::$post['csrf-token'], $user_hash)
        //     ) {

        //         if (!empty($_SESSION['tokens']) && in_array($token, $_SESSION['tokens'])) {

        //             // далее будем проверять на дубликат запроса
                    
    
        //         } else {
    
        //             throw new CustomException(702);
    
        //         }

        //     } else {

        //         throw new CustomException(701);
    
        //     }

        // }

        $token = randString(40);
        $_SESSION['token'] = $token;

        self::$csrf = Crypt::encrypt($token, $user_hash);

    }
    
    /**
     * метод получения IP посетителя
     */
    private static function getIp(): void
    {

        $keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR', 'HTTP_X_REAL_IP');

        foreach ($keys as $key) {

            $ip = trim(strtok(filter_input(INPUT_SERVER, $key), ','));
            $filter = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

            if ((bool) $filter) {

                self::$ip = $filter;
                break;

            }

        }

    }

    /**
     * метод получения юсерагента посетителя
     */
    private static function uAgent(): void
    {

        $results = [];
        
        if (!empty(self::$server['HTTP_USER_AGENT'])) {

            $u_agent = self::$server['HTTP_USER_AGENT'];
			
			$platform = null;
			$browser  = null;
			$version  = null;
			$results = [
                'platform' => $platform, 
                'browser' => $browser, 
                'version' => $version
            ];
			
			if (preg_match('/\((.*?)\)/im', $u_agent, $parent_matches)) {
				
				preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS)|Xbox(\ One)?)
						(?:\ [^;]*)?
						(?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);
						
				$priority = [
                    'Xbox One', 
                    'Xbox', 
                    'Windows Phone', 
                    'Tizen', 
                    'Android', 
                    'CrOS', 
                    'X11'
                ];
				$result['platform'] = array_unique($result['platform']);
				
				if (count($result['platform']) > 1) {
					
					if ($keys = array_intersect($priority, $result['platform'])) {
						
						$platform = reset($keys);
						
					} else {
						
						$platform = $result['platform'][0];
						
					}
					
				} elseif (isset($result['platform'][0])) {
					
					$platform = $result['platform'][0];
					
				}
				
            }
            
			if ($platform == 'linux-gnu' || $platform == 'X11') {
				
				$platform = 'Linux';
				
			} elseif ($platform == 'CrOS') {
				
				$platform = 'Chrome OS';
				
            }
            
			preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
						TizenBrowser|Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|UCBrowser|Puffin|SamsungBrowser|
						Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
						Valve\ Steam\ Tenfoot|
						NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
						(?:\)?;?)
						(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
				$u_agent, $result, PREG_PATTERN_ORDER);
				
			if (!isset($result['browser'][0]) || !isset($result['version'][0])) {
				
				if (preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result)) {
					
					$results = [
                        'platform' => $platform ?: null, 
                        'browser' => $result['browser'], 
                        'version' => isset($result['version']) ? $result['version'] ?: null : null
                    ];
					
				}
				
            }
            
			if (preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $u_agent, $rv_result)) {
				
				$rv_result = $rv_result['version'];
				
            }
            
			$browser = $result['browser'][0];
			$version = $result['version'][0];
			
			$lowerBrowser = array_map('strtolower', $result['browser']);
			
			$find = function ($search, &$key, &$value = null) use ($lowerBrowser) {
				
				$search = (array) $search;
				
				foreach ($search as $val) {
					
					$xkey = array_search(strtolower($val), $lowerBrowser);
					
					if ($xkey !== false) {
						
						$value = $val;
						$key   = $xkey;
						return true;
						
					}
					
				}
				
				return false;
				
			};
			
			$key = 0;
			$val = '';
			
			if ($browser == 'Iceweasel' || strtolower($browser) == 'icecat') {
				
				$browser = 'Firefox';
				
			} elseif ($find('Playstation Vita', $key)) {
				
				$platform = 'PlayStation Vita';
				$browser  = 'Browser';
				
			} elseif ($find(array('Kindle Fire', 'Silk'), $key, $val)) {
				
				$browser = $val == 'Silk' ? 'Silk' : 'Kindle';
				$platform = 'Kindle Fire';
				
				if (!($version = $result['version'][$key]) || !is_numeric($version[0])) {
					
					$version = $result['version'][array_search('Version', $result['browser'])];
					
				}
				
			} elseif ($find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS') {
				
				$browser = 'NintendoBrowser';
				$version = $result['version'][$key];
				
			} elseif ($find('Kindle', $key, $platform)) {
				
				$browser = $result['browser'][$key];
				$version = $result['version'][$key];
				
			} elseif ($find('OPR', $key)) {
				
				$browser = 'Opera Next';
				$version = $result['version'][$key];
				
			} elseif ($find('Opera', $key, $browser)) {
				
				$find('Version', $key);
				$version = $result['version'][$key];
				
			} elseif ($find('Puffin', $key, $browser)) {
				
				$version = $result['version'][$key];
				
				if (strlen($version) > 3) {
					
					$part = substr($version, -2);
					
					if (ctype_upper($part)) {
						
						$version = substr($version, 0, -2);
						$flags = [
                            'IP' => 'iPhone', 
                            'IT' => 'iPad', 
                            'AP' => 'Android', 
                            'AT' => 'Android', 
                            'WP' => 'Windows Phone', 
                            'WT' => 'Windows'
                        ];
						
						if (isset($flags[$part])) {
							
							$platform = $flags[$part];
							
						}
						
					}
					
				}
				
			} elseif ($find(['IEMobile', 'Edge', 'Midori', 'Vivaldi', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome'], $key, $browser)) {
				
				$version = $result['version'][$key];
				
			} elseif ($rv_result && $find('Trident', $key)) {
				
				$browser = 'MSIE';
				$version = $rv_result;
				
			} elseif ($find('UCBrowser', $key)) {
				
				$browser = 'UC Browser';
				$version = $result['version'][$key];
				
			} elseif ($find('CriOS', $key)) {
				
				$browser = 'Chrome';
				$version = $result['version'][$key];
				
			} elseif ($browser == 'AppleWebKit') {
				
				if ($platform == 'Android' && !($key = 0)) {
					
					$browser = 'Android Browser';
					
				} elseif (strpos($platform, 'BB') === 0) {
					
					$browser  = 'BlackBerry Browser';
					$platform = 'BlackBerry';
					
				} elseif ($platform == 'BlackBerry' || $platform == 'PlayBook') {
					
					$browser = 'BlackBerry Browser';
					
				} else {
					
					$find('Safari', $key, $browser) || $find('TizenBrowser', $key, $browser);
					
				}
				
				$find('Version', $key);
				$version = $result['version'][$key];
				
			} elseif ($pKey = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser']))) {
				
				$pKey = reset($pKey);
				$platform = 'PlayStation ' . preg_replace('/[^\d]/i', '', $pKey);
				$browser  = 'NetFront';
				
			}
			
			$results = [
                'platform' => $platform ?: null, 
                'browser' => $browser ?: null, 
                'version' => $version ?: null
            ];
			
        }
        
        self::$agent = json_encode($results);
		
    }
    
    /**
     * функция чистки данных
     */
	private static function cleanData(array $data): array
	{

		$output = [];

        foreach ($data as $key => $el) {

            if (!empty($el)) {

                if (is_array($el)) {

                    $el = self::cleanData($el);

                } else {

                    $el = trim($el);
                    $el = stripslashes($el);
                    $el = strip_tags($el);
                    $el = htmlspecialchars($el);

                }

            } else $el = null;

            $output[$key] = $el;

        }

		return $output;

    }

}