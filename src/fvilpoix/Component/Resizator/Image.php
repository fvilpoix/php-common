<?php

namespace fvilpoix\Component\Resizator;

class Image
{
    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $imageAsBinary;

    public function __construct($mimeType, $binary = null)
    {
        $this->mimeType      = $mimeType;
        $this->imageAsBinary = $binary ?: null;
    }

    public function getImageAsBinary()
    {
        return $this->imageAsBinary;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function setImageAsBinary($binary)
    {
        $this->imageAsBinary = $binary;

        return $this;
    }
}
