<?php

namespace LittleGiant\CmsImageDimensions;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\ORM\ArrayList;

/**
 * Class ImageDimensionsAdmin
 * @package LittleGiant\CmsImageDimensions
 */
class ImageDimensionsAdmin extends LeftAndMain
{
    /**
     * @var string
     */
    private static $menu_title = 'Image Dimensions';

    /**
     * @var string
     */
    private static $menu_icon_class = 'font-icon-image';

    /**
     * @var string
     */
    private static $url_segment = 'image-dimensions';

    /**
     * @config
     * @var bool
     */
    private static $show_constraint_enforcement = true;

    /**
     * @var class[string]
     */
    private static $dependencies = [
        'imageDimensionsProvider' => '%$' . ImageDimensionsProvider::class,
    ];

    /** @var \LittleGiant\CmsImageDimensions\ImageDimensionsProvider */
    public $imageDimensionsProvider;

    /**
     * @return \SilverStripe\ORM\ArrayList
     */
    public function getImageDefinitions(): ArrayList
    {
        return $this->imageDimensionsProvider->getAll();
    }

    /**
     * @return bool
     */
    public function showConstraintEnforcement(): bool
    {
        return static::config()->get('show_constraint_enforcement');
    }
}
