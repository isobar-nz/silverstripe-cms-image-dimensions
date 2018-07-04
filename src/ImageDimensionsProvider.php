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
    private static $allowed_extensions = [
        'png',
        'jpg',
        'gif',
        'webp',
    ];

    /**
     * @config
     * @var int
     */
    private static $max_size_kb;

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

        return ImageDimensions::fromYaml($identifier, $definitions[$identifier], static::getDefaultSettings());
    }

    /**
     * @return array
     */
    public function getDefaultSettings(): array
    {
        $config = static::config();

        return [
            'allowed_extensions' => $config->get('allowed_extensions'),
            'max_size_kb'        => $config->get('max_size_kb'),
        ];
    }

    /**
     * @return \SilverStripe\ORM\ArrayList
     */
    public function getAll(): ArrayList
    {
        $defaults = static::getDefaultSettings();
        $dimensions = [];

        foreach (static::config()->get('definitions') as $identifier => $data) {
            $dimensions[$identifier] = ImageDimensions::fromYaml($identifier, $data, $defaults);
        }

        return ArrayList::create($dimensions);
    }
}
