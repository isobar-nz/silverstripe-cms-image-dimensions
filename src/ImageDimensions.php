<?php

namespace LittleGiant\CmsImageDimensions;

use LittleGiant\CmsImageDimensions\Validation\ImageAspectRatioUploadValidator;
use LittleGiant\CmsImageDimensions\Validation\ImageDimensionsUploadValidator;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ViewableData;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class ImageDimensions
 * @package LittleGiant\CmsImageDimensions
 */
class ImageDimensions extends ViewableData
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var array */
    private $allowedExtensions;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /** @var bool */
    private $validateDimensions;

    /** @var int */
    private $aspectRatioWidth;

    /** @var int */
    private $aspectRatioHeight;

    /** @var bool */
    private $validateAspectRatio;

    /**
     * ImageDimensions constructor.
     * @param string $identifier
     * @param string $name
     * @param array $allowedExtensions
     * @param int $width
     * @param int $height
     * @param string $description
     * @param bool $validateDimensions
     * @param bool $validateAspectRatio
     * @param int $aspectRatioWidth
     * @param int $aspectRatioHeight
     */
    public function __construct(string $identifier, string $name, array $allowedExtensions, int $width, int $height,
                                string $description = '', bool $validateDimensions = true, bool $validateAspectRatio = false,
                                int $aspectRatioWidth = 0, int $aspectRatioHeight = 0)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->description = $description;
        $this->allowedExtensions = $allowedExtensions;

        $this->width = $width;
        $this->height = $height;
        $this->validateDimensions = $validateDimensions;

        $this->aspectRatioWidth = $aspectRatioWidth;
        $this->aspectRatioHeight = $aspectRatioHeight;
        $this->validateAspectRatio = $validateAspectRatio;
    }

    /**
     * @param string $identifier
     * @param array $data
     * @param array $defaultAllowedExtensions
     * @return \LittleGiant\CmsImageDimensions\ImageDimensions
     */
    public static function fromYaml(string $identifier, array $data, array $defaultAllowedExtensions): self
    {
        if (!isset($data['width'], $data['height'])) {
            throw new InvalidConfigurationException("Image dimensions with identifier '{$identifier}' must have required parameters 'width' and 'height'.");
        }

        [$aspectRatioWidth, $aspectRatioHeight] = explode(':', $data['aspect_ratio'] ?? '0:0');

        return new self(
            $identifier,
            $data['name'] ?? $identifier,
            $data['allowed_extensions'] ?? $defaultAllowedExtensions,
            $data['width'],
            $data['height'],
            $data['description'] ?? '',
            $data['validate_dimensions'] ?? true,
            $data['validate_aspect_ratio'] ?? false,
            $aspectRatioWidth,
            $aspectRatioHeight
        );
    }

    /**
     * @param \SilverStripe\AssetAdmin\Forms\UploadField $field
     */
    public function updateUploadField(UploadField $field): void
    {
        $field->setRightTitle($this->getUploadFieldRightText());
        $field->setAllowedExtensions($this->allowedExtensions);

        if ($this->validateDimensions) {
            $field->setValidator(new ImageDimensionsUploadValidator($this->width, $this->height, $field->getValidator()));
        }

        if ($this->validateAspectRatio) {
            $field->setValidator(new ImageAspectRatioUploadValidator($this->aspectRatioWidth, $this->aspectRatioHeight, $field->getValidator()));
        }
    }

    /**
     * @return string
     */
    private function getUploadFieldRightText(): string
    {
        $text = '{description} The dimensions of the image should be {width}px wide by {height}px high.';

        if ($this->aspectRatioWidth > 0 && $this->aspectRatioHeight > 0) {
            $text .= ' The aspect ratio should be {aspect_width}:{aspect_height}.';
        }

        return _t(static::class . '.CMS_UPLOAD_RIGHT_TEXT', "{$text} The allowed extensions are: {allowed_extensions}.", [
                'description'        => $this->description,
                'allowed_extensions' => $this->getAllowedExtensionsNice(),
                'width'              => $this->width,
                'height'             => $this->height,
                'aspect_width'        => $this->aspectRatioWidth,
                'aspect_height'       => $this->aspectRatioHeight,
            ]);
    }

    /**
     * @return string
     */
    public function getAllowedExtensionsNice(): string
    {
        return implode(', ', $this->allowedExtensions);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getDimensionsNice(): DBHTMLText
    {
        return DBHTMLText::create()->setValue("{$this->width}&times;{$this->height}px");
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    /**
     * @return int
     */
    public function getAspectRatioWidth(): int
    {
        return $this->aspectRatioWidth;
    }

    /**
     * @return int
     */
    public function getAspectRatioHeight(): int
    {
        return $this->aspectRatioHeight;
    }

    /**
     * @return string
     */
    public function getAspectRatioNice(): string
    {
        return ($this->aspectRatioWidth > 0 && $this->aspectRatioHeight > 0)
            ? "{$this->aspectRatioWidth}:{$this->aspectRatioHeight}"
            : '';
    }

    /**
     * @return \SilverStripe\ORM\FieldType\DBBoolean
     */
    public function validateDimensions(): DBBoolean
    {
        return DBBoolean::create()->setValue($this->validateDimensions);
    }

    /**
     * @return \SilverStripe\ORM\FieldType\DBBoolean
     */
    public function validateAspectRatio(): DBBoolean
    {
        return DBBoolean::create()->setValue($this->validateAspectRatio);
    }
}
