<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageOptimizer
{
    public const PROFILES = [
        'logo' => ['max_edge' => 1000, 'quality' => 88, 'preserve_format' => true],
        'photography' => ['max_edge' => 1920, 'quality' => 84, 'preserve_format' => false],
        'catalog' => ['max_edge' => 1600, 'quality' => 84, 'preserve_format' => false],
    ];

    /** @return array{path: string, width: int, height: int, mime: string} */
    public function optimize(string $sourcePath, string $destinationDirectory, string $profile): array
    {
        $settings = self::PROFILES[$profile] ?? throw new RuntimeException("Unknown image profile [{$profile}].");
        $imageInfo = @getimagesize($sourcePath);
        $mime = $imageInfo['mime'] ?? null;

        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException('The uploaded file must be a JPG, JPEG, PNG, or WebP image.');
        }

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/webp' => @imagecreatefromwebp($sourcePath),
        };

        if (! $image) {
            throw new RuntimeException('The uploaded image could not be read safely.');
        }

        try {
            if ($mime === 'image/jpeg') {
                $image = $this->applyExifOrientation($image, $sourcePath);
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $longEdge = max($width, $height);

            if ($longEdge > $settings['max_edge']) {
                $scale = $settings['max_edge'] / $longEdge;
                $newWidth = max(1, (int) round($width * $scale));
                $newHeight = max(1, (int) round($height * $scale));
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                $this->prepareTransparency($resized);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
                $width = $newWidth;
                $height = $newHeight;
            }

            $outputMime = $settings['preserve_format'] ? $mime : 'image/webp';
            $extension = match ($outputMime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                default => 'webp',
            };
            $relativePath = trim($destinationDirectory, '/') . '/' . Str::ulid() . '.' . $extension;
            $temporaryPath = tempnam(sys_get_temp_dir(), 'ghrayeb-image-');

            if ($temporaryPath === false) {
                throw new RuntimeException('A temporary image file could not be created.');
            }

            $stream = null;

            try {
                $saved = match ($outputMime) {
                    'image/jpeg' => imagejpeg($image, $temporaryPath, $settings['quality']),
                    'image/png' => imagepng($image, $temporaryPath, 7),
                    default => imagewebp($image, $temporaryPath, $settings['quality']),
                };

                if (! $saved) {
                    throw new RuntimeException('The optimized image could not be encoded.');
                }

                $stream = fopen($temporaryPath, 'rb');

                if ($stream === false || ! Storage::disk('public')->put($relativePath, $stream, 'public')) {
                    throw new RuntimeException('The optimized image could not be stored.');
                }
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }

                @unlink($temporaryPath);
            }

            return ['path' => $relativePath, 'width' => $width, 'height' => $height, 'mime' => $outputMime];
        } finally {
            imagedestroy($image);
        }
    }

    private function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        $orientation = @exif_read_data($path)['Orientation'] ?? 1;

        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip($image, $orientation === 4 ? IMG_FLIP_VERTICAL : IMG_FLIP_HORIZONTAL);
        }

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            5, 6 => imagerotate($image, -90, 0),
            7, 8 => imagerotate($image, 90, 0),
            default => false,
        };

        if ($rotated instanceof \GdImage) {
            imagedestroy($image);

            return $rotated;
        }

        return $image;
    }

    private function prepareTransparency(\GdImage $image): void
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $transparent);
    }
}
