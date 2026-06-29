<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;

class ImageService
{
    const DEFAULT_MAX_DIMENSION = 2500;
    const THUMBNAIL_DIMENSIONS = ['width' => 320, 'height' => 180];
    const DEFAULT_QUALITY = 75;
    const THUMBNAIL_QUALITY = 65;

    public function processWithMemorySafety(UploadedFile $file, string $outputPath, array $options = []): array
    {
        ini_set('memory_limit', '768M');
        gc_disable();


        try {
            $tempPath = $file->getRealPath();
            $maxDimension = $options['max_dimension'] ?? self::DEFAULT_MAX_DIMENSION;

            // Créer le répertoire si nécessaire
            $this->ensureDirectoryExists($outputPath);

            $result = [
                'main' => $this->processMainImage($tempPath, $outputPath, $maxDimension, $options),
                'thumb' => $options['create_thumb'] ?? false
                    ? $this->processThumbnail($tempPath, $outputPath, $options)
                    : null
            ];

            return $result;
        } finally {
            gc_enable();
            gc_collect_cycles();
            if (isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    private function ensureDirectoryExists(string $outputPath): void
    {
        $directory = dirname(public_path($outputPath));
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    private function processMainImage(string $tempPath, string $outputPath, int $maxDimension, array $options): array
    {
        $image = Image::read($tempPath)
            ->setColorspace('rgb')
            ->scaleDown($maxDimension, $maxDimension);

        $format = $options['format'] ?? 'webp';
        $quality = $options['quality'] ?? self::DEFAULT_QUALITY;
        $fullPath = "{$outputPath}.{$format}";

        $this->saveOptimizedImage($image, $fullPath, $format, $quality);

        return [
            'path' => $fullPath,
            'url' => asset($fullPath),
            'width' => $image->width(),
            'height' => $image->height(),
            'size' => filesize(public_path($fullPath)),
            'format' => $format,
            'quality' => $quality
        ];
    }

    private function processThumbnail(string $tempPath, string $outputPath, array $options): array
    {
        $thumbOptions = array_merge(
            self::THUMBNAIL_DIMENSIONS,
            ['quality' => $options['thumb_quality'] ?? self::THUMBNAIL_QUALITY]
        );

        $image = Image::read($tempPath)
            ->setColorspace('rgb')
            ->cover($thumbOptions['width'], $thumbOptions['height']);

        $format = $options['format'] ?? 'webp';
        $fullPath = "{$outputPath}_thumb.{$format}";

        $this->saveOptimizedImage($image, $fullPath, $format, $thumbOptions['quality']);

        return [
            'path' => $fullPath,
            'url' => asset($fullPath),
            'width' => $thumbOptions['width'],
            'height' => $thumbOptions['height'],
            'size' => filesize(public_path($fullPath)),
            'format' => $format,
            'quality' => $thumbOptions['quality']
        ];
    }

    private function saveOptimizedImage($image, string $path, string $format, int $quality): void
    {
        $encoder = match ($format) {
            'webp' => new WebpEncoder($quality),
            'jpeg', 'jpg' => new JpegEncoder($quality),
            'png' => new PngEncoder(),
            default => new WebpEncoder($quality),
        };

        $image->encode($encoder)->save(public_path($path));
    }

    public function delete(string $path): bool
    {
        $absolutePath = public_path($path);

        if (file_exists($absolutePath)) {
            return unlink($absolutePath);
        }
        return false;
    }
}
