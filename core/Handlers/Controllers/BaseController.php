<?php
namespace Handlers\Controllers;

use Throwable;
use Handlers\Exceptions\CustomException;
use Helpers\ToolsKeeper as TK;
use Viewer\{
    Templater as Tpl,
    Compose
};

abstract class BaseController
{

    protected $page     = [];
    protected $options  = [];
    protected $model    = null;
    protected $view     = null;

    public function __construct()
    {

        // получаем опции страницы
        $this->options = TK::get('routing', 'options') ?? [];

        // подключаем вьюху
        $this->view = new Compose();

    }

    /**
     * 
     */
    protected function showPage()
    {

        // добавлем шапку сайта
        $this->view->add(
            Tpl::gen(
                'header',
                $this->page
            ),
            '000'
        );

        // добавляем подвал сайта
        $this->view->add(
            Tpl::gen('footer'),
            'zzz'
        );

        // генерируем страницу
        $this->view->build();

    }

    protected function jsonResults()
    {

        

    }

    /**
     * 
     */
    protected function initModel(string $model): void
    {

        if (preg_match("/^[a-zA-Z\d\_\\\]+$/", $model)) {

            $model_class = implode("\\", [
                'Handlers\Models',
                $model
            ]);
        
            try {

                $model = new $model_class();
    
            } catch (Throwable $e) {

                // в случае безуспешного подключения модели
                // выбрасываем исключение
                throw new CustomException(602);
                
            }

            // если успешно подключили модель
            if ($model instanceof $model_class) {

                $this->model = $model;

            }
            
        } else {

            throw new CustomException(601);

        }

    }

}