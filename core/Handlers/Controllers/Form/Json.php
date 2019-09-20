<?php
namespace Handlers\Controllers\Form;

class Json extends Primary {

    public function tools()
    {

        header('Content-Type: application/json');

        // инициализируем необходимую модель
        $this->initModel('Form\Recipient');

        // получаем массив с данными полей
        $this->prepareFields();

        $results = [
            'checkups' => $this->checkups,
            'depends' => $this->depends
        ];

        // print_r($results);
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        $this->jsonResults();

    }

}