<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Cron;

class CleanupTempDirectory
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\FileUploader
     */
    protected $_fileUploader;

    /**
     * CleanupTempDirectory constructor.
     * @param \Mageside\MultipleCustomForms\Model\FileUploader $fileUploader
     */
    public function __construct(
        \Mageside\MultipleCustomForms\Model\FileUploader $fileUploader
    ) {
        $this->_fileUploader = $fileUploader;
    }

    public function execute()
    {
        $this->_fileUploader->cleanTempDirectory();
    }
}
