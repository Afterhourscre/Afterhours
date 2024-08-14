<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model;

use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\Write as DirectoryWrite;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;

/**
 * FAQ image uploader
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImageUploader
{
    const FAQ_PATH = 'faq';

    /**
     * Core file storage database
     *
     * @var Database
     */
    private $coreFileStorageDatabase;

    /**
     * Uploader factory
     *
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Tmp path
     *
     * @var string
     */
    private $tmpPath;

    /**
     * @param Database $coreFileStorageDatabase
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->tmpPath = DirectoryList::TMP . '/' . self::FAQ_PATH;
    }

    /**
     * Return Media Directory path
     *
     * @return DirectoryWrite
     */
    private function getMediaDirectory()
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $imageName
     * @return string
     */
    private function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Checking file for moving and move it
     *
     * @param string $imageName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveFileFromTmp($imageName)
    {
        $baseImagePath = $this->getFilePath(self::FAQ_PATH, $imageName);
        $baseTmpImagePath = $this->getFilePath($this->tmpPath, $imageName);
        $baseMediaImagePath = $this->getFilePath(self::FAQ_PATH . '/', $imageName);
        $mediaDirectory = $this->getMediaDirectory();

        if ($mediaDirectory->isExist($baseMediaImagePath) && !$mediaDirectory->isExist($baseTmpImagePath)) {
            return $imageName;
        }

        if ($mediaDirectory->isExist($baseTmpImagePath)) {
            try {
                $this->coreFileStorageDatabase->copyFile(
                    $baseTmpImagePath,
                    $baseImagePath
                );
                $mediaDirectory->renameFile(
                    $baseTmpImagePath,
                    $baseImagePath
                );
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
            return $imageName;
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('Image do not exist')
        );
    }

    /**
     * Checking file for save and save it to tmp dir
     *
     * @param string $fileId
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveFileToTmpDir($fileId)
    {
        /** @var Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $uploader->setAllowRenameFiles(true);
        $baseTmpPath = $this->getMediaDirectory()->getAbsolutePath(DirectoryList::TMP . '/' . self::FAQ_PATH);

        $result = $uploader->save($baseTmpPath);

        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($this->tmpPath, $result['file']);
        $result['name'] = $result['file'];

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($this->tmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }

        return $result;
    }
}
