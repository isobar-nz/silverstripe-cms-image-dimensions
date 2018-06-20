<?php

namespace LittleGiant\CmsImageDimensions\Validation;

use SilverStripe\Assets\Upload_Validator;

/**
 * Class ImageDimensionsUploadValidator
 * @package LittleGiant\CmsImageDimensions
 */
class ImageDimensionsUploadValidator extends UploadValidator
{
    /** @var int */
    protected $minWidth;

    /** @var int */
    protected $minHeight;

    /**
     * ImageDimensionsUploadValidator constructor.
     * @param int $minWidth
     * @param int $minHeight
     * @param null|\SilverStripe\Assets\Upload_Validator $previous
     */
    public function __construct(int $minWidth, int $minHeight, ?Upload_Validator $previous = null)
    {
        parent::__construct($previous);

        $this->minWidth = $minWidth;
        $this->minHeight = $minHeight;
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
        if ($width < $this->minWidth || $height < $this->minHeight) {
            $this->addError(self::class, 'DIMENSIONS_TOOSMALL', 'Image must be at least {minWidth}px wide by {minHeight}px high.', [
                'minWidth'  => $this->minWidth,
                'minHeight' => $this->minHeight,
            ]);
            return false;
        }

        return true;
    }
}
