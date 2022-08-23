<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Notification\Notification;
use Illuminate\Support\Facades\Validator;

class LaravelValidator implements ValidatorInterface
{
    public function __construct(
        protected Notification $notification,
        protected Validator $validator
    ) {
    }

    public function validate(array $data, string $context, array $rules): void
    {
        $validator = $this->validator::make($data, $rules);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $error) {
                $this->notification->addError([
                    'context' => $context,
                    'message' => current($error)
                ]);
            }
        }
    }
}
