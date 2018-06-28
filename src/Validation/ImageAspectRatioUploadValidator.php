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

        $imageSize = getimagesize($this->tmpFile['tmp_name']);
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
     * @param float $a
     * @param float $b
     * @return bool
     */
    private function floatEquals(float $a, float $b): bool
    {
        return abs($a - $b) <= static::ALLOWED_VARIATION;
    }
}
