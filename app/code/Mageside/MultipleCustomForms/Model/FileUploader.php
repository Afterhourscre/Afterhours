<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model;

class FileUploader
{
    const TEMP_FILES_STORAGE_TIME = 24;
    const TEMP_DIRECTORY = 'customform/tmp';
    const SUBMISSION_DIRECTORY = 'customform/submission';

    /**
     * Allowed extensions
     *
     * @var array
     */
    protected $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'csv'];

    /**
     * Media directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Uploader factory
     *
     * @var \Magento\Framework\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * FileUploader constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\UploaderFactory $uploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->initFilesystem();
    }

    /**
     * Set allowed extensions
     *
     * @param string[] $allowedExtensions
     * @return $this
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    /**
     * Retrieve base tmp path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return self::TEMP_DIRECTORY;
    }

    /**
     * Retrieve base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return self::SUBMISSION_DIRECTORY;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * @param $file
     * @param string $storeDir
     * @return string
     */
    public function getFileWebUrl($file, $storeDir = 'base')
    {
        switch ($storeDir) {
            case 'temp':
                $path = $this->getBaseTmpPath();
                break;
            case 'base':
            default:
                $path = $this->getBaseTmpPath();
                break;
        }

        return $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($path, $file);
    }

    /**
     * @param $fileName
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveFileFromTmp($fileName)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();

        $baseImagePath = $this->getFilePath($basePath, $fileName);
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $fileName);

        $fileNameNew = \Magento\MediaStorage\Model\File\Uploader::getNewFileName(
            $this->mediaDirectory->getAbsolutePath($baseImagePath)
        );
        if ($fileName !== $fileNameNew) {
            $fileName = $fileNameNew;
            $baseImagePath = $this->getFilePath($basePath, $fileName);
        }

        try {
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }

        return $fileName;
    }

    /**
     * @param $fileId
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = $this->getBaseTmpPath();

        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);

        $result = $uploader->save(
            $this->mediaDirectory->getAbsolutePath($baseTmpPath)
        );

        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->getFileWebUrl($result['file'], 'temp');
        $result['name'] = $result['file'];

        return $result;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function initFilesystem()
    {
        if (!$this->mediaDirectory->isExist($this->getBasePath())) {
            $this->mediaDirectory->create($this->getBasePath());
        }

        if (!$this->mediaDirectory->isExist($this->getBaseTmpPath())) {
            $this->mediaDirectory->create($this->getBaseTmpPath());
        }

        // Directory listing and hotlink secure
        $path = $this->getBasePath() . '/.htaccess';
        if (!$this->mediaDirectory->isFile($path)) {
            $this->mediaDirectory->writeFile($path, "Order deny,allow\nDeny from all");
        }
    }

    /**
     * Clean Temp Directory
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function cleanTempDirectory()
    {
        $expireTime = time() - (self::TEMP_FILES_STORAGE_TIME * 60 * 60);
        $files = $this->mediaDirectory->read($this->getBaseTmpPath());
        foreach ($files as $file) {
            if ($this->mediaDirectory->isFile($file)
                && $expireTime > filemtime($this->mediaDirectory->getAbsolutePath($file))
            ) {
                $this->mediaDirectory->delete($file);
            }
        }
    }
}
