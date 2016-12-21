<?php
/**
 * Copyright (c) new frontiers Software GmbH
 */

namespace NewFrontiers\Images;

class Image
{

    const ALIGN_LEFT = 0;
    const ALIGN_RIGHT = 1;

    const COLOR_BLACK = 0;
    const COLOR_GRAY = 1;

    const FONT_SANS = 'DejaVuSans.ttf';

    /**
     * @var resource
     */
    protected $src;

    /**
     * @param resource $src
     * @return $this
     */
    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }

    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->src);
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->src);
    }


    /**
     *
     */
    public function __destruct()
    {
        imagedestroy($this->src);
    }

    /**
     * @param $filename
     * @return Image
     * @throws \InvalidArgumentException
     */
    public static function fromFile($filename)
    {

        if (!file_exists($filename)) {
            throw new \InvalidArgumentException($filename . ' was not found');
        }

        $temp = new Image();

        if (strpos($filename, '.png') !== false) {
            $temp->setSrc(imagecreatefrompng($filename));
        } elseif (strpos($filename, '.gif') !== false) {
            $temp->setSrc(imagecreatefromgif($filename));
        } else {
            $temp->setSrc(imagecreatefromjpeg($filename));
        }

        return $temp;
    }

    /**
     * @param $resource
     * @return Image
     */
    public static function fromResource($resource)
    {
        $temp = new Image();
        $temp->setSrc($resource);
        return $temp;
    }


    /**
     * @param $filename
     */
    public function saveToFile($filename)
    {
        $pathinfo = pathinfo($filename);

        if ($pathinfo['extension'] === 'png') {
            imagepng($this->src, $filename);
        } elseif ($pathinfo['extension'] === 'gif') {
            imagegif($this->src, $filename);
        } else {
            imagejpeg($this->src, $filename);
        }
    }


    /**
     * @param $modifier
     */
    public function brightness($modifier)
    {
        if (!is_numeric($modifier)) {
            throw new \InvalidArgumentException('modifier hast to be int (-255 < x < 255)');
        }

        if ($modifier !== 0) {
            imagefilter($this->src, IMG_FILTER_BRIGHTNESS, $modifier);
        }
    }

    /**
     * @param $modifier
     */
    public function contrast($modifier)
    {
        if ($modifier !== 0) {
            imagefilter($this->src, IMG_FILTER_CONTRAST, $modifier * -1);
        }
    }

    /**
     * @param $red
     * @param $green
     * @param $blue
     */
    public function colorize($red, $green, $blue)
    {
        if (($red !== 0) || ($green !== 0) || ($blue !== 0)) {
            imagefilter($this->src, IMG_FILTER_COLORIZE, $red, $green, $blue);
        }
    }

    /**
     * @param $angle
     * @return Image
     */
    public function rotate($angle)
    {
        $dest = $this->src;
        if ($angle !== 0) {
            $dest = imagerotate($this->src, 360 - $angle, 0);
        }
        return Image::fromResource($dest);
    }

    /**
     * @param $width
     * @param $height
     * @return Image
     */
    public function resizeTo($width, $height)
    {
        $dest = imagecreatetruecolor($width, $height);
        imagecopyresampled($dest, $this->src, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

        return Image::fromResource($dest);
    }

    /**
     * @param $maxLength
     * @return Image
     */
    public function resizeToMax($maxLength)
    {

        $width = imagesx($this->src);
        $height = imagesy($this->src);

        if ($width > $height) {
            $ratio = $maxLength / $width;
        } else {
            $ratio = $maxLength / $height;
        }

        return $this->resizeTo(round($width * $ratio), round($height * $ratio));
    }

    /**
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     * @param $newWidth
     * @param $newHeight
     * @return Image
     */
    public function crop($x, $y, $width, $height, $newWidth, $newHeight)
    {
        $dest = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($dest, $this->src, 0, 0, $x, $y, $newWidth, $newHeight, $width, $height);

        return Image::fromResource($dest);
    }

    /**
     * @param $length
     * @return Image
     */
    public function cropSquare($length)
    {

        $width = imagesx($this->src);
        $height = imagesy($this->src);


        if ($width < $height) {
            $ratio = $length / $width;
            $originalLength = $length / $ratio;

            $offsetX = 0;
            $offsetY = round(($height - $originalLength) / 2);
        } else {
            $ratio = $length / $height;
            $originalLength = $length / $ratio;

            $offsetY = 0;
            $offsetX = round(($width - $originalLength) / 2);
        }

        $dest = imagecreatetruecolor($length, $length);
        imagecopyresampled(
            $dest,
            $this->src,
            0,
            0,
            $offsetX,
            $offsetY,
            $length,
            $length,
            $originalLength,
            $originalLength
        );
        return Image::fromResource($dest);
    }


    /**
     * (x,y) is the lower left corner!
     *
     * @param int $size
     * @param int $angle
     * @param int $x
     * @param int $y
     * @param int $col
     * @param string $fontfile
     * @param string $text
     * @return Image
     */
    public function text($size, $angle, $x, $y, $col, $fontfile, $text, $align = self::ALIGN_LEFT)
    {
        // RIGHT-ALIGN
        if ($align === self::ALIGN_RIGHT) {
            $dimensions = imagettfbbox($size, $angle, $fontfile, $text);
            $textWidth = abs($dimensions[4] - $dimensions[0]);
            $x = $this->getWidth() - $textWidth - $x;
        }

        imagettftext($this->src, $size, $angle, $x, $y, $this->getColorByConst($col), $fontfile, $text);

        return $this;
    }


    /**
     * @param Image $src
     * @param int $x
     * @param int $y
     * @return Image
     */
    public function addLayer(Image $src, $x, $y)
    {
        $srcX = 0;
        $srcY = 0;
        $srcWidth = $src->getWidth();
        $srcHeight = $src->getHeight();

        imagecopy($this->src, $src->getSrc(), $x, $y, $srcX, $srcY, $srcWidth, $srcHeight);

        return $this;
    }

    /**
     * @param Image $src
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return Image
     */
    public function addAndResizeLayer(Image $src, $x, $y, $width, $height)
    {
        if (($src->getWidth() !== $width) || ($src->getHeight() !== $height)) {
            $src = $src->resizeTo($width, $height);
        }

        return $this->addLayer($src, $x, $y);
    }

    /**
     * @param $const
     * @return int
     */
    private function getColorByConst($const)
    {
        if ($const === self::COLOR_BLACK) {
            return imagecolorallocate($this->src, 0, 0, 0);
        } elseif ($const === self::COLOR_GRAY) {
            return imagecolorallocate($this->src, 128, 128, 128);
        }
        return 0;
    }

    /**
     * @param $color
     * @return $this
     */
    public function rectBorder($color)
    {
        $allocatedColor = $this->getColorByConst($color);

        imagerectangle($this->src, 0, 0, $this->getWidth() - 1, $this->getHeight() - 1, $allocatedColor);

        return $this;
    }
}
