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
class CitationStylesheetControllerTest extends UnitTestCase
{
    /**
     * @var \AcademicPuma\BibsonomyCsl\Controller\CitationStylesheetController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcademicPuma\BibsonomyCsl\Controller\CitationStylesheetController::class))
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
    public function listActionFetchesAllCitationStylesheetsFromRepositoryAndAssignsThemToView(): void
    {
        $allCitationStylesheets = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $citationStylesheetRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $citationStylesheetRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCitationStylesheets));
        $this->subject->_set('citationStylesheetRepository', $citationStylesheetRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('citationStylesheets', $allCitationStylesheets);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenCitationStylesheetToCitationStylesheetRepository(): void
    {
        $citationStylesheet = new \AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet();

        $citationStylesheetRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $citationStylesheetRepository->expects(self::once())->method('add')->with($citationStylesheet);
        $this->subject->_set('citationStylesheetRepository', $citationStylesheetRepository);

        $this->subject->createAction($citationStylesheet);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenCitationStylesheetToView(): void
    {
        $citationStylesheet = new \AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('citationStylesheet', $citationStylesheet);

        $this->subject->editAction($citationStylesheet);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenCitationStylesheetInCitationStylesheetRepository(): void
    {
        $citationStylesheet = new \AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet();

        $citationStylesheetRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository::class)
            ->onlyMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $citationStylesheetRepository->expects(self::once())->method('update')->with($citationStylesheet);
        $this->subject->_set('citationStylesheetRepository', $citationStylesheetRepository);

        $this->subject->updateAction($citationStylesheet);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenCitationStylesheetFromCitationStylesheetRepository(): void
    {
        $citationStylesheet = new \AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet();

        $citationStylesheetRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $citationStylesheetRepository->expects(self::once())->method('remove')->with($citationStylesheet);
        $this->subject->_set('citationStylesheetRepository', $citationStylesheetRepository);

        $this->subject->deleteAction($citationStylesheet);
    }
}
