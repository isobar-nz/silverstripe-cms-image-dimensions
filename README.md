# SilverStripe CMS Image Dimensions
Easily add image dimensions to image fields and enforce maximum sizes for uploads to those fields through configuration.

Provides a single source of image dimensions for the user in the CMS

![CMS Image Dimensions](https://github.com/littlegiant/silverstripe-cms-image-dimensions/blob/master/images/cms-image-dimensions.png)
![CMS Image Dimensions on object](https://github.com/littlegiant/silverstripe-cms-image-dimensions/blob/master/images/cms-image-dimensions-on-object.png)

## Installation

Installation via composer
```
$ composer require littlegiant/silverstripe-cms-image-dimensions
```

## Usage

Set definitions in a yml config and add that configuration to the images which you want it to apply to

```yml
LittleGiant\CmsImageDimensions\ImageDimensionsProvider:
  max_size: 512K
  definitions:
    blog-post-featured-image:
      name: Blog Post Featured Image
      description: Featured image on your blog post.
      min_width: 1024
      min_height: 768
      validate_dimensions: true
      aspect_ratio: 4:3
      validate_aspect_ratio: true
      max_size: 1M # override default of 512KB to allow larger images

SilverStripe\Blog\Model\BlogPost:
  image_dimensions:
    FeaturedImage: blog-post-featured-image
```



## Contributing
### Code guidelines

This project follows the standards defined in:

* [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
* [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
* [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
