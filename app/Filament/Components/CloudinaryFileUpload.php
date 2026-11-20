<?php

namespace App\Filament\Components;

use App\Services\CloudinaryImageUploader;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CloudinaryFileUpload
{
    public static function make(string $name, string $folder, bool $multiple = false): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->multiple($multiple)
            ->maxSize(10 * 1024)
            ->preventFilePathTampering()
            ->fetchFileInformation(false)
            ->getUploadedFileUsing(static function (string $file): array {
                $path = parse_url($file, PHP_URL_PATH);
                $path = is_string($path) ? $path : $file;

                return [
                    'name' => basename(rawurldecode($path)),
                    'size' => 0,
                    'type' => 'image/*',
                    'url' => $file,
                ];
            })
            ->saveUploadedFileUsing(
                static fn (TemporaryUploadedFile $file): string => app(CloudinaryImageUploader::class)->upload($file, $folder),
            )
            ->deleteUploadedFileUsing(static function (string|TemporaryUploadedFile $file): void {
                if (is_string($file)) {
                    app(CloudinaryImageUploader::class)->delete($file);
                }
            });
    }
}
