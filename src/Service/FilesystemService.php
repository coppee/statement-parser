<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class FilesystemService
{
    /** @var LoggerInterface */
    private $logger;
    /** @var Filesystem */
    private $filesystem;

    public function __construct(LoggerInterface $logger, Filesystem $filesystem)
    {
        $this->logger = $logger;
        $this->filesystem = $filesystem;
    }

    public function allInputFiles(string $inputDirectory): array
    {
        if (!$this->filesystem->exists($inputDirectory)) {
            dump('input dir no exist: ' . $inputDirectory);
            return [];
        }

        $finder = new Finder();
        $finder->files()->in($inputDirectory);
        if (!$finder->hasResults()) {
            // logger
            dump('no result');
            return [];
        }

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        return $files;
    }
}
