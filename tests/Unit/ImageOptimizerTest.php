<?php

namespace Tests\Unit;

use App\Filament\Forms\Components\OptimizedImageUpload;
use App\Support\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_large_landscape_jpg_is_resized_proportionally_and_converted_to_webp(): void
    {
        $source = $this->imageFile('jpeg', 2400, 1200);
        $result = app(ImageOptimizer::class)->optimize($source, 'hero', 'photography');

        $this->assertSame([1920, 960], [$result['width'], $result['height']]);
        $this->assertSame('image/webp', $result['mime']);
        $this->assertStringEndsWith('.webp', $result['path']);
        Storage::disk('public')->assertExists($result['path']);
        $this->assertSame([1920, 960], array_slice(getimagesize(Storage::disk('public')->path($result['path'])), 0, 2));

        @unlink($source);
    }

    public function test_small_jpg_is_optimized_without_upscaling(): void
    {
        $source = $this->imageFile('jpeg', 640, 480);
        $result = app(ImageOptimizer::class)->optimize($source, 'services', 'catalog');

        $this->assertSame([640, 480], [$result['width'], $result['height']]);
        $this->assertSame('image/webp', $result['mime']);
        Storage::disk('public')->assertExists($result['path']);
        @unlink($source);
    }

    public function test_portrait_png_and_webp_preserve_aspect_ratio(): void
    {
        foreach (['png', 'webp'] as $format) {
            $source = $this->imageFile($format, 1000, 2500);
            $result = app(ImageOptimizer::class)->optimize($source, 'gallery', 'photography');

            $this->assertSame([768, 1920], [$result['width'], $result['height']]);
            Storage::disk('public')->assertExists($result['path']);
            @unlink($source);
        }
    }

    public function test_catalog_profile_limits_oversized_phone_style_image(): void
    {
        $source = $this->imageFile('jpeg', 3000, 4000);
        $result = app(ImageOptimizer::class)->optimize($source, 'products/main', 'catalog');

        $this->assertSame([1200, 1600], [$result['width'], $result['height']]);
        @unlink($source);
    }

    public function test_small_logo_is_not_upscaled_and_keeps_its_format(): void
    {
        $source = $this->imageFile('png', 320, 120);
        $result = app(ImageOptimizer::class)->optimize($source, 'branding', 'logo');

        $this->assertSame([320, 120], [$result['width'], $result['height']]);
        $this->assertSame('image/png', $result['mime']);
        $this->assertStringEndsWith('.png', $result['path']);
        @unlink($source);
    }

    public function test_unsupported_file_is_rejected_and_existing_files_are_untouched(): void
    {
        Storage::disk('public')->put('existing/hero.jpg', 'existing image');
        $source = tempnam(sys_get_temp_dir(), 'invalid-image-');
        file_put_contents($source, 'not an image');

        try {
            app(ImageOptimizer::class)->optimize($source, 'hero', 'photography');
            $this->fail('An unsupported upload should be rejected.');
        } catch (RuntimeException $exception) {
            $this->assertSame('The uploaded file must be a JPG, JPEG, PNG, or WebP image.', $exception->getMessage());
        }

        Storage::disk('public')->assertExists('existing/hero.jpg');
        $this->assertSame('existing image', Storage::disk('public')->get('existing/hero.jpg'));
        @unlink($source);
    }

    public function test_shared_upload_field_exposes_consistent_validation_limits(): void
    {
        $upload = OptimizedImageUpload::make('image', 'testing');

        $this->assertSame(10240, $upload->getMaxSize());
        $this->assertSame(OptimizedImageUpload::ACCEPTED_TYPES, $upload->getAcceptedFileTypes());
    }

    private function imageFile(string $format, int $width, int $height): string
    {
        $path = tempnam(sys_get_temp_dir(), 'source-image-');
        $image = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($image, 180, 20, 20);
        imagefill($image, 0, 0, $color);

        match ($format) {
            'jpeg' => imagejpeg($image, $path, 90),
            'png' => imagepng($image, $path),
            'webp' => imagewebp($image, $path, 90),
        };

        imagedestroy($image);

        return $path;
    }
}
