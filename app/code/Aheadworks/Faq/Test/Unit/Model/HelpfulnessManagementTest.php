<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Test\Unit\Model;

use Aheadworks\Faq\Api\Data\VoteResultInterface;
use Aheadworks\Faq\Api\Data\VoteResultInterfaceFactory;
use Aheadworks\Faq\Model\Article;
use Aheadworks\Faq\Model\ArticleRepository;
use Aheadworks\Faq\Model\Helpfulness\Manager;
use Aheadworks\Faq\Model\HelpfulnessManagement;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for HelpfulnessManagement
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HelpfulnessManagementTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ArticleRepository
     */
    private $articleRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Manager
     */
    private $helpfulnessManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|VoteResultInterfaceFactory
     */
    private $votesFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Article
     */
    private $articleMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|VoteResultInterface
     */
    private $voteResultMock;

    /**
     * @var HelpfulnessManagement
     */
    private $helpfulnessManagementObject;

    /**
     * Initialize Model
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->articleRepositoryMock = $this->createMock(ArticleRepository::class);
        $this->votesFactoryMock = $this->createMock(VoteResultInterfaceFactory::class);
        $this->voteResultMock = $this->createMock(VoteResultInterface::class);
        $this->articleMock = $this->createMock(Article::class);
        $this->helpfulnessManagerMock = $this->createMock(Manager::class);

        $this->votesFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->voteResultMock);

        $this->voteResultMock
            ->expects($this->once())
            ->method('setLikeStatus')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->voteResultMock
            ->expects($this->once())
            ->method('setDislikeStatus')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->helpfulnessManagementObject = $this->objectManager->getObject(
            HelpfulnessManagement::class,
            [
                'articleRepository' => $this->articleRepositoryMock,
                'helpfulnessManager' => $this->helpfulnessManagerMock,
                'votesFactory' => $this->votesFactoryMock
            ]
        );
    }

    /**
     * Add first like
     *
     * @covers HelpfulnessManagement::like
     */
    public function testAddLike()
    {
        $articleId = 3;
        $likes = 0;

        $this->articleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getById')
            ->with($articleId)
            ->willReturn($this->articleMock);

        $this->helpfulnessManagerMock
            ->expects($this->at(0))
            ->method('isSetAction')
            ->with('like', $articleId)
            ->willReturn(false);

        $this->helpfulnessManagerMock
            ->expects($this->at(1))
            ->method('isSetAction')
            ->with('dislike', $articleId)
            ->willReturn(false);

        $this->articleMock
            ->expects($this->once())
            ->method('getVotesYes')
            ->willReturn($likes);

        $this->articleMock
            ->expects($this->never())
            ->method('getVotesNo');

        $this->articleMock
            ->expects($this->never())
            ->method('setVotesNo');

        $this->articleMock
            ->expects($this->once())
            ->method('setVotesYes')
            ->with(++$likes);

        $this->helpfulnessManagerMock
            ->expects($this->never())
            ->method('removeAction')
            ->withAnyParameters();

        $this->helpfulnessManagerMock
            ->expects($this->once())
            ->method('addAction')
            ->with('like', $articleId);

        $this->articleRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->articleMock);

        $this->assertEquals($this->voteResultMock, $this->helpfulnessManagementObject->like($articleId));
    }

    /**
     * Add like with removing previous dislike
     *
     * @covers HelpfulnessManagement::like
     */
    public function testAddLikeRemoveDislike()
    {
        $articleId = 3;
        $likes = 0;
        $dislikes = 1;

        $this->articleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getById')
            ->with($articleId)
            ->willReturn($this->articleMock);

        $this->helpfulnessManagerMock
            ->expects($this->at(0))
            ->method('isSetAction')
            ->with('like', $articleId)
            ->willReturn(false);

        $this->helpfulnessManagerMock
            ->expects($this->at(1))
            ->method('isSetAction')
            ->with('dislike', $articleId)
            ->willReturn(true);

        $this->helpfulnessManagerMock
            ->expects($this->once())
            ->method('removeAction')
            ->with('dislike', $articleId);

        $this->articleMock
            ->expects($this->once())
            ->method('getVotesNo')
            ->willReturn($dislikes);

        $this->articleMock
            ->expects($this->once())
            ->method('setVotesNo')
            ->with(--$dislikes);

        $this->articleMock
            ->expects($this->once())
            ->method('getVotesYes')
            ->willReturn($likes);

        $this->articleMock
            ->expects($this->once())
            ->method('setVotesYes')
            ->with(++$likes);

        $this->helpfulnessManagerMock
            ->expects($this->once())
            ->method('addAction')
            ->with('like', $articleId);

        $this->articleRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->articleMock);

        $this->assertEquals($this->voteResultMock, $this->helpfulnessManagementObject->like($articleId));
    }

    /**
     * Add first dislike
     *
     * @covers HelpfulnessManagement::dislike
     */
    public function testAddDislike()
    {
        $articleId = 3;
        $dislikes = 0;

        $this->articleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getById')
            ->with($articleId)
            ->willReturn($this->articleMock);

        $this->helpfulnessManagerMock
            ->expects($this->at(0))
            ->method('isSetAction')
            ->with('dislike', $articleId)
            ->willReturn(false);

        $this->helpfulnessManagerMock
            ->expects($this->at(1))
            ->method('isSetAction')
            ->with('like', $articleId)
            ->willReturn(false);

        $this->articleMock
            ->expects($this->once())
            ->method('getVotesNo')
            ->willReturn($dislikes);

        $this->articleMock
            ->expects($this->never())
            ->method('getVotesYes');

        $this->articleMock
            ->expects($this->never())
            ->method('setVotesYes');

        $this->articleMock
            ->expects($this->once())
            ->method('setVotesNo')
            ->with(++$dislikes);

        $this->helpfulnessManagerMock
            ->expects($this->never())
            ->method('removeAction')
            ->withAnyParameters();

        $this->helpfulnessManagerMock
            ->expects($this->once())
            ->method('addAction')
            ->with('dislike', $articleId);

        $this->articleRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->articleMock);

        $this->assertEquals($this->voteResultMock, $this->helpfulnessManagementObject->dislike($articleId));
    }

    /**
     * Add dislike with removing previous like
     *
     * @covers HelpfulnessManagement::dislike
     */
    public function testAddDislikeRemoveLike()
    {
        $articleId = 3;
        $likes = 1;
        $dislikes = 0;

        $this->articleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getById')
            ->with($articleId)
            ->willReturn($this->articleMock);

        $this->helpfulnessManagerMock
            ->expects($this->at(0))
            ->method('isSetAction')
            ->with('dislike', $articleId)
            ->willReturn(false);

        $this->helpfulnessManagerMock
            ->expects($this->at(1))
            ->method('isSetAction')
            ->with('like', $articleId)
            ->willReturn(true);

        $this->helpfulnessManagerMock
            ->expects($this->once())
            ->method('removeAction')
            ->with('like', $articleId);

        $this->articleMock
            ->expects($this->once())
            ->method('getVotesYes')
            ->willReturn($likes);

        $this->articleMock
            ->expects($this->once())
            ->method('setVotesYes')
            ->with(--$likes);

        $this->articleMock
            ->expects($this->once())
            ->method('getVotesNo')
            ->willReturn($dislikes);

        $this->articleMock
            ->expects($this->once())
            ->method('setVotesNo')
            ->with(++$dislikes);

        $this->helpfulnessManagerMock
            ->expects($this->once())
            ->method('addAction')
            ->with('dislike', $articleId);

        $this->articleRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->articleMock);

        $this->assertEquals($this->voteResultMock, $this->helpfulnessManagementObject->dislike($articleId));
    }
}
