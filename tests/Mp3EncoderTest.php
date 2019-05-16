<?php

namespace Cyve\AudioEncoder\Test;

use Cyve\AudioEncoder\Mp3Encoder;
use PHPUnit\Framework\TestCase;

class Mp3EncoderTest extends TestCase
{
    public function testEncodeUnexistingFile()
    {
        $this->expectException(\RuntimeException::class);

        $encoder = new Mp3Encoder();
        $encoder->encode(__DIR__.'/fixtures/notexist.mp3', 'foo.mp3');
    }

    public function testEncodeInvalidMimeType()
    {
        $this->expectException(\RuntimeException::class);

        $encoder = new Mp3Encoder();
        $encoder->encode(__DIR__.'/fixtures/foo.txt', 'foo.mp3');
    }

    public function testGetMimeType()
    {
        $method = new \ReflectionMethod(Mp3Encoder::class, 'getMimeType');
        $method->setAccessible(true);

        $this->assertEquals('audio/mpeg', $method->invoke(new Mp3Encoder(), __DIR__.'/fixtures/chewie.mp3'));
    }
}
