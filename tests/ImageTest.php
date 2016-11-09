<?php
/**
 * Copyright (c) new frontiers Software GmbH
 */

namespace NewFrontiers\Images;

class ImageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers NewFrontiers\Images\Image::fromFile
     */
    public function testFromFileNonExisting()
    {
        $this->expectException(\InvalidArgumentException::class);
        $image = Image::fromFile(__DIR__ . '/appdsdas.png');
    }

    /**
     * @covers NewFrontiers\Images\Image::getWidth
     * @covers NewFrontiers\Images\mage::getHeight
     */
    public function testGetDimension()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');

        $this->assertEquals(256, $image->getWidth());
        $this->assertEquals(256, $image->getHeight());
    }

    /**
     * @covers NewFrontiers\Images\Image::resizeTo
     */
    public function testResize()
    {
        $image = Image::fromFile(__DIR__ . '/app.png');

        $newImage = $image->resizeTo(512, 512);

        $this->assertEquals(512, $newImage->getWidth());
        $this->assertEquals(512, $newImage->getHeight());
    }

    /**
     * @covers NewFrontiers\Images\Image::resizeToMax
     */
    public function testResizeToMax()
    {
        $image = Image::fromFile(__DIR__ . '/a9f54a31915697.5666acd712a3c.jpg');

        $newImage = $image->resizeToMax(512);

        $this->assertEquals(512, $newImage->getWidth());
        $this->assertLessThan(512, $newImage->getHeight());
    }
}
