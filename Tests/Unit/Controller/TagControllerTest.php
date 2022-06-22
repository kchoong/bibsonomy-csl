<?php
declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Test case
 *
 * @author Kevin Choong <choong.kvn@gmail.com>
 * @author Sebastian Böttger <boettger@cs.uni-kassel.de>
 */
class TagControllerTest extends UnitTestCase
{
    /**
     * @var \AcademicPuma\BibsonomyCsl\Controller\TagController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcademicPuma\BibsonomyCsl\Controller\TagController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllTagsFromRepositoryAndAssignsThemToView(): void
    {
        $allTags = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tagRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\TagRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagRepository->expects(self::once())->method('findAll')->will(self::returnValue($allTags));
        $this->subject->_set('tagRepository', $tagRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('tags', $allTags);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenTagToView(): void
    {
        $tag = new \AcademicPuma\BibsonomyCsl\Domain\Model\Tag();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('tag', $tag);

        $this->subject->showAction($tag);
    }
}
