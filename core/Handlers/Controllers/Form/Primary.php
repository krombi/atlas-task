<?php
namespace Handlers\Controllers\Form;

use Handlers\Controllers\BaseController;

class Primary extends BaseController
{

    protected $fields = [];

    protected $checkups = [];
    protected $depends = [];

    public function prepareFields(): void
    {

        $this->fields = $this->model->getFields();

        $rights = $this->model->getRights();
        foreach ($rights as $right) {

            $this->fields[] = $right;

        }

        // добавляем пустое поле своего рода каптчу
        $this->fields[] = [
            'id' => 'captcha',
            'ident' => 'confirmed',
            'type' => 'text',
            'features' => '{"class":["invisible"],"input":{"length":0},"errors":{"length":"Поле должно оставаться пустым!"}}'
        ];

        $this->executeFields();

    }

    /**
     * метод постройки древа из одномерного массива
     */
    private function executeFields(): void
    {

        $tree = [];

        foreach ($this->fields as $field) {

            if (
                !empty($field['features']) 
                && $features = json_decode($field['features'], true)
            ) {
                $field['features'] =  $features;

            }

            if (empty($tree[$field['id']])) {

                $tree[$field['id']] = $field;

            }

            if (!empty($field['v_parent'])) {

                $tree[$field['v_parent']]['v_listing'][] = $field;

            }

        }

        // перезаписываем поля древом
        $this->fields = $tree;

        // после того как сгрупировали поля 
        // и составили древо поле: значения
        // обходим все выводимые поля
        foreach ($tree as $field) {

            $ident = $field['ident'] ?? 0;
            $checker = [
                $ident => []
            ];

            if (
                isset($field['features']['required']) 
                && is_bool($field['features']['required'])
            ) {

                $checker[$ident]['required'] = $field['features']['required'];

            }

            if (!empty($field['type'])) {

                switch ($field['type']) {
                    case 'checkbox':
                    case 'radio':
                    case 'select': {
                        if (
                            isset($field['v_listing']) 
                            && is_array($field['v_listing'])
                        ) {
                            $values = [];
                            foreach ($field['v_listing'] as $value) {
                                $values[] = $value['v_ident'];
                            }
                            $checker[$ident]['possible'] = $values;
                        }
                        break;
                    }
                    case 'text':
                    case 'email': {
                        if (!empty($field['features']['input'])) {
                            $data = $field['features']['input'];
                            $checkers = ['match', 'length', 'compare'];
                            foreach ($checkers as $key) {
                                if (isset($data[$key])) {
                                    $checker[$ident][$key] = $data[$key];
                                }
                            }
                        }
                    }
                }

            }

            if (!empty($field['features']['errors'])) {

                $checker[$ident]['errors'] = $field['features']['errors'];

            }
            
            $this->checkups = array_merge($this->checkups, $checker);

            if (!empty($field['features']['depends'])) {

                $this->depends[$field['ident']] = $field['features']['depends'];

            }

        }

    }

}