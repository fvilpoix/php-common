<?php

namespace fvilpoix\Component\Resizator;

use Gaufrette\Filesystem;
use Gaufrette\Exception\FileNotFound;
use Imagine\Imagick\Imagine;

class Resizator
{
    protected static $MIMES = array(
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
    );

    /**
     * @var \Gaufrette\Filesystem
     */
    protected $fileSystem;

    /**
     * @var string
     */
    protected $keyPattern;

    public function __construct(Filesystem $fileSystem, $keyPattern)
    {
        $this->fileSystem = $fileSystem;
        $this->keyPattern = $keyPattern;
    }

    public function get($fileKey, $format)
    {
        if ($format && !$format instanceof Format) {
            $format = new Format($format);
        }

        $sanitizedFileKey = $this->sanitizeFileKey($fileKey);
        $ext = $this->getExtension($sanitizedFileKey);

        $fileKeyWithFormat = $this->getFileKeyWithFormat($sanitizedFileKey, $format, $ext);

        if ($this->fileSystem->has($fileKeyWithFormat)) {
            $binaryContent = $this->fileSystem->read($fileKeyWithFormat);
        } else {
            $binaryContent = $this->generateThumbnail($sanitizedFileKey, $fileKeyWithFormat, $format);
        }

        $mimeType  = $this->getMimeType($sanitizedFileKey);

        return new Image($mimeType, $binaryContent);
    }

    protected function sanitizeFileKey($fileKey)
    {
        return str_replace([
            '..',
        ], [
            '',
        ], $fileKey);
    }

    protected function getFileKeyWithFormat($filename, Format $format, $extension)
    {
        return str_replace(
            [
                '{format}',
                '{filename}',
                '{ext}',
            ], [
                $format->getRaw(),
                $filename,
                $extension,
            ],
            $this->keyPattern
        );
    }

    protected function getMimeType($filename)
    {
        $extension = $this->getExtension($filename);

        return array_key_exists($extension, self::$MIMES)
            ? self::$MIMES[$extension]
            : self::$MIMES['jpeg']
        ;
    }

    protected function getExtension($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        return $ext;
    }

    /**
     * @param string $fileKey
     * @param string $fileKeyWithFormat
     * @param Format $format
     *
     * @return
     */
    protected function generateThumbnail($fileKey, $fileKeyWithFormat, Format $format)
    {
        // check if has original picture
        try {
            $has = $this->fileSystem->has($fileKey);
        } catch (\OutOfBoundsException $e) {
            $has = false;
        }

        if (!$has) {
            throw new Exception\ImageDoesNotExistException();
        }

        // create thumbnail
        try {
            $blobOriginal = $this->fileSystem->read($fileKey);
        } catch (FileNotFound $e) {
            throw new Exception\ImageDoesNotExistException();
        }

        $imagine      = new Imagine();
        $image        = $imagine->load($blobOriginal);

        $resizedImage = Manipulator::resize($image, $format);

        $extension = $this->getExtension($fileKey);

        $blobResizedImage = $resizedImage->get($extension, array(
            'jpeg_quality' => 90, // 0 to 100
            'png_compression_level' => 9, // 0 to 9
        ));

        $this->fileSystem->write($fileKeyWithFormat, $blobResizedImage, true);

        return $blobResizedImage;
    }
}
