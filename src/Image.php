<?php
/**
 * Copyright (c) new frontiers Software GmbH
 */

namespace NewFrontiers\Images;

class Image
{

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

        if ((!is_int($modifier)) || ((int)$modifier < -255) || ((int)$modifier > 255)) {
            throw new \InvalidArgumentException('Modifier must be an integer value between -255 and 255');
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
}
