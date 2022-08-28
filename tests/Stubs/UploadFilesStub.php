<?php

namespace Tests\Stubs;

use Core\Seedwork\Application\Interfaces\FileStorageInterface;

class UploadFilesStub implements FileStorageInterface
{
    /**
     * @param string $path
     * @param array $_FILES[file]
     */
    public function store(string $path, array $file): string
    {
        return "{$path}/test.mp4";
    }

    public function delete(string $path): void
    {
        // NO IMPLEMENTATION NEEDED
    }
}
