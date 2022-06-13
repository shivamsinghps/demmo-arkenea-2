<?php

namespace Tests\FMT\InfrastructureBundle\Assumptions\Amazon;

/**
 * Class AmazonStorageTest
 * @package Tests\FMT\InfrastructureBundle\Assumptions\Amazon
 */
class AmazonStorageTest extends AbstractStorageTest
{
    public function testUpload(): void
    {
        $url = $this->storage->url(self::FIXTURE_AVATAR_NAME);

        $this->assertFalse(
            $this->isRealUrl($url)
        );
        $this->assertNotFalse(
            $this->storage->upload(self::$avatarPath, self::FIXTURE_AVATAR_NAME)
        );
        $this->assertTrue(
            $this->isRealUrl($url)
        );
    }

    /**
     * Same function as url()
     */
    public function testGetFilePath(): void
    {
        $this->assertTrue(
            $this->isRealUrl($this->storage->url(self::FIXTURE_AVATAR_NAME))
        );
    }

    public function testUrl(): void
    {
        $this->assertTrue(
            $this->isRealUrl($this->storage->url(self::FIXTURE_AVATAR_NAME))
        );
    }

    public function testGlob(): void
    {
        $partOfAvatarName = explode('.', self::FIXTURE_AVATAR_NAME)[0];
        $generator = $this->storage->glob($partOfAvatarName);

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertNotEmpty($generator->current());
        $this->assertEquals(self::FIXTURE_AVATAR_NAME, $generator->current());
    }

    public function testDownload(): void
    {
        $this->assertFileNotExists(self::$downloadedPath);
        $this->assertTrue(
            $this->isRealUrl(
                $this->storage->url(self::FIXTURE_AVATAR_NAME)
            )
        );
        $this->assertNotFalse(
            $this->storage->download(self::FIXTURE_AVATAR_NAME, self::$downloadedPath)
        );
        $this->assertFileExists(self::$downloadedPath);
    }

    public function testDelete(): void
    {
        $url = $this->storage->url(self::FIXTURE_AVATAR_NAME);

        $this->assertTrue(
            $this->isRealUrl($url)
        );
        $this->assertTrue(
            $this->storage->delete(self::FIXTURE_AVATAR_NAME)
        );
        $this->assertFalse(
            $this->isRealUrl($url)
        );
    }
}
