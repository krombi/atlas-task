<?php
namespace Routing;

use Catchers\Request;
use Helpers\ToolsKeeper as TK;
use Handlers\Exceptions\CustomException;

class Router
{

    /**
     * хранилище url страницы
     */
    private $url;

    /**
     * обработчик страницы
     */
    protected $handler = null;

    public function __construct()
    {
        // получаем данные запроса
        Request::prepare();

        // получаем url страницы
        $this->url = Request::$get['q'] ?? '';

    }

    /**
     * 
     */
    public function build() 
    {

        if (!is_null($this->handler)) {

            $namespace = [
				'Handlers\Controllers', 
				$this->handler['c']
            ];
            
            $class = implode("\\", $namespace);
            
            // пытаемся инициализировать объект обработчика
			try {

				$handler = new $class();

			} catch(Throwable $e) {

				throw new CustomException(202);

            }
            
            if (isset($handler) && $handler instanceof $class) {

                $method = $this->handler['m'];

                if (method_exists($handler, $method)) {

					// если метод существует то вызываем его обработку
					$handler->$method();

				} else {

					throw new CustomException(404);

				}

            } else {

                throw new CustomException(202);

            }

        } else {

            throw new CustomException(201);

        }

    }

    /**
     * 
     */
    public function prepare() 
    {

        // получаем хуки
        $hooks = TK::get('routing', 'hooks');

        if (count($hooks)) {

            // пробегаем по хукам и на основании текущего url выбираем обработчик
            foreach ($hooks as $hook) {
    
                if (
                    !empty($hook['m']) 
                    && preg_match($hook['m'], $this->url, $values)
                ) {
    
                    // устанавливаем обработчик страницы
                    $this->setHandler($hook);

                    // удаляем исходную строку из совпадений
                    array_shift($values);                   
    
                    // и если остаются еще какие то совпадения то продолжаем работу
                    if (count($values)) {
    
                        // предварительно определяем массив значений
                        $options = $values;
                        
                        // преобразуем регулярку для получения ключей из паттерна урла
                        $reg_Replace = preg_replace('/\((.[^\(\)]*)\)/', '{([\w_]*)}', $hook['m']);
    
                        if (
                            !empty($hook['l']) 
                            && preg_match($reg_Replace, $hook['l'], $keys)
                        ) {
    
                            // удаляем исходную строку из совпадений
                            array_shift($keys);
                            
                            // если кол-во совпадений одинаковое 
                            // то сопоставляем ключи совпадений со значениями
                            // и передаем в переменную опций
                            if (count($keys) == count($values)) {
    
                                $options = array_combine($keys, $values);
    
                            }
        
                        }
    
                        // добавляем опции страницы в инструментарий
                        TK::set($options, 'routing', 'options');
    
                    }
    
                    break;
    
                }
    
            }

        }

    }

    /**
     * 
     */
    private function setHandler(array $hook)
    {

        if (!empty($hook['h']['c']) && !empty($hook['h']['m'])) {

            $this->handler = $hook['h'];

        }

    }

}