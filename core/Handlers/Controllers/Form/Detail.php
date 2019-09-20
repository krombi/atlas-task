<?php
namespace Handlers\Controllers\Form;

use Catchers\Request;
use Helpers\{
    FormBuilder,
    ToolsKeeper as TK
};
use Viewer\Templater as Tpl;
use Helpers\Interfaces\IForm;

class Detail extends Primary implements IForm
{

    public function creating()
    {

        // инициализируем необходимую модель
        $this->initModel('Form\Recipient');

        // получаем массив с данными полей
        $this->prepareFields();

        if (Request::$is_post) {

            $results = [];
            $errors = [];

            foreach ($this->checkups as $name => $checkup) {

                $errors_messages = $checkup['errors'] ?? [];

                if (!empty($this->depends[$name])) {

                    foreach ($this->depends[$name] as $leading => $values) {

                        if (!empty(Request::$post[$leading])) {

                            $value = Request::$post[$leading];
                            if (isset($values[$value])) {

                                $features = $this->depends[$name][$leading][$value]['input'];
                                $checkup = array_replace_recursive($checkup, $features);

                            }

                        }

                    }

                }

                if (is_array($checkup)) {

                    foreach ($checkup as $parameter => $test) {

                        if ($parameter == 'required') {

                            if (empty(Request::$post[$name])) {
        
                                if (!empty($errors_messages[$parameter])) {
        
                                    $errors[] = $errors_messages[$parameter];
        
                                }
        
                            }
        
                        }

                        if ($parameter == 'compare') {

                            if (!empty(Request::$post[$name])) {

                                switch ($test) {
                                    case 'email': {
                                        if (!filter_var(Request::$post[$name], FILTER_VALIDATE_EMAIL)) {

                                            if (!empty($errors_messages['match'])) {
        
                                                $errors[] = $errors_messages['match'];
                    
                                            }

                                        }
                                        break;
                                    }
                                }

                            }

                        } else if ($parameter == 'match') {
            
                            $match = rawurldecode($test);
        
                            if (!empty(Request::$post[$name])) {
        
                                if (!preg_match("/$match/u", Request::$post[$name])) {
        
                                    if (!empty($errors_messages[$parameter])) {
        
                                        $errors[] = $errors_messages[$parameter];
            
                                    }
        
                                }
            
                            }
                            
                        }
        
                        if ($parameter == 'length') {
        
                            if (!empty(Request::$post[$name])) {
        
                                if (strlen(Request::$post[$name]) > $test) {
        
                                    if (!empty($errors_messages[$parameter])) {
        
                                        $errors[] = $errors_messages[$parameter];
            
                                    }
        
                                }
        
                            }
                            
                        }
        
                        if ($parameter == 'possible') {
        
                            if (!empty(Request::$post[$name])) {
                
                                $fit = true;

                                if (is_array(Request::$post[$name])) {

                                    foreach (Request::$post[$name] as $entry) {

                                        if (!in_array($entry, $test)) {

                                            $fit = false;

                                        }

                                    }

                                } else {

                                    if (!in_array(Request::$post[$name], $test)) {

                                        $fit = false;

                                    }

                                }
        
                                if (!$fit) {
        
                                    if (!empty($errors_messages[$parameter])) {
        
                                        $errors[] = $errors_messages[$parameter];
            
                                    }
        
                                }
        
                            }
        
                        }

                    }
                    
                }

            }

            if (!count($errors)) {

                $results['insert'] = $this->model->insert(Request::$post);

            } else {

                $results['errors'] = $errors;

            }

            if (Request::$is_ajax) {

                echo json_encode($results, JSON_UNESCAPED_UNICODE);
                exit;

            }

        }

        // подключаем строителя формы
        $builder = new FormBuilder($this->fields);

        // // добавлем блок с формой на страницу
        $this->view->add(
            Tpl::gen(
                'blocks/form',
                [
                    'fields' => $builder->building(),
                    'csrf' => Request::$csrf,
                    'unique' => randString(32)
                ]
            ),
            'aaa'
        );

        // запускаем генерацию страницы
        $this->showPage();

    }

    public function profile()
    {

        // инициализируем необходимую модель
        $this->initModel('Form\Recipient');

        $options = TK::get('routing', 'options') ?? [];
        $details = $this->model->userDetails($options);

        $this->showPage();

    }

}