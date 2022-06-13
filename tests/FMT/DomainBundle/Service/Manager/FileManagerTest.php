<?php

namespace FMT\DomainBundle\Service\Manager;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserAvatar;
use FMT\DataBundle\Entity\UserProfile;
use FMT\InfrastructureBundle\Service\AmazonS3\LocalStorage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\FMT\InfrastructureBundle\AbstractTest;

/**
 * Class FileManagerTest
 * @package FMT\DomainBundle\Service\Manager
 * @coversDefaultClass FMT\DomainBundle\Service\Manager
 */
class FileManagerTest extends AbstractTest
{
    /**
     * @covers FileManager::generateUniqueFileName
     */
    public function testGenerateUniqueFileName()
    {
        $uniqueFileName = FileManager::generateUniqueFileName('test');
        $uniqueFileName2 = FileManager::generateUniqueFileName('test');

        $this->assertContains('test', $uniqueFileName);
        $this->assertContains('test', $uniqueFileName2);
        $this->assertNotEquals($uniqueFileName, $uniqueFileName2);
    }

    /**
     * @dataProvider dataProvider
     * @param User $user
     * @param LocalStorage $avatarStorage
     * @param $oldAvaName
     * @covers FileManager::UploadAvatar
     */
    public function testUploadAvatar(User $user, LocalStorage $avatarStorage, $oldAvaName)
    {
        $fileManager = new FileManager($avatarStorage);
        $uploadFile = new UploadedFile(__FILE__, basename(__FILE__), null, null, null, true);
        $uploadedFile = $fileManager->uploadAvatar($user, $uploadFile, $oldAvaName);
        $this->assertNotEmpty($uploadedFile);
    }

    /**
     * @dataProvider dataProviderWithException
     * @param User $user
     * @param LocalStorage $avatarStorage
     * @param $oldAvaName
     * @expectedException \Exception
     * @covers FileManager::UploadAvatar
     */
    public function testUploadAvatarWithException(User $user, LocalStorage $avatarStorage, $oldAvaName)
    {
        $fileManager = new FileManager($avatarStorage);
        $uploadFile = new UploadedFile(__FILE__, basename(__FILE__), null, null, null, true);
        $fileManager->uploadAvatar($user, $uploadFile, $oldAvaName);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $result = [];

        // 1.
        $avatarStorageMock = $this->createMock(LocalStorage::class);
        $avatarStorageMock->method('upload')->willReturn(false);
        $avatarStorageMock->expects($this->never())->method('delete');
        $result[] = [
            $this->createUser(),
            $avatarStorageMock,
            null,
        ];

        // 2.
        $avatarStorageMock = $this->createMock(LocalStorage::class);
        $avatarStorageMock->method('upload')->willReturn(true);
        $avatarStorageMock->expects($this->once())->method('delete')->with('OldAvaName');
        $result[] = [
            $this->createUser(),
            $avatarStorageMock,
            'OldAvaName',
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function dataProviderWithException()
    {
        $result = [];
        $avatarStorageMockWithException = $this->createMock(LocalStorage::class);
        $avatarStorageMockWithException->method('upload')->willThrowException(new \Exception('test'));
        $result[] = [
            $this->createUser(),
            $avatarStorageMockWithException,
            null,
        ];

        return $result;
    }

    /**
     * @return User
     */
    private function createUser()
    {
        $userName = sprintf('test%d@test.test', microtime(true));
        $user = new User();
        $profile = new UserProfile();
        $profile->setAvatar(new UserAvatar());
        $user
            ->setLogin($userName)
            ->setPassword('123')
            ->setRoles([User::ROLE_INCOMPLETE_DONOR]);
        $user->setProfile($profile);

        return $user;
    }
}
