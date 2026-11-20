<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use UnexpectedValueException;

class CloudinaryImageUploader
{
    /**
     * @return array{cloud_name: string, api_key: string, api_secret: string}
     */
    private function credentials(): array
    {
        $credentials = [
            'cloud_name' => (string) config('services.cloudinary.cloud_name'),
            'api_key' => (string) config('services.cloudinary.api_key'),
            'api_secret' => (string) config('services.cloudinary.api_secret'),
        ];

        if (in_array('', $credentials, true)) {
            throw new RuntimeException('Cloudinary is not configured. Set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET.');
        }

        return $credentials;
    }

    public function upload(UploadedFile $file, string $folder): string
    {
        $credentials = $this->credentials();
        $parameters = [
            'folder' => 'golden-jaggery/'.trim($folder, '/'),
            'timestamp' => (string) now()->timestamp,
        ];
        $path = $file->getRealPath();

        if (! is_string($path)) {
            throw new RuntimeException('The uploaded image could not be read.');
        }

        $stream = fopen($path, 'rb');

        if ($stream === false) {
            throw new RuntimeException('The uploaded image could not be read.');
        }

        try {
            $response = Http::timeout(60)
                ->asMultipart()
                ->attach('file', $stream, $file->getClientOriginalName())
                ->post($this->endpoint($credentials['cloud_name'], 'image/upload'), [
                    ...$parameters,
                    'api_key' => $credentials['api_key'],
                    'signature' => $this->signature($parameters, $credentials['api_secret']),
                ]);
        } finally {
            fclose($stream);
        }

        $response->throw();

        $url = $response->json('secure_url');

        if (! is_string($url) || ! $this->isCloudinaryUrl($url, $credentials['cloud_name'])) {
            throw new UnexpectedValueException('Cloudinary returned an invalid secure image URL.');
        }

        return $url;
    }

    public function delete(string $url): void
    {
        $publicId = $this->publicIdFromUrl($url);

        if ($publicId === null) {
            return;
        }

        $credentials = $this->credentials();
        $parameters = [
            'public_id' => $publicId,
            'timestamp' => (string) now()->timestamp,
        ];

        $response = Http::timeout(30)
            ->asForm()
            ->post($this->endpoint($credentials['cloud_name'], 'image/destroy'), [
                ...$parameters,
                'api_key' => $credentials['api_key'],
                'signature' => $this->signature($parameters, $credentials['api_secret']),
            ]);

        $response->throw();

        if (! in_array($response->json('result'), ['ok', 'not found'], true)) {
            throw new UnexpectedValueException('Cloudinary did not confirm image deletion.');
        }
    }

    private function publicIdFromUrl(string $url): ?string
    {
        $parts = parse_url($url);
        $cloudName = (string) config('services.cloudinary.cloud_name');

        if (
            ! is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || strtolower($parts['host'] ?? '') !== 'res.cloudinary.com'
            || $cloudName === ''
        ) {
            return null;
        }

        $segments = array_map('rawurldecode', explode('/', trim($parts['path'] ?? '', '/')));

        if (
            ($segments[0] ?? null) !== $cloudName
            || ($segments[1] ?? null) !== 'image'
            || ($segments[2] ?? null) !== 'upload'
        ) {
            return null;
        }

        $versionIndex = null;

        for ($index = 3; $index < count($segments); $index++) {
            if (preg_match('/^v\d+$/', $segments[$index])) {
                $versionIndex = $index;
                break;
            }
        }

        if ($versionIndex === null) {
            return null;
        }

        $publicIdSegments = array_slice($segments, $versionIndex + 1);

        if ($publicIdSegments === [] || in_array('..', $publicIdSegments, true)) {
            return null;
        }

        $lastSegment = array_key_last($publicIdSegments);
        $extension = pathinfo($publicIdSegments[$lastSegment], PATHINFO_EXTENSION);

        if ($extension !== '') {
            $publicIdSegments[$lastSegment] = substr(
                $publicIdSegments[$lastSegment],
                0,
                -(strlen($extension) + 1),
            );
        }

        $publicId = implode('/', $publicIdSegments);

        return $publicId === '' ? null : $publicId;
    }

    private function isCloudinaryUrl(string $url, string $cloudName): bool
    {
        $parts = parse_url($url);

        if (
            ! is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || strtolower($parts['host'] ?? '') !== 'res.cloudinary.com'
        ) {
            return false;
        }

        $segments = explode('/', trim($parts['path'] ?? '', '/'));

        return rawurldecode($segments[0] ?? '') === $cloudName;
    }

    /**
     * @param  array<string, string>  $parameters
     */
    private function signature(array $parameters, string $apiSecret): string
    {
        ksort($parameters);

        $query = implode('&', array_map(
            static fn (string $key, string $value): string => "{$key}={$value}",
            array_keys($parameters),
            array_values($parameters),
        ));

        return sha1($query.$apiSecret);
    }

    private function endpoint(string $cloudName, string $action): string
    {
        return 'https://api.cloudinary.com/v1_1/'.rawurlencode($cloudName).'/'.$action;
    }
}
