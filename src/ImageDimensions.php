<?php

namespace LittleGiant\CmsImageDimensions;

use LittleGiant\CmsImageDimensions\Validation\ImageAspectRatioUploadValidator;
use LittleGiant\CmsImageDimensions\Validation\ImageDimensionsUploadValidator;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Core\Convert;
use SilverStripe\View\ViewableData;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class ImageDimensions
 * @package LittleGiant\CmsImageDimensions
 */
class ImageDimensions extends ViewableData
{
    /**
     * @var array
     */
    private static $casting = [
        'DimensionsNice'      => 'HTMLFragment',
        'ValidateDimensions'  => 'Boolean',
        'ValidateAspectRatio' => 'Boolean',
    ];

    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var array */
    private $allowedExtensions;

    /** @var int */
    private $minWidth;

    /** @var int */
    private $minHeight;

    /** @var int */
    private $maxSize;

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
     * @param int $minWidth
     * @param int $minHeight
     * @param string $description
     * @param int $maxSize Maximum allowed file size in bytes, or 0 for unlimited.
     * @param bool $validateDimensions
     * @param bool $validateAspectRatio
     * @param int $aspectRatioWidth
     * @param int $aspectRatioHeight
     */
    public function __construct(string $identifier, string $name, array $allowedExtensions, int $minWidth, int $minHeight,
                                string $description = '', int $maxSize = 0, bool $validateDimensions = true,
                                bool $validateAspectRatio = false, int $aspectRatioWidth = 0, int $aspectRatioHeight = 0)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->description = $description;
        $this->maxSize = $maxSize;
        $this->allowedExtensions = $allowedExtensions;

        $this->minWidth = $minWidth;
        $this->minHeight = $minHeight;
        $this->validateDimensions = $validateDimensions;

        $this->aspectRatioWidth = $aspectRatioWidth;
        $this->aspectRatioHeight = $aspectRatioHeight;
        $this->validateAspectRatio = $validateAspectRatio;
    }

    /**
     * @param string $identifier
     * @param array $data
     * @param array $defaults
     * @return \LittleGiant\CmsImageDimensions\ImageDimensions
     */
    public static function fromYaml(string $identifier, array $data, array $defaults): self
    {
        if (!isset($data['min_width'], $data['min_height'])) {
            throw new InvalidConfigurationException("Image dimensions with identifier '{$identifier}' must have required parameters 'min_width' and 'min_height'.");
        }

        $data = array_merge([
            'description'  => '',
            'aspect_ratio' => '0:0',
            'max_size'     => 0,
            'name'         => $identifier,
        ], $defaults, $data);

        $maxSize = $data['max_size'];
        if (is_string($maxSize)) {
            // Handle INI format values
            $maxSize = Convert::memstring2bytes($maxSize);
        }

        [$aspectRatioWidth, $aspectRatioHeight] = explode(':', $data['aspect_ratio']);

        return static::create(
            $identifier,
            $data['name'],
            $data['allowed_extensions'],
            $data['min_width'],
            $data['min_height'],
            $data['description'],
            $maxSize,
            $data['validate_dimensions'],
            $data['validate_aspect_ratio'],
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

        if ($this->maxSize > 0) {
            /** @var \SilverStripe\Assets\Upload_Validator $validator */
            $validator = $field->getValidator();
            $validator->setAllowedMaxFileSize([
                // Set max file size for all file types. Multiply from KiB to bytes.
                '*' => $this->maxSize,
            ]);
        }

        if ($this->validateDimensions) {
            $field->setValidator(ImageDimensionsUploadValidator::create($this->minWidth, $this->minHeight, $field->getValidator()));
        }

        if ($this->validateAspectRatio) {
            $field->setValidator(ImageAspectRatioUploadValidator::create($this->aspectRatioWidth, $this->aspectRatioHeight, $field->getValidator()));
        }
    }

    /**
     * @return string
     */
    private function getUploadFieldRightText(): string
    {
        $text = '{description} The dimensions of the image should be at least {min_width}px wide and at least {min_height}px high.';

        if ($this->aspectRatioWidth > 0 && $this->aspectRatioHeight > 0) {
            $text .= ' The aspect ratio should be {aspect_width}:{aspect_height}.';
        }

        if ($this->maxSize > 0) {
            $text .= ' The file size should be no more than {max_size}.';
        }

        return _t(static::class . '.CMS_UPLOAD_RIGHT_TEXT', "{$text} The allowed extensions are: {allowed_extensions}.", [
            'description'        => $this->description,
            'allowed_extensions' => $this->getAllowedExtensionsNice(),
            'min_width'          => $this->minWidth,
            'min_height'         => $this->minHeight,
            'aspect_width'       => $this->aspectRatioWidth,
            'aspect_height'      => $this->aspectRatioHeight,
            'max_size'           => File::format_size($this->maxSize),
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
    public function getMinWidth(): int
    {
        return $this->minWidth;
    }

    /**
     * @return int
     */
    public function getMinHeight(): int
    {
        return $this->minHeight;
    }

    /**
     * @return string
     */
    public function getDimensionsNice(): string
    {
        return "{$this->minWidth}&times;{$this->minHeight}px";
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getMaxSize(): int
    {
        return $this->maxSize;
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
            : 'n/a';
    }

    /**
     * @return bool
     */
    public function validateDimensions(): bool
    {
        return $this->validateDimensions;
    }

    /**
     * @return bool
     */
    public function validateAspectRatio(): bool
    {
        return $this->validateAspectRatio;
    }
}
