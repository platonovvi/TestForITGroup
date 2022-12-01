<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidationTrait
{
    static private $error_messages = [
        'required' => 'Поле :attribute обязательно для заполнения.',
        'unique' => ':attribute уже используется.',
        'string' => ':attribute должен быть строкой.',
        'numeric' => ':attribute должен быть числом.',
        'mimes' => ':attribute должен быть картинкой.',
    ];

    static private $attribute_names = [
        'name' => '"Название"',
        'poster_link' => '"Ссылка"',
        'file' => '"Файл"',
        'status' => '"Статус"',
    ];

    static protected function validation(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules, self::$error_messages);
        $validator->setAttributeNames(self::$attribute_names);
        if ($validator->fails()) {
            return implode("\n", $validator->errors()->all());
        }
    }
}
