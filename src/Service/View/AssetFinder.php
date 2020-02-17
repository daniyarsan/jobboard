<?php

namespace App\Service\View;

use Doctrine\Migrations\Tools\Console\Exception\DirectoryDoesNotExist;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetFinder
{
    public const UPLOADS_DIR = 'uploads';
    public const RESUMES_DIR = 'resumes';
    public const LOGOS_DIR = 'logos';
    public const AVATARS_DIR = 'avatars';

    public function getAvatarPath(string $fileName): string
    {
        return '/' . $this->getUploadsDir() . '/' . self::AVATARS_DIR . '/' . $fileName;
    }

    public function getLogoPath(string $fileName): string
    {
        return $this->getUploadsDir() . '/' . self::LOGOS_DIR . '/' . $fileName;
    }

    public function getImagePath(string $fileName): string
    {
        return $this->getUploadsDir() . '/' . self::AVATARS_DIR . '/' . $fileName;
    }

    public function getUploadsDir()
    {
        return self::UPLOADS_DIR;
    }


}