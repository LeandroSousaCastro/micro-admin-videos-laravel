<?php

namespace Tests\Unit\Domain\Notification;

use Core\Seedwork\Domain\Notification\Notification;
use PHPUnit\Framework\TestCase;

class NotificationUnitTest extends TestCase
{
    public function testGetErrors()
    {
        $notification = new Notification();
        $errors = $notification->getErrors();

        $this->assertIsArray($errors);
        $this->assertEmpty($errors);
    }

    public function testAddErrors()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'video title is required'
        ]);

        $errors = $notification->getErrors();
        $this->assertIsArray($errors);
    }

    public function testHasErrors()
    {
        $notification = new Notification();
        $hasErrors = $notification->hasErrors();
        $this->assertFalse($hasErrors);
        $notification->addError([
            'context' => 'video',
            'message' => 'video title is required'
        ]);
        $hasErrors = $notification->hasErrors();
        $this->assertTrue($hasErrors);
    }

    public function testMessage()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'title title is required'
        ]);
        $messages = $notification->messages();
        $this->assertIsString($messages);
        $this->assertEquals(
            "video: title title is required,",
            $messages
        );
        $notification->addError([
            'context' => 'video',
            'message' => 'description title is required'
        ]);
        $messages = $notification->messages();
        $this->assertEquals(
            "video: title title is required,video: description title is required,",
            $messages
        );
    }

    public function testMessageFilterContext()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'title title is required'
        ]);
        $notification->addError([
            'context' => 'category',
            'message' => 'name title is required'
        ]);

        $this->assertCount(2, $notification->getErrors());

        $messages = $notification->messages(
            context: 'video'
        );
        $this->assertEquals(
            "video: title title is required,",
            $messages
        );
    }
}
