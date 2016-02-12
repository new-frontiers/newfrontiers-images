# newfrontiers-images

[![Latest Version on Packagist][ico-version]][link-packagist]

## Install

Via Composer

``` bash
$ composer require newfrontiers-images
```

## Usage

Load images from a file or an existing resource. Note: Resources are closed upon destruction of the 
object. 

``` php
$image = Image::fromFile($myImageFile);
$image = Image::fromResource($myImageResource);
```

Simple image manipulation functions like brightness and contrast are applied to the loaded image 
directly. More complex manipulations like rotation and cropping return a new Image instance leaving
the original image untouched. 

``` php
$originalImage = Image::fromFile('original.jpg');
$croppedImage = $originalImage->cropSquare(200);
$croppedImage->saveToFile('cropped.jpg');
```


## License

All rights reserved. You are NOT allowed to use this!

[ico-version]: https://img.shields.io/packagist/v/newfrontiers/images.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/newfrontiers/images
