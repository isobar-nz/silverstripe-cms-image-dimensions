<?php

namespace LittleGiant\CmsImageDimensions\Validation;

use SilverStripe\Assets\Upload_Validator;

/**
 * Class ImageAspectRatioUploadValidator
 * @package LittleGiant\CmsImageDimensions
 */
class ImageAspectRatioUploadValidator extends UploadValidator
{
    const ALLOWED_VARIATION = 0.001;

    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /**
     * ImageAspectRatioUploadValidator constructor.
     * @param int $width
     * @param int $height
     * @param null|\SilverStripe\Assets\Upload_Validator $previous
     */
    public function __construct(int $width, int $height, ?Upload_Validator $previous = null)
    {
        parent::__construct($previous);

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        if (!parent::validate()) {
            return false;
        }

        $isSvg = strpos($this->tmpFile['type'], 'image/svg') !== false;
        $imageSize = $isSvg ? $this->getSvgDimensions($this->tmpFile['tmp_name']) : getimagesize($this->tmpFile['tmp_name']);

        if ($imageSize === false) {
            $this->addError(UploadValidator::class, 'DIMENSIONS_UNAVAILABLE', 'The dimensions of your image could not be determined.');
            return false;
        }

        [$width, $height] = $imageSize;
        $ratio = (float) $width / $height;
        $expectedRatio = (float) $this->width / $this->height;

        if (!$this->floatEquals($ratio, $expectedRatio)) {
            $this->addError(self::class, 'DIMENSIONS_RATIO_FAIL', 'Image must have aspect ratio {width}:{height}.', [
                'width'  => $this->width,
                'height' => $this->height,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Try extract the dimensions of an SVG from the viewbox, falling back to the width/height attributes.
     * @see getimagesize()
     * @param string $path
     * @return array|false Array of [width, height] or false on failure. This is to maintain return type compatibility
     *                     with getimagesize()
     */
    protected function getSvgDimensions(string $path)
    {
        $svgXml = simplexml_load_file($path);
        $attributes = $svgXml->attributes();

        if ($attributes->viewBox) {
            // viewBox="xmin ymin width height"
            $viewBox = explode(' ', $attributes->viewBox);

            $width = $viewBox[2];
            $height = $viewBox[3];
        } else {
            $width = $attributes->width;
            $height = $attributes->height;
        }

        return ($width && $height)
            ? [
                floatval($width),
                floatval($height),
            ]
            : false;
    }

    /**
     * @param float $a
     * @param float $b
     * @return bool
     */
    protected function floatEquals(float $a, float $b): bool
    {
        return abs($a - $b) <= static::ALLOWED_VARIATION;
    }
}
