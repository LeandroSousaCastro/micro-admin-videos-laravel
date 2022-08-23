<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Exception\NotificationException;
use Rakit\Validation\Validator;

class RakitValidator implements ValidatorInterface
{
    public function validate(Entity $entity, string $context, array $rules): void
    {
        $data = $entity->toArray();

        $validation = (new Validator())->validate($data, $rules);

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $entity->notification->addError([
                    'context' => $context,
                    'message' => $error
                ]);
            }
            throw new NotificationException(
                $entity->notification->messages($context)
            );
        }
    }
}
