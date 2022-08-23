<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Entity\Entity;
use Illuminate\Support\Facades\Validator;

class LaravelValidator implements ValidatorInterface
{
    public function validate(Entity $entity, string $context, array $rules): void
    {
        $data = $entity->toArray();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $error) {
                $entity->notification->addError([
                    'context' => $context,
                    'message' => $error[0]
                ]);
            }
        }
    }
}
