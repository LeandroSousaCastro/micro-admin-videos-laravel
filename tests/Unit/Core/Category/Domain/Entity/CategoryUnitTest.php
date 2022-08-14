<?php

namespace Tests\Unit\Core\Category\Domain\Entity;

use Core\Category\Domain\Entity\Category;
use Core\Seedwork\Domain\Exception\EntityValidationException;
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
        $uuid = Uuid::random()->__toString();
        $category = new Category(
            id: $uuid,
            name: 'Category',
            description: "Category description",
            isActive: true,
            createdAt: '2020-01-01 00:00:00',
        );

        $category->update(
            name: 'New Category',
            description: "New Category description",
        );

        $this->assertEquals($uuid, $category->id());
        $this->assertEquals('New Category', $category->name);
        $this->assertEquals("New Category description", $category->description);
    }

    public function testExceptionName()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage("The value must not be least than 2 characters");
        $category = new Category(
            name: 'a',
            description: "Category description",
            isActive: true
        );
    }

    public function testExceptionDescription()
    {
        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage("The value must not be greater than 255 characters");
        $category = new Category(
            name: str_repeat('a', 5),
            description: str_repeat('a', 256)
        );
    }
}
