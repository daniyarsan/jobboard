<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{

    protected $settings;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->settings = $parameterBag;
    }

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

    public function dirCheck($dir)
    {
        return !is_dir($dir) ? false : true;
    }

    public function getFilesInDirByExtension($dir, $extension = false)
    {
        $ext = '';
        if ($extension) {
            $ext .= '.' . $extension;
        }
        return glob($dir . '/[!~]*' . $ext);
    }

    public function upload(UploadedFile $file, $dir)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($dir, $fileName);
        } catch (FileException $e) {
            throw new FileException('File cant be uploaded');
        }

        return $fileName;
    }

    public function uploadLogo(UploadedFile $file)
    {
        return $this->upload($file, $this->settings->get('logos_dir'));
    }

    public function uploadResume(UploadedFile $file)
    {
        return $this->upload($file, $this->settings->get('resumes_dir'));
    }

}