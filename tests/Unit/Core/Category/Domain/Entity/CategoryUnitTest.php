<?php

namespace Tests\Unit\Core\Category\Domain\Entity;

use ArgumentCountError;
use Core\Category\Domain\Entity\Category;
use Core\Seedwork\Domain\Exception\NotificationException;
use Core\Seedwork\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new Category(
            name: "Category",
            description: "Category description",
            isActive: true
        );
        $this->assertNotEmpty($category->id());
        $this->assertNotInstanceOf(Uuid::class, $category->id());
        $this->assertNotEmpty($category->createdAt());
        $this->assertEquals("Category", $category->name);
        $this->assertEquals("Category description", $category->description);
        $this->assertTrue($category->isActive);
    }

    public function testActivated()
    {
        $category = new Category(
            name: 'Category',
            isActive: false,
        );

        $this->assertFalse($category->isActive);
        $category->activate();
        $this->assertTrue($category->isActive);
    }

    public function testDeactivated()
    {
        $category = new Category(
            name: 'Category',
            isActive: true,
        );

        $this->assertTrue($category->isActive);
        $category->deactivate();
        $this->assertFalse($category->isActive);
    }

    public function testUpdate()
    {
        $uuid = Uuid::random();
        $category = new Category(
            name: 'Category',
            description: "Category description",
            isActive: true,
            id: $uuid,
            createdAt: new \DateTime('2020-01-01 00:00:00'),
        );

        $category->update(
            name: 'New Category',
            description: "New Category description",
        );

        $this->assertEquals($uuid->__toString(), $category->id());
        $this->assertEquals('New Category', $category->name);
        $this->assertEquals("New Category description", $category->description);
    }

    public function testExceptionNameMotNull()
    {
        $this->expectException(ArgumentCountError::class);
        (new Category());
    }

    public function testExceptionNameMinLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage("Category: The Name minimum is 3,");
        (new Category(
            name: 'a',
            description: "Category description",
            isActive: true
        ));
    }

    public function testExceptionNameMaxLength()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage("Category: The Name maximum is 255,");
        (new Category(
            name: str_repeat('a', 256),
            description: "Category description",
            isActive: true
        ));
    }

    public function testExceptionDescription()
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage("Category: The Description maximum is 255,");
        (new Category(
            name: str_repeat('a', 256),
            description: str_repeat('a', 256)
        ));
    }
}
