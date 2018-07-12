<?php

namespace LittleGiant\CmsImageDimensions;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;

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
        /*
         * This is done via reflection because afterExtending is protected, but the image field annotation can only
         * work on extension-provided fields if it is done after all extension fields are added.
         */
        $afterExtending = new \ReflectionMethod($this->owner, 'afterExtending');
        $afterExtending->setAccessible(true);

        $owner = $this->owner;
        $afterExtending->invoke($this->owner, 'updateCMSFields',
            function (FieldList $fields) use ($owner) {
                $this->processImageDimensions($fields, $owner);
            });

        $afterExtending->setAccessible(false);
    }

    /**
     * @param \SilverStripe\Forms\FieldList $fields
     * @param \SilverStripe\ORM\DataObject $owner
     */
    public function processImageDimensions(FieldList $fields, DataObject $owner): void
    {
        foreach ($owner->config()->get('image_dimensions') as $fieldName => $identifier) {
            $field = $fields->dataFieldByName($fieldName);

            if ($field instanceof UploadField) {
                $identifier = $owner->resolveImageDefinition($fieldName, $identifier);
                $this->imageDimensionsProvider->get($identifier)->updateUploadField($field);
            }
        }
    }

    /**
     * @param string $fieldName
     * @param string $rawIdentifier
     * @return string
     */
    public function resolveImageDefinition(string $fieldName, string $rawIdentifier): string
    {
        return $rawIdentifier;
    }
}
