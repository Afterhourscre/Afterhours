<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Category;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Aheadworks\Faq\Model\ImageUploader;

/**
 * Class FileInfo
 * @package Aheadworks\Faq\Model\Category
 */
class FileInfo
{
    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     */
    public function __construct(
        Filesystem $filesystem,
        Mime $mime
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mime = $mime;
    }

    /**
     * Retrieve MIME type of file
     *
     * @param string $fileName
     * @return string
     */
    public function getMimeType($fileName)
    {
        $absoluteFilePath = $this->mediaDirectory->getAbsolutePath(
            $this->getFilePath($fileName)
        );
        return $this->mime->getMimeType($absoluteFilePath);
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     */
    public function getStat($fileName)
    {
        return $this->mediaDirectory->stat($this->getFilePath($fileName));
    }

    /**
     * Check if file exists
     *
     * @param string $fileName
     * @return bool
     */
    public function isExist($fileName)
    {
        return $this->mediaDirectory->isExist($this->getFilePath($fileName));
    }

    /**
     * Get file path
     *
     * @param string $fileName
     * @return string
     */
    private function getFilePath($fileName)
    {
        return rtrim(ImageUploader::FAQ_PATH, '/') . '/' . ltrim($fileName, '/');
    }
}
