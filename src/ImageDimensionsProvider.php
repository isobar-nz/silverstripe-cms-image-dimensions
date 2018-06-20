<?php

namespace LittleGiant\CmsImageDimensions;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class ImageDimensionsProvider
 * @package LittleGiant\CmsImageDimensions
 */
class ImageDimensionsProvider
{
    use Injectable;
    use Configurable;

    /**
     * @config
     * @var \LittleGiant\CmsImageDimensions\ImageDimensions[string]
     */
    private static $definitions = [];

    /**
     * @config
     * @var string[]
     */
    private static $default_allowed_extensions = [
        'png',
        'jpg',
        'gif',
        'webp',
    ];

    /**
     * @param string $identifier
     * @return \LittleGiant\CmsImageDimensions\ImageDimensions
     */
    public function get(string $identifier): ImageDimensions
    {
        $definitions = static::config()->get('definitions');

        if (empty($definitions[$identifier])) {
            throw new InvalidConfigurationException("There are no image dimensions defined with identifier '{$identifier}'.");
        }

        return ImageDimensions::fromYaml($identifier, $definitions[$identifier],
            static::config()->get('default_allowed_extensions'));
    }

    /**
     * @return \SilverStripe\ORM\ArrayList
     */
    public function getAll(): ArrayList
    {
        $defaultAllowedExtensions = static::config()->get('default_allowed_extensions');
        $dimensions = [];

        foreach (static::config()->get('definitions') as $identifier => $data) {
            $dimensions[$identifier] = ImageDimensions::fromYaml($identifier, $data, $defaultAllowedExtensions);
        }

        return new ArrayList($dimensions);
    }
}
