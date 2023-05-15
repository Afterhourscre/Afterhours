<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Test\Unit\Model;

use Aheadworks\Faq\Model\VoteResult;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for VoteResult
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VoteResultTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var VoteResult
     */
    private $voteResultObject;

    /**
     * Initialize Model
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->voteResultObject = $this->objectManager->getObject(
            VoteResult::class,
            ['data' => ['like' => true, 'dislike' => false]]
        );
    }

    /**
     * @param null|bool $data
     *
     * @covers VoteResult::getLikeStatus
     */
    public function testGetLikeStatus($data = null)
    {
        $this->assertEquals($data === null ? true : $data, $this->voteResultObject->getLikeStatus());
    }

    /**
     * Set like status to true and test it
     *
     * @covers  VoteResult::setLikeStatus
     * @depends testGetLikeStatus
     */
    public function testSetLikeStatusTrue()
    {
        $newStatus = true;

        $result = $this->voteResultObject->setLikeStatus($newStatus);

        $this->assertInstanceOf(VoteResult::class, $result);
        $this->testGetLikeStatus($newStatus);
    }

    /**
     * Set like status to false and test it
     *
     * @covers  VoteResult::setLikeStatus
     * @depends testGetLikeStatus
     */
    public function testSetLikeStatusFalse()
    {
        $newStatus = false;

        $result = $this->voteResultObject->setLikeStatus($newStatus);

        $this->assertInstanceOf(VoteResult::class, $result);
        $this->testGetLikeStatus($newStatus);
    }

    /**
     * @param null|bool $data
     *
     * @covers VoteResult::getDislikeStatus
     */
    public function testGetDislikeStatus($data = null)
    {
        $this->assertEquals($data === null ? false : $data, $this->voteResultObject->getDislikeStatus());
    }

    /**
     * Set dislike status to true and test it
     *
     * @covers  VoteResult::setLikeStatus
     * @depends testGetDislikeStatus
     */
    public function testSetDislikeStatusTrue()
    {
        $newStatus = true;

        $result = $this->voteResultObject->setDislikeStatus($newStatus);

        $this->assertInstanceOf(VoteResult::class, $result);
        $this->testGetDislikeStatus($newStatus);
    }

    /**
     * Set dislike status to false and test it
     *
     * @covers  VoteResult::setLikeStatus
     * @depends testGetDislikeStatus
     */
    public function testSetDislikeStatusFalse()
    {
        $newStatus = false;

        $result = $this->voteResultObject->setDislikeStatus($newStatus);

        $this->assertInstanceOf(VoteResult::class, $result);
        $this->testGetDislikeStatus($newStatus);
    }
}
