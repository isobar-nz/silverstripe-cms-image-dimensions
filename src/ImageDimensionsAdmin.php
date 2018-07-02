<?php

namespace LittleGiant\CmsImageDimensions;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * Class ImageDimensionsAdmin
 * @package LittleGiant\CmsImageDimensions
 */
class ImageDimensionsAdmin extends LeftAndMain implements PermissionProvider
{
    const CMS_ACCESS = 'CMS_ACCESS_' . self::class;

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
        return $this->imageDimensionsProvider->getAll()->sort('Name');
    }

    /**
     * @return bool
     */
    public function showConstraintEnforcement(): bool
    {
        return static::config()->get('show_constraint_enforcement');
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            self::CMS_ACCESS => [
                'name' => 'View CMS image specifications',
                'category' => _t(Permission::class . '.CMS_ACCESS_CATEGORY', 'CMS Access'),
            ],
        ];
    }
}
