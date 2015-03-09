<?php

namespace fvilpoix\Component\Resizator;

use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Manipulator
{
    public static function resize(ImageInterface $image, Format $format)
    {
        switch ($format->getType()) {
            case Format::FLAG_W: return self::resizeByWidth($image, $format);
            case Format::FLAG_H: return self::resizeByHeight($image, $format);
            case Format::FLAG_WH: return self::resizeByWidthHeight($image, $format);
        }
    }

    /**
     * Resize image by its width. Compute the height proportionaly to the width
     */
    protected static function resizeByWidth(ImageInterface $image, Format $format)
    {
        $expectedWidth = $format->getWidth();
        $size          = $image->getSize();
        $originalWidth = $size->getWidth();

        if ($originalWidth < $expectedWidth) {
            $box = $size;
        } else {
            $expectedHeight = (int) ($expectedWidth * $size->getHeight() / $size->getWidth());
            $box = new Box($expectedWidth, $expectedHeight);
        }

        return static::doResize($image, $box);
    }

    /**
     * Resize image by its height. Compute the witdh proportionaly to the height
     */
    protected static function resizeByHeight(ImageInterface $image, Format $format)
    {
        $expectedHeight = $format->getHeight();
        $size           = $image->getSize();
        $originalHeight = $size->getHeight();

        if ($originalHeight < $expectedHeight) {
            $box = $size;
        } else {
            $expectedWidth = (int) ($expectedHeight * $size->getWidth() / $size->getHeight());
            $box = new Box($expectedWidth, $expectedHeight);
        }

        return static::doResize($image, $box);
    }

    /**
     * Force the width and the height
     */
    protected static function resizeByWidthHeight(ImageInterface $image, Format $format)
    {
        $box = new Box($format->getWidth(), $format->getHeight());

        return static::doResize($image, $box);
    }

    protected static function doResize(ImageInterface $image, Box $box)
    {
        return $image->resize($box);
    }

    private static function isJpeg($mime)
    {
        return  $mime == 'image/jpg' || $mime == 'image/jpeg';
    }

    public static function rotatePicture($filePath)
    {
        if (self::isJpeg($filePath)) {
            $exif = exif_read_data($filePath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    //See http://www.impulseadventure.com/photo/exif-orientation.html
                case 8:
                    $angle = -90;
                    break;
                case 3:
                    $angle = 180;
                    break;
                case 6:
                    $angle = 90;
                    break;
                default:
                    return;
                }
                $imagine = new Imagine();
                $transformation = new \Imagine\Filter\Transformation();
                $transformation->rotate($angle);
                $transformation->apply($imagine->open($filePath))->save($filePath);
            }
        }
    }
}
