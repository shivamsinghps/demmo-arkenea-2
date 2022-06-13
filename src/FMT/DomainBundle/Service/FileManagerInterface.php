<?php

namespace FMT\DomainBundle\Service;

use FMT\DataBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface FileManagerInterface
 * @package FMT\DomainBundle\Service
 */
interface FileManagerInterface
{
    /**
     * @param User $user
     * @param UploadedFile|null $clientFile
     * @param null $oldAvatarName
     * @return string|null
     */
    public function uploadAvatar(User $user, UploadedFile $clientFile = null, $oldAvatarName = null);

    /**
     * @param $fileName
     * @return string
     */
    public static function generateUniqueFileName($fileName);

    /**
     * @param User $user
     * @param UploadedFile $tempAvatar
     * @return void
     */
    public function uploadTempAvatar(User $user, UploadedFile $tempAvatar);

    /**
     * @param User $user
     * @return UploadedFile|null
     */
    public function getTempAvatar(User $user);

    /**
     * @param User $user
     * @return string|null
     */
    public function getTempAvatarFileName(User $user);
}
