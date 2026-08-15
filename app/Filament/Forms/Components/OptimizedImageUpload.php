<?php

namespace App\Filament\Forms\Components;

use App\Support\ImageOptimizer;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class OptimizedImageUpload
{
    public const MAX_SIZE_KB = 10240;

    public const ACCEPTED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public static function make(
        string $name,
        string $directory,
        string $profile = 'catalog',
        bool $withEditor = true,
    ): FileUpload {
        $upload = FileUpload::make($name)
            ->image()
            ->acceptedFileTypes(self::ACCEPTED_TYPES)
            ->maxSize(self::MAX_SIZE_KB)
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->helperText('JPG, JPEG, PNG, or WebP. Maximum 10 MB. Large images are resized automatically.')
            ->validationMessages([
                'mimetypes' => 'Please upload a JPG, JPEG, PNG, or WebP image.',
                'max' => 'The image must not be larger than 10 MB.',
            ])
            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) use ($directory, $profile): string {
                return app(ImageOptimizer::class)->optimize($file->getRealPath(), $directory, $profile)['path'];
            });

        return $withEditor ? $upload->imageEditor() : $upload;
    }
}
