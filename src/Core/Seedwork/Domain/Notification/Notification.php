<?php

namespace Core\Seedwork\Domain\Notification;

class Notification
{
    private $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param $error array[context, message]
     */
    public function addError(array $error): void
    {
        array_push($this->errors, $error);
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function messages(string $context = ''): string
    {
        $message = '';
        foreach ($this->errors as $key => $error) {
            if ($context == '' || $error['context'] == $context) {
                $message .= "{$error['context']}: {$error['message']},";
            }
        }

        return $message;
    }
}
