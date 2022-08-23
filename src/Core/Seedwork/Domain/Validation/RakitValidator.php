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

    public function validate(array $data, string $context, array $rules): void
    {
        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $this->notification->addError([
                    'context' => $context,
                    'message' => $error
                ]);
            }
            throw new NotificationException(
                $this->notification->messages($context)
            );
        }
    }
}
