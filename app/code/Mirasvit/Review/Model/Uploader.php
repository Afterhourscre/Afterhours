<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Review\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Review\Repository\ReviewMediaRepository;
use Magento\Framework\Filesystem\Driver\File;

class Uploader
{
    protected $messageManager;

    protected $storeManager;

    protected $filesystem;

    protected $fileUploader;

    protected $request;

    protected $mediaRepository;

    protected $mediaDirectory;

    protected $file;

    public function __construct
    (
        File                    $file,
        ReviewMediaRepository   $mediaRepository,
        RequestInterface        $request,
        MessageManagerInterface $messageManager,
        StoreManagerInterface   $storeManager,
        Filesystem              $filesystem,
        UploaderFactory         $fileUploader
    ) {
        $this->file            = $file;
        $this->mediaRepository = $mediaRepository;
        $this->request         = $request;
        $this->mediaDirectory  = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->messageManager  = $messageManager;
        $this->storeManager    = $storeManager;
        $this->fileUploader    = $fileUploader;
        $this->filesystem      = $filesystem;
    }


    public function uploadFiles(array $photos, string $inputName): ?array
    {
        $mediaFolder = 'mst_review/tmp/';
        try {
            $files = $this->request->getFiles()->toArray();

            if ($photos) {
                foreach ($photos as $key => $photo) {
                    $files[$inputName]["name"][$key]  = $photo['name'];
                    $target                           = $this->mediaDirectory->getAbsolutePath($mediaFolder);
                    $uploader                         = $this->fileUploader->create(['fileId' => $inputName . '[' . $key . ']']);

                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setAllowRenameFiles(true);
                    $result = $uploader->save($target);

                    if ($result['file']) {
                        $result['url'] = $this->storeManager->getStore()
                                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $mediaFolder . $result['file'];

                        return $result;
                    }
                }

            }

        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }

        return null;
    }

    public function deleteImage(int $reviewId, array $newMedia = [])
    {
        $mediaFolder = 'mst_review/' . $reviewId . '/';

        $media  = $this->mediaRepository->getByReview($reviewId);
        $target = $this->mediaDirectory->getAbsolutePath($mediaFolder);

        if ($media) {
            foreach ($media as $image) {
                if (!in_array($image['value'],$newMedia) &&
                    $this->file->isExists($target . $image['value'])) {
                    $this->file->deleteFile($target . $image['value']);
                }
            }
        }
    }
}
