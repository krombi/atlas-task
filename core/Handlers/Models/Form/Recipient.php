<?php
namespace Handlers\Models\Form;

use PDO;
use Handlers\Models\BaseModel;
use Catchers\Request;
use Helpers\{
    Crypt,
    Url
};

class Recipient extends BaseModel
{

    private $fields = [];
    private $rights = [];

    /**
     * метод получения списка полей из базы данных
     */
    public function getFields(): array
    {

        // составляем sql запрос
        $sql = "
            SELECT
                f.*,
                fv.id AS v_id,
                fv.field AS v_parent,
                fv.ident AS v_ident,
                fv.label AS v_label
            FROM
                at_fields as f
                LEFT JOIN
                    (
                        SELECT
                            id,
                            field,
                            ident,
                            label
                        FROM
                            at_field_values
                        WHERE
                            active
                        ORDER BY
                            sort ASC
                    ) AS fv
                    ON f.id = fv.field
            WHERE
                f.active
            ORDER BY 
                f.sort ASC
        ";

        $fields = $this->getFromDB($sql);

        foreach ($fields as $field) {

            $this->fields[$field['ident']] = [
                'id' => $field['id'],
                'alias' => $field['alias'] ?? 0
            ];

        }

        return $fields;

    }

    /**
     * метод получения списка прав из базы данных
     */
    public function getRights(): array
    {

        $results = [];

        // составляем sql запрос
        $sql = "
            SELECT
                *
            FROM
                at_rights
            WHERE
                active = 1
            ORDER BY
                sort ASC
        ";

        $rights = $this->getFromDB($sql);

        if (count($rights)) {

            // создаем макет поля
            $parent = [
                'id' => 'access',
                'ident' => 'rights',
                'type' => 'checkbox',
                'label' => 'Права доступа',
                'validator' => 'choose',
                'sort' => '1000000',
                'features' => '{"class":["list-block"],"errors":{"possible":"Недопустимое значение!"}}'
            ];

            foreach ($rights as $right) {

                $value = [
                    'v_id' => $right['id'] ?? null,
                    'v_parent' => 'access',
                    'v_ident' => $right['ident'] ?? null,
                    'v_label' => $right['label'] ?? null
                ];

                $results[] = array_merge($parent, $value);

                $this->rights[$right['ident']] = $right['id'];

            }
            
        }

        return $results;

    }
    
    /**
     * метод вставки данных в базу данных
     */
    public function insert(array $data): array
    {

        # !!!!
        # необходима ревизия
        # необходимо разбить не цепочку обязанностей
        # !!!!

        $results = [];
        $time = time();

        // 
        $maker = [
            'ip' => Request::$ip,
            'agent' => Request::$agent
        ];

        // 
        $maker_hash = hash("sha1", implode("", $maker));

        //
        $maker_id = 0;
        $maker_db = $this->db->query("SELECT id FROM at_makers WHERE hash = '{$maker_hash}'");
        if ($maker_db) {

            $maker_db = $maker_db->fetch();
            $maker_id = $maker_db['id'] ?? $maker_id;

        } else {

            $m_ip = $maker['ip'];
            $m_agent = $maker['agent'];

            $this->db->query("INSERT INTO at_makers(hash, ip, agent) VALUES ('$maker_hash','{$m_ip}','{$m_agent}')");

            $maker_id = $this->db->lastId();

        }

        # !!!!
        # следующая цепочка 'создание пользователя'
        # передаем идентификатор создателя
        # !!!!

        $user = [
            'created' => $time,
            'whose' => $maker_id
        ];

        $alias_key = null;
        $alias = null;
        // 
        if (count($this->fields)) {

            $alias_key = array_search(1, array_column($this->fields, 'alias'));

        }

        foreach ($data as $ident => $field) {

            if ($ident == $alias_key) {

                $alias = $this->transliterate($field);
                $user['alias'] = "'$alias'";
                break;

            }

        }

        $user_columns = implode(",", array_keys($user));
        $user_values = implode(",", $user);

        // создаем пользователя
        $this->db->query("INSERT INTO at_users($user_columns) VALUES ($user_values)");

        $user_id = $this->db->lastId();
        $user_ident = Crypt::encode($user_id);
        
        # !!!!
        # следующая цепочка 'запись полей о пользователе'
        # передаем идентификатор пользователя
        # 
        # вынести в отдельный метод добавления в базу что бы не дублировать
        # !!!!

        foreach ($data as $ident => $field) {

            if (isset($this->fields[$ident])) {

                $field_id = $this->fields[$ident]['id'];

                if (is_array($field)) {

                    foreach ($field as $sub) {

                        $this->db->query("INSERT INTO at_user_fields(created, user, field, content) VALUES ($time,$user_id,$field_id,'$sub')");

                    }

                } else {

                    $this->db->query("INSERT INTO at_user_fields(created, user, field, content) VALUES ($time,$user_id,$field_id,'$field')");

                }

            }

        }
        
        # !!!!
        # следующая цепочка 'создание пользователя'
        # передаем идентификатор создателя
        # !!!!
        $rights = $data['rights'];
        foreach ($rights as $right) {

            if (isset($this->rights[$right])) {

                $right_id = $this->rights[$right];
                if (is_numeric($right_id)) {

                    $this->db->query("INSERT INTO at_user_rights(user, permission, active) VALUES ($user_id,$right_id,1)");

                }

            }

        }
        
        # !!!!
        # следующая цепочка 'генерация урла'
        # передаем алиас и идентификатор пользователя
        # !!!!
        $results['url'] = Url::make('detail', [
            'alias' => $alias ?? 'user',
            'id' => $user_ident
        ]);
        
        
        return $results;

    }

    /**
     * метод получения данных пользователя
     * для детальной страницы
     */
    public function userDetails(array $options = []): array
    {

        # !!!!
        # необходима ревизия
        # необходимо разбить не цепочку обязанностей
        # !!!!
        $results = [];

        return $results;

    }

    /**
     * 
     */
    private function transliterate(string $string): string 
    {

        $symbols = [
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',    'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        ];

        $string = strtr($string, $symbols);
        $string = strtolower($string);
        $string = preg_replace('~[^-a-z0-9_]+~u', '-', $string);
        $string = trim($string, "-");

        return $string;

    }   

}