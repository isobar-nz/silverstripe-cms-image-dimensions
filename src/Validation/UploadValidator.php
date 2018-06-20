<?php

namespace LittleGiant\CmsImageDimensions\Validation;

use SilverStripe\Assets\Upload_Validator;

/**
 * Class UploadValidator
 * @package LittleGiant\CmsImageDimensions
 */
abstract class UploadValidator extends Upload_Validator
{
    /** @var \SilverStripe\Assets\Upload_Validator */
    protected $previous;

    /**
     * UploadValidator constructor.
     * @param null|\SilverStripe\Assets\Upload_Validator $previous
     */
    public function __construct(?Upload_Validator $previous = null)
    {
        $this->previous = $previous;

        if ($previous !== null) {
            $this->tmpFile = $previous->tmpFile;
        }
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        if ($this->previous === null) {
            return true;
        }

        $result = $this->previous->validate();
        $this->errors = array_merge($this->errors, $this->previous->errors);
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setTmpFile($tmpFile)
    {
        parent::setTmpFile($tmpFile);

        if ($this->previous !== null) {
            $this->previous->setTmpFile($tmpFile);
        }
    }

    /**
     * @inheritDoc
     */
    public function setAllowedMaxFileSize($rules)
    {
        parent::setAllowedMaxFileSize($rules);

        if ($this->previous !== null) {
            $this->previous->setAllowedMaxFileSize($rules);
        }
    }

    /**
     * @inheritDoc
     */
    public function setAllowedExtensions($rules)
    {
        parent::setAllowedExtensions($rules);

        if ($this->previous !== null) {
            $this->previous->setAllowedExtensions($rules);
        }
    }

    /**
     * Helper for adding errors.
     * @param string $class Translation entity class.
     * @param string $entityName Translation entity name.
     * @param string[] $args
     */
    protected function addError(string $class, string $entityName, ...$args): void
    {
        if (empty($class)) {
            $class = static::class;
        }

        $this->errors[] = _t("{$class}.{$entityName}", ...$args);
    }
}
