<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Exception\NotificationException;
use Core\Seedwork\Domain\Notification\Notification;
use Rakit\Validation\Validator;

class RakitValidator implements ValidatorInterface
{
    public function __construct(
        protected Notification $notification,
        protected Validator $validator
    ) {
    }

    public function validate(array $dataValidation): void
    {
        $validation = $this->validator->validate(
            $dataValidation['data'],
            $dataValidation['rules']
        );

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $this->notification->addError([
                    'context' => $dataValidation['context'],
                    'message' => $error
                ]);
            }
            throw new NotificationException(
                $this->notification->messages($dataValidation['context'])
            );
        }
    }
}
