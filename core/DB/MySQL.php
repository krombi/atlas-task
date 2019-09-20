<?php
namespace DB;

use PDO, PDOException;
use Helpers\Interfaces\IDataBase;
use Handlers\Exceptions\CustomException;

class Mysql implements IDataBase
{

    /**
     * 
     */
    private $options = [];

    /**
     * 
     */
    private $db_data = null;

    /**
     * 
     */
    private $connection = null;

    public function __construct()
    {

        // задаем опции PDO для работы с базой данных
        $this->options = [
            PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES      => false
        ];

        // составляем строку с данными базы данных
        $this->db_data = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
        

    }

    public function connect()
    {

        // пытаемся подключиться к базе данных
        try {

            $this->connection = new PDO($this->db_data, DB_USER, DB_PASS, $this->options);

        } catch (PDOException $e) { 

            // и если при подключении возникли какие то проблемы
            // то вызываем исключкение ошибки
            throw new CustomException(101);

        }

    }

    public function query(string $sql = 'SELECT NOW()')
    {

        if ($this->connection instanceof PDO) {

            $results = $this->connection->query($sql);
    
            if ($results && $results->rowCount()) {
    
                return $results;
    
            }

        }

        return false;

    }

    public function lastId(string $key = ''): string
    {

        if ($this->connection instanceof PDO) {

            $result = $this->connection->lastInsertId($key);
            return $result;

        }

    }

}