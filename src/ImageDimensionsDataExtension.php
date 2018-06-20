<?php

namespace LittleGiant\CmsImageDimensions;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Class ImageDimensionsDataExtension
 * @package LittleGiant\CmsImageDimensions
 */
class ImageDimensionsDataExtension extends DataExtension
{
    /**
     * @config
     * @var string[string]
     */
    private static $image_dimensions = [];

    /**
     * @var class[string]
     */
    private static $dependencies = [
        'imageDimensionsProvider' => '%$' . ImageDimensionsProvider::class,
    ];

    /** @var \LittleGiant\CmsImageDimensions\ImageDimensionsProvider */
    public $imageDimensionsProvider;

    /**
     * @inheritDoc
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** @var \SilverStripe\Core\Config\Config_ForClass $config */
        $config = $this->owner->config();

        foreach ($config->get('image_dimensions') as $fieldName => $identifier) {
            $field = $fields->dataFieldByName($fieldName);

            if ($field instanceof UploadField) {
                $this->imageDimensionsProvider->get($identifier)->updateUploadField($field);
            }
        }
    }
}
