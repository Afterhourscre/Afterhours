<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ReCaptcha;

class Adapter
{
    /**
     * @var \Mageside\MultipleCustomForms\Lib\ReCaptcha\ReCaptcha|bool
     */
    private $reCaptcha;

    /**
     * @var \Mageside\MultipleCustomForms\Helper\Config
     */
    private $configHelper;

    /**
     * Adapter constructor.
     * @param \Mageside\MultipleCustomForms\Helper\Config $configHelper
     */
    public function __construct(\Mageside\MultipleCustomForms\Helper\Config $configHelper)
    {
        $this->configHelper = $configHelper;

        $secretKey = $this->configHelper->getConfigModule('recaptcha_secret_key');
        if (!$secretKey || !is_string($secretKey)) {
            $this->reCaptcha = false;
        } else {
            $this->reCaptcha = new \Mageside\MultipleCustomForms\Lib\ReCaptcha\ReCaptcha($secretKey);
        }
    }

    /**
     * @param $response
     * @param null $remoteIp
     * @return \Mageside\MultipleCustomForms\Lib\ReCaptcha\Response|bool
     */
    public function verify($response, $remoteIp = null)
    {
        if ($this->reCaptcha) {
            return $this->reCaptcha->verify($response, $remoteIp);
        } else {
            return false;
        }
    }
}
