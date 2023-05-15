<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;

class FileType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\DefaultType
{
    /**
     * @var \Magento\Framework\File\Size
     */
    protected $_fileSizeService;

    /**
     * FileType constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Settings $fieldSettings
     * @param \Magento\Framework\File\Size $fileSize
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        \Magento\Framework\File\Size $fileSize,
        array $data = []
    ) {
        $this->_fileSizeService = $fileSize;
        parent::__construct($context, $fieldSettings, $data);
    }

    /**
     * @return mixed
     */
    public function getMaxFileSize()
    {
        $iniConfig = $this->_fileSizeService->getMaxFileSize();
        $fieldConfig = $this->_field->getData(Settings::OPTION_MAX_FILE_SIZE) * 1024;

        return min($iniConfig, $fieldConfig);
    }

    /**
     * @return bool|string
     */
    public function getAllowedExtensions()
    {
        if ($extensions = $this->_field->getData(Settings::OPTION_ALLOWED_EXTENSIONS)) {
            $allowedExtensions = explode(',', $extensions);
            array_walk(
                $allowedExtensions,
                function (&$value) {
                    $value = strtolower(trim($value));
                }
            );
            return implode(' ', $allowedExtensions);
        }

        return false;
    }

    /**
     * @return number
     */
    public function getAllowedMaxUploads()
    {
        return $this->_field->getData(Settings::OPTION_COUNT_UPLOADS);
    }
}
