<?php

namespace Tests\Unit\Core\Genre\Domain\Entity;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\Genre\Domain\Entity\Genre;
use Core\Seedwork\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class GenreUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = (string) RamseyUuid::uuid4();
        $genre = new Genre(
            id: new Uuid($uuid),
            name: 'Action',
            isActive: true,
            createdAt: new \DateTime('2021-01-01 00:00:00')
        );

        $this->assertEquals($uuid, $genre->id);
        $this->assertEquals('Action', $genre->name);
        $this->assertEquals(true, $genre->isActive);
        $this->assertEquals('2021-01-01 00:00:00', $genre->createdAt->format('Y-m-d H:i:s'));
    }

    public function testAttributesCreate()
    {
        $genre = new Genre(
            name: 'Action',
        );

        $this->assertNotEmpty($genre->id);
        $this->assertEquals('Action', $genre->name);
    }
}
