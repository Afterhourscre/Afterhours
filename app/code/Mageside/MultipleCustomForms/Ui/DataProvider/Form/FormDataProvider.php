<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Ui\DataProvider\Form;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Collection;
use Mageside\MultipleCustomForms\Helper\Config;
use Mageside\MultipleCustomForms\Model\ResourceModel\Recipient\CollectionFactory as RecipientCollectionFactory;
use Magento\Framework\Stdlib\ArrayManager;

class FormDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Meta config path
     */
    const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\Recipient\CollectionFactory
     */
    protected $recipientCollectionFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var null|\Mageside\MultipleCustomForms\Model\CustomForm
     */
    private $form;

    /**
     * FormDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PoolInterface $pool
     * @param Collection $collection
     * @param RecipientCollectionFactory $recipientCollectionFactory
     * @param Config $configHelper
     * @param ArrayManager $arrayManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        Collection $collection,
        RecipientCollectionFactory $recipientCollectionFactory,
        Config $configHelper,
        ArrayManager $arrayManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->pool = $pool;
        $this->collection = $collection;
        $this->recipientCollectionFactory = $recipientCollectionFactory;
        $this->configHelper = $configHelper;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->arrayManager = $arrayManager;

        $storeId = (int) $this->request->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        if (!$this->isReCaptchaInstalled()) {
            $meta['custom_form']['children']['reCaptcha']['arguments']['data']['config']['disabled'] = true;
            $meta['custom_form']['children']['reCaptcha']['arguments']['data']['config']['tooltip'] = [
                'link' => 'https://mageside.com/media/attachment/file/m/u/multipleforms_installation.pdf',
                'description' => __(
                    'If you want to use this feature, you will have to configure Google reCaptcha api keys.'
                )
            ];
        }

        $this->meta = $meta;

        $form = $this->getForm();
        if (!empty($form->getData('useDefault')) && $this->request->getParam('store')) {
            foreach ($form->getData('useDefault') as $title => $usedDefault) {
                $this->titleUsedDefault($title, $usedDefault);
            }
        }

        return $this->meta;
    }

    /**
     * @param $titleIndex
     * @param $usedDefault
     * @return $this
     */
    protected function titleUsedDefault($titleIndex, $usedDefault)
    {
        $useDefaultConfig = [
            'usedDefault'   => $usedDefault,
            'disabled'      => $usedDefault,
            'service'       => [
                'template'  => 'ui/form/element/helper/service',
            ]
        ];
        $this->meta['custom_form']['children'][$titleIndex]['arguments']['data']['config'] = $useDefaultConfig;

        return $this;
    }

    /**
     * @return bool
     */
    private function isReCaptchaInstalled()
    {
        try {
            $secretKey = $this->configHelper->getConfigModule('recaptcha_secret_key');
            if (!$secretKey || !is_string($secretKey)) {
                return false;
            }
            $publicKey = $this->configHelper->getConfigModule('recaptcha_public_key');
            if (!$publicKey || !is_string($publicKey)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $form = $this->getForm();
        $this->data[$form->getId()]['form'] = $form->toArray();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    private function getForm()
    {
        if ($this->form === null) {
            /** @var $form \Mageside\MultipleCustomForms\Model\CustomForm */
            $form = $this->collection
                ->addFieldToFilter('id', $this->request->getParam('id'))
                ->getFirstItem();
            if ($form->getId()) {
                $form->getResource()->loadAdditionalSettings($form);
                $form->getResource()->loadEmailsSettings($form);
            }

            $this->form = $form;
        }

        return $this->form;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        parent::addFilter($filter);
    }
}
