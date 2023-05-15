<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Faq votes mysql resource
 */
class Votes extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_faq_article_votes', 'votes_id');
    }

    /**
     * Add visitor action
     *
     * @param int $visitorId
     * @param int $articleId
     * @param string $action
     * @return $this
     */
    public function addVisitorAction($visitorId, $articleId, $action)
    {
        if (!$this->isSetVisitorAction($visitorId, $articleId, $action)) {
            $this->getConnection()->insert(
                $this->getMainTable(),
                [
                    'article_id' => $articleId,
                    'visitor_id' => $visitorId,
                    'action' => $action
                ]
            );
        }

        return $this;
    }

    /**
     * Add customer action
     *
     * @param int $customerId
     * @param int $articleId
     * @param string $action
     * @return $this
     */
    public function addCustomerAction($customerId, $articleId, $action)
    {
        if (!$this->isSetCustomerAction($customerId, $articleId, $action)) {
            $this->getConnection()->insert(
                $this->getMainTable(),
                [
                    'article_id' => $articleId,
                    'customer_id' => $customerId,
                    'action' => $action
                ]
            );
        }

        return $this;
    }

    /**
     * Check visitor action status
     *
     * @param int $visitorId
     * @param int $articleId
     * @param string $action
     * @return bool
     */
    public function isSetVisitorAction($visitorId, $articleId, $action)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['faq' => $this->getMainTable()])
            ->where('faq.article_id = ?', $articleId)
            ->where('faq.visitor_id = ?', $visitorId)
            ->where('faq.action = ?', $action);

        if ($connection->fetchRow($select)) {
            return true;
        }

        return false;
    }

    /**
     * Check customer action status
     *
     * @param int $customerId
     * @param int $articleId
     * @param string $action
     * @return bool
     */
    public function isSetCustomerAction($customerId, $articleId, $action)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['faq' => $this->getMainTable()])
            ->where('faq.article_id = ?', $articleId)
            ->where('faq.customer_id = ?', $customerId)
            ->where('faq.action = ?', $action);

        if ($connection->fetchRow($select)) {
            return true;
        }

        return false;
    }

    /**
     * Delete visitor action
     *
     * @param int $visitorId
     * @param int $articleId
     * @param string $action
     * @return $this
     */
    public function removeVisitorAction($visitorId, $articleId, $action)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                'article_id = ?' => $articleId,
                'visitor_id = ?' => $visitorId,
                'action = ?' => $action
            ]
        );

        return $this;
    }

    /**
     * Delete customer action
     *
     * @param int $customerId
     * @param int $articleId
     * @param string $action
     * @return $this
     */
    public function removeCustomerAction($customerId, $articleId, $action)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                'article_id = ?' => $articleId,
                'customer_id = ?' => $customerId,
                'action = ?' => $action
            ]
        );

        return $this;
    }
}
