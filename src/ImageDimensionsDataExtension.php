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
    public function updateCMSFields(FieldList $f)
    {
        /** @var array $imageDimensions */
        $imageDimensions = $this->owner->config()->get('image_dimensions');

        /*
         * This is done via reflection because afterExtending is protected, but the image field annotation can only
         * work on extension-provided fields if it is done after all extension fields are added.
         */
        $afterExtending = new \ReflectionMethod($this->owner, 'afterExtending');
        $afterExtending->setAccessible(true);

        $afterExtending->invoke($this->owner, 'updateCMSFields',
            function (FieldList $fields) use ($imageDimensions) {
                $this->processImageDimensions($fields, $imageDimensions);
            });

        $afterExtending->setAccessible(false);
    }

    /**
     * @param \SilverStripe\Forms\FieldList $fields
     * @param array $imageDimensionsConfig
     */
    public function processImageDimensions(FieldList $fields, array $imageDimensionsConfig): void
    {
        foreach ($imageDimensionsConfig as $fieldName => $identifier) {
            $field = $fields->dataFieldByName($fieldName);

            if ($field instanceof UploadField) {
                $this->imageDimensionsProvider->get($identifier)->updateUploadField($field);
            }
        }
    }
}
