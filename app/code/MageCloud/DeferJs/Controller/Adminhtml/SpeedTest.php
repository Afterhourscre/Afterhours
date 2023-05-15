<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 */
namespace MageCloud\DeferJs\Controller\Adminhtml;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;
use MageCloud\DeferJs\Helper\Data as DataHelper;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

/**
 * Class SpeedTest
 * @package MageCloud\DeferJs\Controller\Adminhtml
 */
abstract class SpeedTest extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Form key validator
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * @var ConfigInterface
     */
    protected $_configInterface;

    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl|null
     */
    protected $_curl = null;

    /**
     * SpeedTest constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param DataHelper $dataHelper
     * @param \Magento\Framework\HTTP\Adapter\Curl $curl
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory,
        DataHelper $dataHelper,
        ConfigInterface $configInterface,
        \Magento\Framework\HTTP\Adapter\Curl $curl
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_urlBuilder = $context->getUrl();
        $this->_dataHelper = $dataHelper;
        $this->_configInterface = $configInterface;
        $this->_formKeyValidator = $context->getFormKeyValidator();
        $this->_curl = $curl;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function objectToArray($data)
    {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = $this->objectToArray($value);
            } elseif (is_array($value)) {
                foreach ($value as $nestedKey => $nestedValue) {
                    if (is_object($nestedValue)) {
                        $value[$nestedKey] = $this->objectToArray($nestedValue);
                    }
                }
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param $string
     * @return mixed
     */
    public function makeClickableLinks($string)
    {
        return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="$1" target="_blank">$1</a>', $string);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urlBuilder->getBaseUrl();
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageCloud_DeferJs::deferjs');
    }
}