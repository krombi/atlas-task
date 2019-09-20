<?php
namespace Handlers\Models;

use Throwable;
use DB\Distributor as DB;
use Helpers\Interfaces\IDataBase;

abstract class BaseModel
{

    protected $db = null;

    public function __construct()
    {

        // инициализируем подключение к базе данных mysql
        try {

            $this->db = DB::init('MySQL');

        } catch (Throwable $e) {

            throw new CustomException(100);

        }

        if ($this->db instanceof IDataBase) {

            $this->db->connect();

        } else {

            throw new CustomException(100);

        }

    }

    /**
     * 
     */
    protected function getFromDB(string $sql): array
    {

        $results = [];

        // выполняем sql запрос
        $rights = $this->db->query($sql);

        // и если нам что то пришло
        // то именно это и возвращаем в контроллер
        if ($rights) {
            
            $results = $rights->fetchAll();

        }

        return $results;

    }

}