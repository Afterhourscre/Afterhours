<?php
namespace WeltPixel\Backend\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Logger\Monolog;

class Logger extends Monolog
{
    const XML_PATH_WELTPIXEL_DEVELOPER_LOGGING = 'weltpixel_backend_developer/logging/disable_broken_reference';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Logger constructor.
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct($name, array $handlers = [], array $processors = [])
    {
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * Set scope config.
     * 
     * @param ScopeConfigInterface $scopeConfig
     */
    public function setScopeConfig(ScopeConfigInterface $scopeConfig): void
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        $result = $this->_parseLogMessage($message, $context);
        if ($result !== false) {
            parent::warning($message, $context);
        }
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return void
     */
    public function info($message, array $context = []): void
    {
        $result = $this->_parseLogMessage($message, $context);
        if ($result !== false) {
            parent::info($message, $context);
        }
    }

    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    protected function _parseLogMessage($message, array $context): bool
    {
        if ($this->scopeConfig === null) {
            return true;
        }

        $isLogEnabled = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_DEVELOPER_LOGGING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $pos = strpos($message, 'Broken reference');
        if (!$isLogEnabled && ($pos !== false)) {
            return false;
        }

        return true;
    }
}
