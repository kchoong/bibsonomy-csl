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
class DocumentControllerTest extends UnitTestCase
{
    /**
     * @var \AcademicPuma\BibsonomyCsl\Controller\DocumentController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcademicPuma\BibsonomyCsl\Controller\DocumentController::class))
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
    public function listActionFetchesAllDocumentsFromRepositoryAndAssignsThemToView(): void
    {
        $allDocuments = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $documentRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\DocumentRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $documentRepository->expects(self::once())->method('findAll')->will(self::returnValue($allDocuments));
        $this->subject->_set('documentRepository', $documentRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('documents', $allDocuments);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenDocumentToView(): void
    {
        $document = new \AcademicPuma\BibsonomyCsl\Domain\Model\Document();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('document', $document);

        $this->subject->showAction($document);
    }
}
