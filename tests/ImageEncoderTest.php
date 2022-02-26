<?php

declare(strict_types=1);

use AapSoftware\VideoColor\exceptions\FileNotFoundException;
use AapSoftware\VideoColor\ImageEncoder;
use PHPUnit\Framework\TestCase;

class ImageEncoderTest extends TestCase
{
    /**
     * @dataProvider successProvider
     */
    public function testSuccess(string $image, string $expected): void
    {
        $encoder = new ImageEncoder($image);

        $this->assertStringEqualsFile($expected, $encoder->encode());
    }

    public function successProvider(): array
    {
        return [
            [__DIR__ . '/assets/1.jpg', __DIR__ . '/assets/1.data'],
            [__DIR__ . '/assets/2.jpg', __DIR__ . '/assets/2.data'],
        ];
    }

    public function testFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);

        new ImageEncoder('foo/bar');
    }
}
