<?php

namespace Tests\FMT\InfrastructureBundle\Assumptions\Amazon;

use FMT\InfrastructureBundle\Service\AmazonS3\StorageInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\FMT\InfrastructureBundle\AbstractTest;

/**
 * Class AbstractStorageTest
 * @package Tests\FMT\InfrastructureBundle\Assumptions\Amazon
 */
abstract class AbstractStorageTest extends AbstractTest
{
    const FIXTURE_AVATAR_DIR = 'tests/FMT/DomainBundle/Fixtures/Avatar';
    const FIXTURE_AVATAR_NAME = 'avatar.png';
    const TEST_DOWNLOADED_NAME = 'downloadedAvatar.png';

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var string
     */
    protected static $avatarPath;

    /**
     * @var string
     */
    protected static $downloadedPath;

    public function setUp()
    {
        parent::setUp();

        $avatarStorage = $this->container->getParameter('avatar_storage');

        switch (true) {
            case strpos($avatarStorage, 'file') !== false && static::class === AmazonStorageTest::class:
            case strpos($avatarStorage, 's3') !== false && static::class === LocalStorageTest::class:
                $this->markTestSkipped(sprintf('%s skipped', static::class));
        }

        $this->storage = $this->container
            ->get('test.infrastructure.service.amazon.storage_factory')
            ->getInstance(
                $avatarStorage,
                $this->container->getParameter('s3_key'),
                $this->container->getParameter('s3_secret')
            );

        self::$avatarPath = sprintf('%s/%s', self::FIXTURE_AVATAR_DIR, self::FIXTURE_AVATAR_NAME);
        self::$downloadedPath = sprintf('%s/%s', self::FIXTURE_AVATAR_DIR, self::TEST_DOWNLOADED_NAME);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $absoluteTestAvatarPath = sprintf('%s/%s', getcwd(), self::$downloadedPath);
        if (file_exists($absoluteTestAvatarPath) && is_file($absoluteTestAvatarPath)) {
            unlink($absoluteTestAvatarPath);
        }
    }

    abstract public function testUpload(): void;

    abstract public function testGetFilePath(): void;

    abstract public function testUrl(): void;

    abstract public function testGlob(): void;

    abstract public function testDownload(): void;

    abstract public function testDelete(): void;

    /**
     * @param string $url
     * @return bool
     */
    protected function isRealUrl(string $url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code === Response::HTTP_OK;
    }
}
