<?php

namespace Tests\Unit\Core\CastMember\Domain\Entity;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\CastMember\Domain\Entity\CastMember;
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Seedwork\Domain\Exception\EntityValidationException;
use PHPUnit\Framework\TestCase;

class CastMemberUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = (string) RamseyUuid::uuid4();
        $castMember = new CastMember(
            id: new Uuid($uuid),
            name: 'Name',
            type: CastMemberType::ACTOR,
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
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('Should not be empty or null');
        (new CastMember(name: 'Name'));
    }

    public function testEntityExceptionMotNull()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('Should not be empty or null');
        (new CastMember());
    }

    public function testEntityExceptionMinLength()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('The value must not be least than 2 characters');
        (new CastMember(name: 'A'));
    }

    public function testEntityExceptionMaxLength()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('The value must not be greater than 255 characters');
        (new CastMember(name: str_repeat('a', 256)));
    }

    public function testUpdate()
    {
        $uuid = Uuid::random()->__toString();
        $castMember = new CastMember(
            id: $uuid,
            name: 'Name',
            type: CastMemberType::DIRECTOR
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
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('The value must not be least than 2 characters');
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
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage('The value must not be greater than 255 characters');
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::DIRECTOR
        );
        $castMember->update(
            name: str_repeat('a', 256)
        );
    }
}
