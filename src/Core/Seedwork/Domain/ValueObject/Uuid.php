<?php

namespace Core\Seedwork\Domain\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid extends ValueObject
{
    public function __construct(protected $value)
    {
        parent::__construct($value);
        $this->ensureIsValid($value);
    }

    public static function random(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public function ensureIsValid(string $id): void
    {
        if (!RamseyUuid::isValid($id)) {
            throw new \InvalidArgumentException(
                sprintf('<%s> does not allow the value <%s>', static::class, $id)
            );
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
