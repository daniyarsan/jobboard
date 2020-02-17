<?php

namespace Daniyarsan\Helpers;

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{

    /**
     * Returns content by path
     * @param string $path
     * @return string
     * @throws FileNotFoundException
     */

    public function getFileContent(string $path)
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException("File {$path} not found");
        }

        $content = file_get_contents($path);

        if (empty($content)) {
            throw new FileNotFoundException('Content is empty');
        }

        return $content;
    }

    /**
     * Checkes if directory exists
     * @param $dir
     * @return bool
     */
    public function dirCheck($dir)
    {
        return !is_dir($dir) ? false : true;
    }

    /**
     * Scan directory and get files of particular extension. If no extension - returns all files.
     *
     * @param $dir
     * @param bool $ext
     * @return array
     */
    public function getFilesInDirByExtension($dir, $extension = false)
    {
        $ext = '';
        if ($extension) {
            $ext .= '.' . $extension;
        }
        return glob($dir . '/[!~]*' . $ext);
    }

    /**
     * Uploads file to defined path
     *
     * @param UploadedFile $file
     * @param $path
     * @return string
     */
    public function uploadFileToPath(UploadedFile $file, $path)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($path, $fileName);
        } catch (FileException $e) {
            throw new FileException('File cant be uploaded');
        }

        return $fileName;
    }

}