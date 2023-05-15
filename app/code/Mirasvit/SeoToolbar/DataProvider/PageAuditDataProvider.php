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
 * @version   2.0.169
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoToolbar\DataProvider;

use Magento\Framework\App\ResponseInterface;
use Mirasvit\SeoToolbar\Api\Service\DataProviderInterface;

class PageAuditDataProvider implements DataProviderInterface
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var Criteria\AbstractCriteria[]
     */
    private $criteriaPool;

    public function __construct(
        ResponseInterface $response,
        array $criteriaPool
    ) {
        $this->response = $response;
        $this->criteriaPool = $criteriaPool;
    }

    public function getTitle()
    {
        return __('Page analysis');
    }

    public function getItems()
    {
        $content = $this->response->getContent();

        $items = [];
        foreach ($this->criteriaPool as $criteria) {
            $items[] = $criteria->handle($content);
        }

        return $items;
    }
}
