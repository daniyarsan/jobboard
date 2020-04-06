<?php

namespace App\Service\View;

use Doctrine\Migrations\Tools\Console\Exception\DirectoryDoesNotExist;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetFinder
{
    public const UPLOADS_DIR = '/uploads';
    public const RESUMES_DIR = '/resume';
    public const LOGOS_DIR = '/logo';
    public const AVATARS_DIR = '/avatar';

    public function getAvatarPath(string $fileName): string
    {
        return '/' . self::UPLOADS_DIR  . '/' . self::AVATARS_DIR . '/' . $fileName;
    }

    public function getLogoPath(string $fileName): string
    {
        return self::UPLOADS_DIR  . '/' . self::LOGOS_DIR . '/' . $fileName;
    }

    public function getImagePath(string $fileName): string
    {
        return self::UPLOADS_DIR  . '/' . self::AVATARS_DIR . '/' . $fileName;
    }

    public function getResumesPath(string $fileName)
    {
        return self::UPLOADS_DIR . self::RESUMES_DIR . '/' . $fileName;
    }
}