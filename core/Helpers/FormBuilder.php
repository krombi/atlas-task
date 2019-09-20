<?php
namespace Helpers;

use Viewer\Templater as Tpl;

final class FormBuilder
{

    private $fields = [];

    public function __construct(array $fields)
    {

        $this->fields = $fields;

    }

    /**
     * метод постройки формы
     */
    public function building(): array
    {

        $results = [];

        foreach ($this->fields as $field) {

            $body = null;

            if (!empty($field['type'])) {

                switch ($field['type']) {
                    case 'text': 
                    case 'email': {
                        $body = $this->textField($field);
                        break;
                    }
                    case 'radio':
                    case 'checkbox': {
                        $body = $this->chooseField($field);
                        break;
                    }
                    case 'select': {
                        $body = $this->selectField($field);
                        break;
                    }
                }

            }

            $results[] = Tpl::gen('elements/field', [
                'body' => $body
            ]);

        }

        return $results;

    }

    /**
     * метод генерации блока с текстовым полем
     */
    private function textField(array $data): ?string
    {

        # !!!!
        # провести ревизию
        # разбить итератор и строитель
        # !!!!

        $contain = [];
        $required = $this->fieldRequired($data);
        $bunch = "cf-field-{$data['ident']}";

        // получаем label поля
        if (isset($data['label'])) {

            $label_data = [
                'for' => $bunch,
                'class' => [
                    'title'
                ]
            ];
    
            $label_body = [];

            $label_body[] = $data['label'];

            if ($required) {
    
                $label_body[] = Tpl::create(
                    'span',
                    [
                        'class' => 'required'
                    ],
                    '*',
                    true
                );
    
            }
            
            $label = Tpl::create(
                'label',
                $label_data,
                implode(' ', $label_body),
                true
            );

            $contain[] = $label;

        }
        
        // далее составляем данные для обертки
        $wrapper_attr = [
            'class' => [
                'cf-input'
            ]
        ];

        $wrapper_classes = $this->fieldClasses($data);
        if (count($wrapper_classes)) {

            $wrapper_attr['class'] = array_merge_recursive($wrapper_attr['class'], $wrapper_classes);

        }

        $wrapper_body = [];

        // получаем тег инпута
        $input_attr = [
            'id' => $bunch,
            'class' => [
                'anim'
            ],
            'name' => $data['ident'],
            'type' => $data['type']
        ];

        // 
        $input_classes = $this->inputClasses($data);
        if (count($input_classes)) {

            $input_attr['class'] = array_merge_recursive($input_attr['class'], $input_classes);

        }

        // получаем placeholder инпута
        $placeholder = $this->fieldPlaceholder($data);

        // добавляем placeholder в атрибуты инпута
        if (!is_null($placeholder)) {

            $input_attr['placeholder'] = $placeholder;

        }

        // применяем маску для поля
        $input_mask = $this->fieldMask($data);

        if (!is_null($input_mask)) {

            $input_attr['data']['inputmask'] = "'mask':'$input_mask'";

        }

        // получаем доступность инпута
        $input_disabled = $this->fieldDisabled($data);
        if ($input_disabled) {

            $input_attr[] = 'disabled';

        }

        // создаем тег инпута
        $wrapper_body[] = Tpl::create('input', $input_attr);

        // получаем span подчеркивания
        $wrapper_body[] = Tpl::create(
            'span', 
            [
                'class' => [
                    'underline',
                    'anim'
                ]
            ], 
            null,
            true
        );

        // оборачиваем в див
        $contain[] = Tpl::create(
            'div',
            $wrapper_attr,
            $wrapper_body,
            true
        );

        return implode(PHP_EOL, $contain);

    }

    /**
     * метод генерации блока с чекбоксами и радио
     */
    private function chooseField(array $data): ?string
    {

        # !!!!
        # провести ревизию
        # разбить итератор и строитель
        # !!!!

        $contain = [];
        $required = $this->fieldRequired($data);

        if (!empty($data['v_listing']) && count($data['v_listing'])) {

            $values = $data['v_listing'];

            // получаем label поля
            if (isset($data['label'])) {
                
                $label_attr = [
                    'class' => [
                        'title'
                    ]
                ];
        
                $label_body = [
                    $data['label']
                ];

                if ($required) {
        
                    $label_body[] = Tpl::create(
                        'span',
                        [
                            'class' => [
                                'required'
                            ]
                        ],
                        '*',
                        true
                    );
        
                }
                
                $label = Tpl::create(
                    'div',
                    $label_attr,
                    implode(' ', $label_body),
                    true
                );

                $contain[] = $label;

            }

            // далее составляем данные для обертки
            $wrapper_attr = [
                'class' => [
                    'cf-list'
                ]
            ];

            $wrapper_classes = $this->fieldClasses($data);
            if (count($wrapper_classes)) {

                $wrapper_attr['class'] = array_merge_recursive($wrapper_attr['class'], $wrapper_classes);

            }

            $wrapper_body = [];

            foreach ($values as $value) {

                $input_wrapper_attr = [
                    'class' => [
                        "choose-{$value['type']}"
                    ]
                ];

                $input_wrapper_body = [];

                $input_name = $value['ident'];

                if ($value['type'] == 'checkbox') {
                    $input_name .= '[]';
                }

                $input_attr = [
                    'type' => $value['type'],
                    'name' => $input_name,
                    'value' => $value['v_ident']
                ];

                $input_wrapper_body[] = Tpl::create(
                    'input',
                    $input_attr
                );

                $input_wrapper_body[] = Tpl::create(
                    'span',
                    [
                        'class' => [
                            'makeup'
                        ]
                    ],
                    null,
                    true
                );

                $input_wrapper_body[] = Tpl::create(
                    'span',
                    [],
                    $value['v_label'],
                    true
                );

                $wrapper_body[] = Tpl::create(
                    'label',
                    $input_wrapper_attr,
                    $input_wrapper_body,
                    true
                );

            }

            $contain[] = Tpl::create(
                'div',
                $wrapper_attr,
                $wrapper_body,
                true
            );

        }

        // return Tpl::gen('elements/fields/choose', $data);
        return implode(PHP_EOL, $contain);

    }

    /**
     * метод генерации блока с селектом
     */
    private function selectField(array $data): ?string
    {

        # !!!!
        # провести ревизию
        # разбить итератор и строитель
        # !!!!

        $contain = [];
        $required = $this->fieldRequired($data);
        $bunch = "cf-field-{$data['ident']}";

        if (!empty($data['v_listing']) && count($data['v_listing'])) {

            $values = $data['v_listing'];

            // получаем label поля
            if (isset($data['label'])) {
                
                $label_attr = [
                    'for' => $bunch,
                    'class' => [
                        'title'
                    ]
                ];
        
                $label_body = [
                    $data['label']
                ];

                if ($required) {
        
                    $label_body[] = Tpl::create(
                        'span',
                        [
                            'class' => [
                                'required'
                            ]
                        ],
                        '*',
                        true
                    );
        
                }
                
                $label = Tpl::create(
                    'label',
                    $label_attr,
                    implode(' ', $label_body),
                    true
                );

                $contain[] = $label;

            }

            // далее составляем данные для обертки
            $wrapper_attr = [
                'class' => [
                    'cf-select'
                ]
            ];

            $wrapper_classes = $this->fieldClasses($data);
            if (count($wrapper_classes)) {

                $wrapper_attr['class'] = array_merge_recursive($wrapper_attr['class'], $wrapper_classes);

            }

            $wrapper_body = [];

            $select_attr = [
                'id' => $bunch,
                'name' => $data['ident'],
            ];

            $select_body = [];

            $select_default = $this->fieldDefault($data);
            if (!is_null($select_default)) {

                $select_body[] = Tpl::create(
                    'option',
                    [
                        'selected', 
                        'disabled'
                    ],
                    $select_default,
                    true
                );

            }

            foreach ($values as $value) {

                $option_attr = [
                    'value' => $value['v_ident']
                ];

                $select_body[] = Tpl::create(
                    'option',
                    $option_attr,
                    $value['v_label'],
                    true
                );

            }

            $wrapper_body[] = Tpl::create(
                'select',
                $select_attr,
                $select_body,
                true
            );

            $contain[] = Tpl::create(
                'div',
                $wrapper_attr,
                $wrapper_body,
                true
            );

        }

        return implode(PHP_EOL, $contain);

    }

    /**
     * 
     */
    private function getFeatures(array $data): array
    {

        $results = [];

        if (
            !empty($data['features']) 
            && $features = json_decode($data['features'], true)
        ) {

            $results = $features;

        }

        return $results;

    }

    /**
     * 
     */
    private function fieldPlaceholder(array $data): ?string
    {

        $result = null;

        if (!empty($data['features']['input']['placeholder'])) {

            $result = (string) $data['features']['input']['placeholder'];

        }

        return $result;

    }

    /**
     * 
     */
    private function fieldDisabled(array $data): bool
    {

        $results = false;

        if (!empty($data['features']['input']['disabled'])) {

            $results = (bool) $data['features']['input']['disabled'];

        }

        return $results;

    }

    /**
     * 
     */
    private function inputClasses(array $data): array
    {

        $result = [];

        if (!empty($data['features']['input']['attributes']['class'])) {

            $result = (array) $data['features']['input']['attributes']['class'];

        }

        return $result;

    }

    /**
     * 
     */
    private function fieldClasses(array $data): array
    {

        $result = [];

        if (!empty($data['features']['class'])) {

            $result = (array) $data['features']['class'];

        }

        return $result;

    }

    /**
     * 
     */
    private function fieldRequired(array $data): bool
    {

        $result = false;

        if (!empty($data['features']['required'])) {

            $result = (bool) $data['features']['required'];

        }

        return $result;

    }

    private function fieldDefault(array $data): ?string
    {
        
        $results = null;

        if (!empty($data['features']['select']['default'])) {

            $results = (string) $data['features']['select']['default'];
            
        }

        return $results;

    }

    private function fieldMask(array $data): ?string
    {

        $results = null;
        if (!empty($data['features']['input']['mask'])) {

            $results = (string) $data['features']['input']['mask'];

        }
        
        return $results;

    }

    /**
     * метод постройки древа из одномерного массива
     */
    private function buildTree(array $fields)
    {

        $tree = [];

        foreach ($fields as $field) {

            if (empty($tree[$field['id']])) {

                $tree[$field['id']] = $field;

            }

            if (!empty($field['v_parent'])) {

                $tree[$field['v_parent']]['v_listing'][] = $field;

            }

        }

        return $tree;

    }
    
}