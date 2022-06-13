<?php

namespace FMT\DomainBundle\Service\Manager;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserAvatar;
use FMT\DomainBundle\Service\FileManagerInterface;
use FMT\InfrastructureBundle\Service\AmazonS3\StorageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class FileManager
 * @package FMT\DomainBundle\Service\Manager
 */
class FileManager extends EventBasedManager implements FileManagerInterface
{
    /**
     * @var StorageInterface
     */
    private $avatarStorage;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * FileManager constructor.
     * @param StorageInterface $avatarStorage
     * @param SessionInterface $session
     */
    public function __construct(StorageInterface $avatarStorage, SessionInterface $session)
    {
        $this->avatarStorage = $avatarStorage;
        $this->session = $session;
    }

    /**
     * @param User $user
     * @param UploadedFile|null $clientFile
     * @param string|null $oldAvatarName
     * @return string|null
     */
    public function uploadAvatar(User $user, UploadedFile $clientFile = null, $oldAvatarName = null)
    {
        $avatar = $user->getProfile()->getAvatar();

        if (!$clientFile instanceof UploadedFile) {
            $avatar->setFilename($oldAvatarName);
            $this->deleteSessionTempAvatar($user);
            return null;
        }

        $fileExtension = $clientFile->getClientOriginalExtension();
        $destination = self::generateUniqueFileName($fileExtension);
        $avatar->setFilename($destination);
        $avatar->setStatus(UserAvatar::DEFAULT_AVATAR_STATUS);
        if ($this->avatarStorage->upload($clientFile, $destination) && $oldAvatarName) {
            $this->avatarStorage->delete($oldAvatarName);
        }
        $this->deleteSessionTempAvatar($user);

        return $destination;
    }

    /**
     * @param string $fileExtension
     * @return string
     */
    public static function generateUniqueFileName($fileExtension)
    {
        return sprintf('%s.%s', md5(uniqid(rand(), true)), $fileExtension);
    }

    /**
     * @param User $user
     * @param UploadedFile $tempAvatar
     * @return void
     */
    public function uploadTempAvatar(User $user, UploadedFile $tempAvatar)
    {
        $fileExtension = $tempAvatar->getClientOriginalExtension();
        $destination = self::generateUniqueFileName($fileExtension);

        if ($this->avatarStorage->upload($tempAvatar, $destination)) {
            $this->session->set($this->getTempAvatarSessionKey($user), [
                'file' => $destination,
                'originalName' => $tempAvatar->getClientOriginalName(),
            ]);
        }
    }

    /**
     * @param User $user
     * @return UploadedFile|null
     * @throws \Exception
     */
    public function getTempAvatar(User $user)
    {
        $tempAvatar = $this->getSessionTempAvatarData($user);
        if (empty($tempAvatar)) {
            return null;
        }
        $tempAvatarFile = $tempAvatar['file'];
        $filePath = $this->avatarStorage->getFilePath($tempAvatarFile);
        $tmpSavedFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($tempAvatarFile);
        copy($filePath, $tmpSavedFile);
        $pathInfo = pathinfo($tmpSavedFile);

        return new UploadedFile(
            $tmpSavedFile,
            $pathInfo['basename'],
            mime_content_type($tmpSavedFile),
            filesize($tmpSavedFile),
            0,
            true
        );
    }

    /**
     * @param User $user
     * @return string|null
     */
    public function getTempAvatarFileName(User $user)
    {
        $tempAvatar = $this->getSessionTempAvatarData($user);
        if (empty($tempAvatar)) {
            return null;
        }
        return $tempAvatar['originalName'] ?? null;
    }

    /**
     * @param User $user
     * @return string
     */
    private function getTempAvatarSessionKey(User $user): string
    {
        return sprintf('temp_avatar_user_id_%s', $user->getId());
    }

    /**
     * @param User $user
     * @return array|null
     */
    private function getSessionTempAvatarData(User $user)
    {
        return $this->session->get($this->getTempAvatarSessionKey($user)) ?: null;
    }

    /**
     * @param User $user
     */
    private function deleteSessionTempAvatar(User $user)
    {
        $this->session->remove($this->getTempAvatarSessionKey($user));
    }
}
