<?php

namespace Tests\FMT\InfrastructureBundle\Assumptions\Amazon;

/**
 * Class LocalStorageTest
 * @package Tests\FMT\InfrastructureBundle\Assumptions\Amazon
 */
class LocalStorageTest extends AbstractStorageTest
{
    /**
     * @var string
     */
    private $webAvatarPath;

    public function setUp()
    {
        parent::setUp();

        $this->webAvatarPath = sprintf('%s/web/avatar/%s', getcwd(), self::FIXTURE_AVATAR_NAME);
    }

    /**
     * @throws \Exception
     */
    public function testUpload(): void
    {
        $this->assertFileNotExists($this->webAvatarPath);
        $this->assertNotFalse(
            $this->storage->upload(self::$avatarPath, self::FIXTURE_AVATAR_NAME)
        );
        $this->assertFileExists($this->webAvatarPath);
    }

    /**
     * @throws \Exception
     */
    public function testGetFilePath(): void
    {
        $this->assertEquals(
            $this->webAvatarPath,
            $this->storage->getFilePath(self::FIXTURE_AVATAR_NAME)
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
        $this->assertFileExists($this->webAvatarPath);
        $this->assertNotFalse(
            $this->storage->download(self::FIXTURE_AVATAR_NAME, self::$downloadedPath)
        );
        $this->assertFileExists(self::$downloadedPath);
    }

    /**
     * @throws \Exception
     */
    public function testDelete(): void
    {
        $this->assertFileExists($this->webAvatarPath);
        $this->assertTrue(
            $this->storage->delete(self::FIXTURE_AVATAR_NAME)
        );
        $this->assertFileNotExists($this->webAvatarPath);
    }
}
