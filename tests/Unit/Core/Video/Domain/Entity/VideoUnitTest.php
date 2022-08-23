<?php

namespace Tests\Unit\Core\Video\Domain\Entity;

use Core\Video\Domain\Entity\Video;
use Core\Seedwork\Domain\Exception\NotificationException;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Domain\Enum\MediaStatus;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Ramsey\Uuid\Uuid as RamseyUuid;
use PHPUnit\Framework\TestCase;

class VideoUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $video = new Video(
            id: new Uuid($uuid),
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            createdAt: new \DateTime('2022-08-22 13:30:09')
        );
        $this->assertNotEmpty($video->id());
        $this->assertNotInstanceOf(Uuid::class, $video->id());
        $this->assertEquals("Video", $video->title);
        $this->assertEquals("Video description", $video->description);
        $this->assertEquals(2021, $video->yearLaunched);
        $this->assertTrue($video->opened);
        $this->assertFalse($video->publish);
        $this->assertEquals('12', $video->rating->value);
        $this->assertEquals('2022-08-22 13:30:09', $video->createdAt());
    }

    public function testIdAndCreatedAt()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
        );
        $this->assertInstanceOf(Uuid::class, $video->id);
        $this->assertNotEmpty($video->id());
        $this->assertInstanceOf(\DateTime::class, $video->createdAt);
        $this->assertNotEmpty($video->createdAt());
    }

    public function testAddCategory()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false
        );

        $this->assertEmpty($video->categoriesId);

        $uuid = RamseyUuid::uuid4()->toString();
        $video->addCategoryId(categoryId: $uuid);

        $this->assertContains($uuid, $video->categoriesId);
    }

    public function testAddCategories()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            createdAt: new \DateTime('2022-08-22 13:30:09')
        );
        $id = RamseyUuid::uuid4()->toString();
        $id2 = RamseyUuid::uuid4()->toString();

        $video->addCategoryId(categoryId: $id);
        $video->addCategoryId(categoryId: $id2);
        $this->assertContains($id, $video->categoriesId);
        $this->assertContains($id2, $video->categoriesId);
        $this->assertCount(2, $video->categoriesId);
    }

    public function testRemoveCategories()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            createdAt: new \DateTime('2022-08-22 13:30:09')
        );
        $id = RamseyUuid::uuid4()->toString();
        $id2 = RamseyUuid::uuid4()->toString();

        $video->addCategoryId(categoryId: $id);
        $video->addCategoryId(categoryId: $id2);
        $this->assertContains($id, $video->categoriesId);
        $this->assertContains($id2, $video->categoriesId);
        $this->assertCount(2, $video->categoriesId);

        $video->removeCategoryId(categoryId: $id);
        $this->assertContains($id2, $video->categoriesId);
        $this->assertCount(1, $video->categoriesId);
    }

    public function testAddGenres()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            createdAt: new \DateTime('2022-08-22 13:30:09')
        );
        $id = RamseyUuid::uuid4()->toString();
        $id2 = RamseyUuid::uuid4()->toString();

        $video->addGenreId(genreId: $id);
        $video->addGenreId(genreId: $id2);
        $this->assertContains($id, $video->genresId);
        $this->assertContains($id2, $video->genresId);
        $this->assertCount(2, $video->genresId);
    }

    public function testRemoveGenres()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            createdAt: new \DateTime('2022-08-22 13:30:09')
        );
        $id = RamseyUuid::uuid4()->toString();
        $id2 = RamseyUuid::uuid4()->toString();

        $video->addGenreId(genreId: $id);
        $video->addGenreId(genreId: $id2);
        $this->assertContains($id, $video->genresId);
        $this->assertContains($id2, $video->genresId);
        $this->assertCount(2, $video->genresId);

        $video->removeGenreId(genreId: $id);
        $this->assertContains($id2, $video->genresId);
        $this->assertCount(1, $video->genresId);
    }

    public function testAddCastMembers()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            createdAt: new \DateTime('2022-08-22 13:30:09')
        );
        $id = RamseyUuid::uuid4()->toString();
        $id2 = RamseyUuid::uuid4()->toString();

        $video->addCastMemberId(castMemberId: $id);
        $video->addCastMemberId(castMemberId: $id2);
        $this->assertContains($id, $video->castMembersId);
        $this->assertContains($id2, $video->castMembersId);
        $this->assertCount(2, $video->castMembersId);
    }

    public function testRemoveCastMembers()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            createdAt: new \DateTime('2022-08-22 13:30:09')
        );
        $id = RamseyUuid::uuid4()->toString();
        $id2 = RamseyUuid::uuid4()->toString();

        $video->addCastMemberId(castMemberId: $id);
        $video->addCastMemberId(castMemberId: $id2);
        $this->assertContains($id, $video->castMembersId);
        $this->assertContains($id2, $video->castMembersId);
        $this->assertCount(2, $video->castMembersId);

        $video->removeCastMemberId(castMemberId: $id);
        $this->assertContains($id2, $video->castMembersId);
        $this->assertCount(1, $video->castMembersId);
    }

    public function testValueObjectImageThumbFile()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            thumbFile: new Image(
                path: 'path/image-filme-x.png'
            ),
        );

        $this->assertEquals('path/image-filme-x.png', $video->thumbFile()->path());
        $this->assertNotNull($video->thumbFile());
        $this->assertInstanceOf(Image::class, $video->thumbFile());
    }

    public function testValueObjectImageToThumbHalf()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            thumbHalf: new Image(
                path: 'path/image-filme-x.png'
            ),
        );

        $this->assertEquals('path/image-filme-x.png', $video->thumbHalf()->path());
        $this->assertNotNull($video->thumbHalf());
        $this->assertInstanceOf(Image::class, $video->thumbHalf());
    }

    public function testValueObjectImageBannerFile()
    {
        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            bannerFile: new Image(
                path: 'path/banner.png'
            ),
        );

        $this->assertEquals('path/banner.png', $video->bannerFile()->path());
        $this->assertNotNull($video->bannerFile());
        $this->assertInstanceOf(Image::class, $video->bannerFile());
    }

    public function testValueObjectMediaTrailerFile()
    {
        $trailerFile = new Media(
            filePath: 'path/trailer.mp4',
            mediaStatus: MediaStatus::PENDING,
            encodedPath: 'path/encoded.extension'
        );

        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            trailerFile: $trailerFile
        );

        $this->assertEquals('path/trailer.mp4', $video->trailerFile()->filePath);
        $this->assertEquals(MediaStatus::PENDING, $video->trailerFile()->mediaStatus);
        $this->assertEquals('path/encoded.extension', $video->trailerFile()->encodedPath);
        $this->assertNotNull($video->trailerFile());
        $this->assertInstanceOf(Media::class, $video->trailerFile());
    }

    public function testValueObjectMediaVideoFile()
    {
        $videoFile = new Media(
            filePath: 'path/video.mp4',
            mediaStatus: MediaStatus::PENDING,
            encodedPath: 'path/encoded.extension'
        );

        $video = new Video(
            title: "Video",
            description: "Video description",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            videoFile: $videoFile
        );

        $this->assertEquals('path/video.mp4', $video->videoFile()->filePath);
        $this->assertEquals(MediaStatus::PENDING, $video->videoFile()->mediaStatus);
        $this->assertEquals('path/encoded.extension', $video->videoFile()->encodedPath);
        $this->assertNotNull($video->videoFile());
        $this->assertInstanceOf(Media::class, $video->videoFile());
    }

    public function testExceptions()
    {
        $this->expectException(NotificationException::class);
        (new Video(
            title: 'aa',
            description: "Vi",
            yearLaunched: 2021,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
        ));
    }
}
