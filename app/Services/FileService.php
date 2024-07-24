<?php

namespace App\Services;

class FileService
{
    public function chunk(string $filePath, string $destinationFolder, int $chunkSize = 1024 * 1024): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $file = fopen($filePath, 'rb');

        $chunkNumber = 0;

        mkdir($destinationFolder);

        while (!feof($file)) {
            $chunkData = fread($file, $chunkSize);

            if ($chunkData) {
                $chunkFileName = $destinationFolder . DIRECTORY_SEPARATOR . "chunk_{$chunkNumber}.mp3";

                file_put_contents($chunkFileName, $chunkData);

                $chunkNumber++;
            }
        }

        fclose($file);
    }
}
