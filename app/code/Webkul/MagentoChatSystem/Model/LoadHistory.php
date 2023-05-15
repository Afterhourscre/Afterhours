<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MagentoChatSystem\Model;

use Webkul\MagentoChatSystem\Api\LoadHistoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class LoadHistory implements LoadHistoryInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\ObjectManagerInterface   $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->date = $date;
        $this->_objectManager = $objectManager;
        $this->urlDecoder = $urlDecoder;
    }

    /**
     * Returns Message History.
     *
     * @api
     * @param int $currentPage Users name.
     * @return int $customerId.
     */
    public function loadHistory($currentPage, $customerId)
    {
        $customer = $this->_objectManager->create(
            'Magento\Customer\Model\Customer'
        )->load($customerId);

        if ($customer) {
            $customerData = [];
            $chatCustomerModel = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\CustomerData')
                ->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $customer->getId()])
                ->addFieldToFilter('chat_status', ['neq' => 0]);

            $customerData['chatStatus'] = $chatCustomerModel->getFirstItem()->getChatStatus();
            $customerData['profileImageUrl'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).
                'chatsystem/profile/'
                .$customer->getId().'/'.$chatCustomerModel->getFirstItem()->getImage();

            $customerUniqueId = $chatCustomerModel->getFirstItem()->getUniqueId();
            
            $messageModel = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\Message')
                ->getCollection()
                ->addFieldToFilter(
                    ['sender_unique_id', 'receiver_unique_id'],
                    [['eq' => $customerUniqueId], ['eq' => $customerUniqueId]]
                )
                ->setOrder('date', 'ASC');
                // ->setCurPage($currentPage)
                // ->setPageSize(10);
            //$lastMsgDate = $messageModel->getFirstItem()->getDate();
            $loadDate = date("Y-m-d H:i:s");
            if ($loadtime == 1) {
                $loadDate = date('Y-m-d H:i:s', strtotime($loadDate . ' -1 day'));
            } elseif ($loadtime == 2) {
                $loadDate = date('Y-m-d H:i:s', strtotime($loadDate . ' -7 day'));
            } elseif ($loadtime == 3) {
                $loadDate = date('Y-m-d H:i:s', strtotime($loadDate . ' -(5*365) day'));
            } else {
                $loadDate = date('Y-m-d H:i:s', strtotime($loadDate . ' -12 hour'));
            }
            $messageModel = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\Message')
                ->getCollection()
                ->addFieldToFilter(['sender_unique_id', 'receiver_unique_id'], [['eq' => $customerUniqueId], ['eq' => $customerUniqueId]])
                ->addFieldToFilter('date', ['gteq'=> $loadDate])
                ->setOrder('date', 'ASC');
            $previousDate = '';
            $customerData['messages'] = [];
            foreach ($messageModel as $key => $value) {
                $data = $value->getData();
                $changeDate = 0;
                $currentDate = strtotime($this->date->gmtDate('Y-m-d', $data['date']));
                if ($previousDate == '') {
                    $previousDate = strtotime($this->date->gmtDate('Y-m-d', $data['date']));
                    $changeDate = true;
                } elseif ($currentDate !== $previousDate) {
                    $changeDate = true;
                    $previousDate = strtotime($this->date->gmtDate('Y-m-d', $data['date']));
                }
                $data['type'] = 'text';

                $file = $this->urlDecoder->decode($data['message']);

                $fileSystem = $this->_objectManager->get(\Magento\Framework\Filesystem::class);
                $directory = $fileSystem->getDirectoryRead(DirectoryList::MEDIA);

                $fileName = 'chatsystem/attachments' . ltrim($file, '/');
                $filePath = $directory->getAbsolutePath($fileName);

                if ($directory->isFile($fileName)) {
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    switch (strtolower($extension)) {
                        case 'gif':
                            $contentType = 'image/gif';
                            $data['type'] = 'image';
                            break;
                        case 'jpg':
                            $contentType = 'image/jpeg';
                            $data['type'] = 'image';
                            break;
                        case 'png':
                            $contentType = 'image/png';
                            $data['type'] = 'image';
                            break;
                        default:
                            $contentType = 'application/octet-stream';
                            $data['type'] = 'file';
                            break;
                    }
                    $data['message'] = $this->urlBuilder->getUrl('chatsystem/index/viewfile', [$paramType => $data['message']]);
                }

                $data['time'] = $this->date->gmtDate('h:i A', $data['date']);
                $data['date'] = $this->date->gmtDate('Y-m-d', $data['date']);
                $data['changeDate'] = $changeDate;
                $customerData['messages'][$key] = $data;
            }

            return json_encode($customerData);
        }
    }
}
