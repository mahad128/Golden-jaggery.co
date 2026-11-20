<?php

use App\Services\CloudinaryImageUploader;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config([
        'services.cloudinary.cloud_name' => 'demo-cloud',
        'services.cloudinary.api_key' => 'test-key',
        'services.cloudinary.api_secret' => 'test-secret',
    ]);
});

it('uploads images with a signed Cloudinary request and returns the secure URL', function (): void {
    Http::fake([
        'api.cloudinary.com/*' => Http::response([
            'secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/v123/golden-jaggery/products/photo.jpg',
        ]),
    ]);

    $url = app(CloudinaryImageUploader::class)->upload(
        UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg'),
        'products',
    );

    expect($url)->toBe('https://res.cloudinary.com/demo-cloud/image/upload/v123/golden-jaggery/products/photo.jpg');

    Http::assertSent(function (Request $request): bool {
        $data = collect($request->data())
            ->filter(fn (array $part): bool => isset($part['name']))
            ->mapWithKeys(fn (array $part): array => [$part['name'] => $part['contents']])
            ->all();

        if (! isset($data['timestamp'], $data['signature'])) {
            return false;
        }

        $parameters = [
            'folder' => $data['folder'],
            'timestamp' => $data['timestamp'],
        ];
        ksort($parameters);
        $expectedSignature = sha1(implode('&', array_map(
            static fn (string $key, string $value): string => "{$key}={$value}",
            array_keys($parameters),
            array_values($parameters),
        )).'test-secret');

        return $request->url() === 'https://api.cloudinary.com/v1_1/demo-cloud/image/upload'
            && $data['api_key'] === 'test-key'
            && $data['folder'] === 'golden-jaggery/products'
            && $data['signature'] === $expectedSignature;
    });
});

it('deletes only assets belonging to the configured Cloudinary account', function (): void {
    Http::fake([
        'api.cloudinary.com/*' => Http::response(['result' => 'ok']),
    ]);

    $uploader = app(CloudinaryImageUploader::class);
    $uploader->delete('https://res.cloudinary.com/another-cloud/image/upload/v123/products/photo.jpg');

    Http::assertNothingSent();

    $uploader->delete('https://res.cloudinary.com/demo-cloud/image/upload/v123/golden-jaggery/products/photo.jpg');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.cloudinary.com/v1_1/demo-cloud/image/destroy'
        && $request->data()['public_id'] === 'golden-jaggery/products/photo'
        && $request->data()['api_key'] === 'test-key');
});

it('rejects upload URLs outside the configured Cloudinary account', function (): void {
    Http::fake([
        'api.cloudinary.com/*' => Http::response([
            'secure_url' => 'https://example.com/photo.jpg',
        ]),
    ]);

    app(CloudinaryImageUploader::class)->upload(
        UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg'),
        'products',
    );
})->throws(UnexpectedValueException::class, 'Cloudinary returned an invalid secure image URL.');

it('fails explicitly when Cloudinary credentials are missing', function (): void {
    config(['services.cloudinary.api_secret' => null]);

    app(CloudinaryImageUploader::class)->upload(
        UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg'),
        'products',
    );
})->throws(RuntimeException::class, 'Cloudinary is not configured.');
