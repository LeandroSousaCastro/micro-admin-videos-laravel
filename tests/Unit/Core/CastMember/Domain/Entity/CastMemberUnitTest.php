<?php

namespace Tests\Unit\Core\CastMember\Domain\Entity;

use ArgumentCountError;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\CastMember\Domain\Entity\CastMember;
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Seedwork\Domain\Exception\EntityValidationException;
use Core\Seedwork\Domain\Exception\NotificationException;
use PHPUnit\Framework\TestCase;

class CastMemberUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = Uuid::random();
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::ACTOR,
            id: $uuid,
            createdAt: new \DateTime('2021-01-01 00:00:00')
        );

        $this->assertEquals($uuid, $castMember->id);
        $this->assertEquals('Name', $castMember->name);
        $this->assertEquals(CastMemberType::ACTOR, $castMember->type);
        $this->assertEquals('2021-01-01 00:00:00', $castMember->createdAt->format('Y-m-d H:i:s'));
    }

    public function testAttributesCreate()
    {
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::ACTOR
        );

        $this->assertNotEmpty($castMember->id);
        $this->assertEquals('Name', $castMember->name);
        $this->assertEquals(CastMemberType::ACTOR, $castMember->type);
    }

    public function testAttributesCreateWithEmptyType()
    {
        $this->expectException(ArgumentCountError::class);
        (new CastMember(name: 'Name'));
    }

    public function testEntityExceptionNotNull()
    {
        $this->expectException(ArgumentCountError::class);
        (new CastMember());
    }

    public function testEntityExceptionMinLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('CastMember: The Name minimum is 3,');
        (new CastMember(name: 'A', type: CastMemberType::DIRECTOR));
    }

    public function testEntityExceptionMaxLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('CastMember: The Name maximum is 255,');
        (new CastMember(name: str_repeat('a', 256), type: CastMemberType::ACTOR));
    }

    public function testUpdate()
    {
        $uuid = Uuid::random();
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::DIRECTOR,
            id: $uuid,
        );

        $castMember->update(
            name: 'New Name',
            type: CastMemberType::ACTOR
        );

        $this->assertEquals($uuid, $castMember->id());
        $this->assertEquals('New Name', $castMember->name);
        $this->assertEquals(CastMemberType::ACTOR, $castMember->type);
    }

    public function testEntityExceptionUpdateMinLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('CastMember: The Name minimum is 3,');
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::DIRECTOR
        );
        $castMember->update(
            name: 'A'
        );
    }

    public function testEntityExceptionUpdateMaxLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('CastMember: The Name maximum is 255,');
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::DIRECTOR
        );
        $castMember->update(
            name: str_repeat('a', 256)
        );
    }
}
