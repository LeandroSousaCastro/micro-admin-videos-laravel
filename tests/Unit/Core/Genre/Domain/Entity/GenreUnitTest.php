<?php

namespace Tests\Unit\Core\Genre\Domain\Entity;

use ArgumentCountError;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\Genre\Domain\Entity\Genre;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Seedwork\Domain\Exception\NotificationException;
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

    public function testActivated()
    {
        $category = new Genre(
            name: 'Action',
            isActive: false,
        );

        $this->assertFalse($category->isActive);
        $category->activate();
        $this->assertTrue($category->isActive);
    }

    public function testDeactivated()
    {
        $category = new Genre(
            name: 'Action',
            isActive: true,
        );

        $this->assertTrue($category->isActive);
        $category->deactivate();
        $this->assertFalse($category->isActive);
    }

    public function testUpdate()
    {
        $uuid = Uuid::random()->__toString();
        $category = new Genre(
            id: $uuid,
            name: 'Action'
        );

        $category->update(
            name: 'Drama'
        );

        $this->assertEquals($uuid, $category->id());
        $this->assertEquals('Drama', $category->name);
    }

    public function testEntityExceptionNotNull()
    {
        $this->expectException(ArgumentCountError::class);
        (new Genre());
    }

    public function testEntityExceptionMinLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('Genre: The Name minimum is 3,');
        (new Genre(name: 'A'));
    }

    public function testEntityExceptionMaxLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('Genre: The Name maximum is 255,');
        (new Genre(name: str_repeat('a', 256)));
    }

    public function testEntityExceptionUpdateNotNull()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('Genre: The Name is required,');
        $genre = new Genre(
            name: 'Action'
        );
        $genre->update();
    }

    public function testEntityExceptionUpdateMinLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('Genre: The Name minimum is 3,');
        $genre = new Genre(
            name: 'Action'
        );
        $genre->update(
            name: 'A'
        );
    }

    public function testEntityExceptionUpdateMaxLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('Genre: The Name maximum is 255,');
        $genre = new Genre(
            name: 'Action'
        );
        $genre->update(
            name: str_repeat('a', 256)
        );
    }

    public function testAddCategoryToGenre()
    {
        $genre = new Genre(
            name: 'Action'
        );

        $this->assertIsArray($genre->categoriesId);
        $this->assertEmpty($genre->categoriesId);

        $id = Uuid::random()->__toString();
        $id2 = Uuid::random()->__toString();
        $categoriesId = [$id, $id2];
        $genre = new Genre(
            name: 'Action',
            categoriesId: $categoriesId
        );

        $this->assertCount(2, $genre->categoriesId);

        $genre->removeCategory($id);
        $this->assertCount(1, $genre->categoriesId);
        $this->assertNotContains($id, $genre->categoriesId);
        $this->assertContains($id2, $genre->categoriesId);
    }
}
