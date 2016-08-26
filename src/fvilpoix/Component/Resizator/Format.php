<?php

namespace fvilpoix\Component\Resizator;

class Format
{
    const FLAG_WH = 'x'; // Format {width}x{height}
    const FLAG_W = 'w'; // w{width}
    const FLAG_H = 'h'; // h{height}

    /**
     * @var string
     */
    protected $rawFormat;

    /**
     * @var string One of self::FLAG_* constant
     */
    protected $type;

    /**
     * @var string
     */
    protected $flagWH;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    public function __construct($formatString)
    {
        $this->rawFormat = $formatString;

        if (0 === strpos($formatString, self::FLAG_W)) {
            $this->extractWidth($formatString);
        } elseif (0 === strpos($formatString, self::FLAG_H)) {
            $this->extractHeight($formatString);
        } elseif (false !== strpos($formatString, self::FLAG_WH)) {
            $this->extractWidthHeight($formatString);
        } else {
            throw new Exception\UnknownFormatException(sprintf('Unknown %s format for Resizator', $formatString));
        }
    }

    protected function extractWidthHeight($formatString)
    {
        $formatTokens = explode(self::FLAG_WH, $formatString);
        $this->width = (int) $formatTokens[0];
        $this->height = (int) $formatTokens[1];
        $this->type = self::FLAG_WH;
    }

    protected function extractWidth($formatString)
    {
        $this->width = (int) substr($formatString, 1);
        $this->type = self::FLAG_W;
    }

    protected function extractHeight($formatString)
    {
        $this->height = (int) substr($formatString, 1);
        $this->type = self::FLAG_H;
    }

    /**
     * @return string
     */
    public function getRaw()
    {
        return $this->rawFormat;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
