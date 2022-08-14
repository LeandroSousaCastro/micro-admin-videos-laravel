<?php

namespace Tests\Unit\Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Exception\EntityValidationException;
use Core\Seedwork\Domain\Validation\DomainValidation;
use PHPUnit\Framework\TestCase;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNullCustomMessage()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('custom message');
        $value = '';
        DomainValidation::notNull($value, 'custom message');
    }

    public function testNotNull()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('Should not be empty or null');
        $value = '';
        DomainValidation::notNull($value);
    }

    public function testStrMaxLengthCustomLengthAndCustomMessage()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('custom message');
        $value = str_repeat('a', 6);
        DomainValidation::strMaxLength($value, 5, 'custom message');
    }

    public function testStrMaxLength()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('The value must not be greater than 255 characters');
        $value = str_repeat('a', 256);
        DomainValidation::strMaxLength($value);
    }

    public function testStrMinLengthCustomLengthAndCustomMessage()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('custom message');
        $value = 'a';
        DomainValidation::strMinLength($value, 5, 'custom message');
    }

    public function testStrMinLength()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('The value must not be least than 2 characters');
        $value = 'a';
        DomainValidation::strMinLength($value);
    }

    public function testStrCanNullAndMaxLength()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('The value must not be greater than 3 characters');
        $value = str_repeat('a', 4);
        DomainValidation::strCanNullAndMaxLength($value, 3);
    }
}
