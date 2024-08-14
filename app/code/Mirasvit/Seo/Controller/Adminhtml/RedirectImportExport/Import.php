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
 * @package   mirasvit/module-seo
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Controller\Adminhtml\RedirectImportExport;

class Import extends \Mirasvit\Seo\Controller\Adminhtml\RedirectImportExport
{
    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $existingStoreIds = [0];
        $stores           = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $existingStoreIds[] = $store->getId();
        }

        /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
        $uploader = $this->fileUploaderFactory->create(['fileId' => 'import_redirect_file']);
        $uploader->setAllowedExtensions(['csv']);
        $uploader->setAllowRenameFiles(true);
        $path = $this->filesystem
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
                ->getAbsolutePath() . '/import';
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }

        try {
            $result   = $uploader->save($path);
            $fullPath = $result['path'] . '/' . $result['file'];

            $file = new \Magento\Framework\Filesystem\Driver\File();
            $file->isFile($fullPath);
            $csv  = new \Magento\Framework\File\Csv($file);
            $data = $csv->getData($fullPath);

            $items = [];
            if (count($data) > 1) {
                for ($i = 1; $i < count($data); ++$i) {
                    $item = [];
                    for ($j = 0; $j < count($data[0]); ++$j) {
                        if (isset($data[$i][$j]) && trim($data[$i][$j]) != '') {
                            $preparedKey                    = preg_replace('/[[:^print:]]/', "", $data[0][$j]); //delete invisible symbols
                            $item[strtolower($preparedKey)] = $data[$i][$j];
                        }
                    }
                    $items[] = $item;
                }
            }

            $resource        = $this->resource;
            $writeConnection = $resource->getConnection('core_write');
            $table           = $resource->getTableName('mst_seo_redirect');
            $tableB          = $resource->getTableName('mst_seo_redirect_store');
            $i               = 0;
            foreach ($items as $item) {
                if (!isset($item['url_from']) || !isset($item['url_to'])) {
                    continue;
                }
                $item = new \Magento\Framework\DataObject($item);
                $query
                      = "REPLACE {$table} SET
                    redirect_id = '" . $item->getRedirectId() . "',  
                    url_from = '" . addslashes((string)$item->getUrlFrom()) . "',
                    url_to = '" . addslashes((string)$item->getUrlTo()) . "',
                    redirect_type = '" . addslashes((string)$item->getRedirectType()) . "',
                    is_redirect_only_error_page = '" . addslashes((string)$item->getIsRedirectOnlyErrorPage()) . "',
                    comments = '" . addslashes((string)$item->getComments()) . "',
                    is_active = '" . addslashes((string)$item->getIsActive()) . "';
                     ";
                /** @var \Zend_Db_Adapter_Mysqli  $writeConnection*/
                $writeConnection->query($query);

                $lastInsertId = $item->getRedirectId() ?: $writeConnection->lastInsertId();
                $storeIds     = ($item->getStoreId()) ? explode('/', (string)$item->getStoreId()) : [0];
                $storeIds     = array_intersect($storeIds, $existingStoreIds);

                foreach ($storeIds as $storeId) {
                    $query = "REPLACE {$tableB} SET
                        store_id = " . $storeId . ",
                        redirect_id = " . $lastInsertId . ";";
                        $writeConnection->query($query);
                }

                ++$i;
            }

            $this->messageManager->addSuccessMessage('' . $i . ' records were inserted or updated');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
