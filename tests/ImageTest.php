<?php
/**
 * Copyright (c) new frontiers Software GmbH
 */

namespace NewFrontiers\Images;

use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{

    /**
     * @covers \NewFrontiers\Images\Image::fromFile
     */
    public function testFromFileNonExisting()
    {
        $this->expectException(\InvalidArgumentException::class);
        Image::fromFile(__DIR__ . '/appdsdas.png');
    }

    /**
     * @covers \NewFrontiers\Images\Image::getWidth
     * @covers \NewFrontiers\Images\Image::getHeight
     * @covers \NewFrontiers\Images\Image::fromFile
     */
    public function testGetDimension()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');

        $this->assertEquals(256, $image->getWidth());
        $this->assertEquals(256, $image->getHeight());

        Image::fromFile(__DIR__ . '/nfs.gif');
        Image::fromFile(__DIR__ . '/a9f54a31915697.5666acd712a3c.jpg');
    }


    /**
     * @covers \NewFrontiers\Images\Image::resizeTo
     * @covers \NewFrontiers\Images\Image::fromResource
     * @covers \NewFrontiers\Images\Image::setSrc
     */
    public function testResize()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');

        $newImage = $image->resizeTo(512, 512);

        $this->assertEquals(512, $newImage->getWidth());
        $this->assertEquals(512, $newImage->getHeight());
    }

    /**
     * @covers \NewFrontiers\Images\Image::resizeToMax
     */
    public function testResizeToMax()
    {
        $image = Image::fromFile(__DIR__ . '/a9f54a31915697.5666acd712a3c.jpg');

        $newImage = $image->resizeToMax(512);

        $this->assertEquals(512, $newImage->getWidth());
        $this->assertLessThan(512, $newImage->getHeight());
    }

    /**
     * @covers \NewFrontiers\Images\Image::saveToFile
     */
    public function testSaveToFile()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');

        $image->saveToFile(__DIR__ . '/save.png');
        $image->saveToFile(__DIR__ . '/save.jpg');
        $image->saveToFile(__DIR__ . '/save.gif');

        // TODO: Check if files  are readible
        $this->assertFileExists(__DIR__ . '/save.png');
        $this->assertFileExists(__DIR__ . '/save.jpg');
        $this->assertFileExists(__DIR__ . '/save.gif');
    }

    /**
     * @covers \NewFrontiers\Images\Image::brightness
     */
    public function testBrightness()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');
        $image->brightness(100);

        $this->expectException(\InvalidArgumentException::class);
        $image->brightness('XYZ');
    }

    /**
     * @covers \NewFrontiers\Images\Image::contrast
     */
    public function testContrast()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');
        $image->contrast(100);

        $this->assertTrue($image->getHeight() > 0);
    }

    /**
     * @covers \NewFrontiers\Images\Image::colorize
     */
    public function testColorize()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');
        $image->colorize(100, 0, 0);

        $this->assertTrue($image->getHeight() > 0);
    }

    /**
     * @covers \NewFrontiers\Images\Image::rotate
     */
    public function testRotate()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');
        $image->rotate(100);

        $this->assertTrue($image->getHeight() > 0);
    }

    /**
     * @covers \NewFrontiers\Images\Image::crop
     */
    public function testCrop()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');
        $newImage = $image->crop(0, 0, 256, 256, 100, 100);

        $this->assertEquals(256, $image->getWidth());
        $this->assertEquals(256, $image->getHeight());
        $this->assertEquals(100, $newImage->getWidth());
        $this->assertEquals(100, $newImage->getHeight());
    }

    /**
     * @covers \NewFrontiers\Images\Image::cropSquare
     */
    public function testCropSquare()
    {
        $image = Image::fromFile(__DIR__ . '/a9f54a31915697.5666acd712a3c.jpg');
        $newImage = $image->cropSquare(100);

        $this->assertEquals(100, $newImage->getWidth());
        $this->assertEquals(100, $newImage->getHeight());
    }
}
