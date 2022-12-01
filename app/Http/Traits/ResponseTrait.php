<?php
namespace App\Http\Traits;
trait ResponseTrait
{
    protected function send_success(array $data = []): string
    {
        return json_encode(array_merge([
            'success' => true
        ],$data));
    }

    protected function send_error(string $message = null): string
    {
        return json_encode([
            'message' => $message ? $message : 'Неопознанная ошибка',
            'success' => false
        ]);
    }
}
