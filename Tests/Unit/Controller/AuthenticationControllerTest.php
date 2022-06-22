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
class AuthenticationControllerTest extends UnitTestCase
{
    /**
     * @var \AcademicPuma\BibsonomyCsl\Controller\AuthenticationController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\AcademicPuma\BibsonomyCsl\Controller\AuthenticationController::class))
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
    public function listActionFetchesAllAuthenticationsFromRepositoryAndAssignsThemToView(): void
    {
        $allAuthentications = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $authenticationRepository->expects(self::once())->method('findAll')->will(self::returnValue($allAuthentications));
        $this->subject->_set('authenticationRepository', $authenticationRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('authentications', $allAuthentications);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenAuthenticationToAuthenticationRepository(): void
    {
        $authentication = new \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication();

        $authenticationRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationRepository->expects(self::once())->method('add')->with($authentication);
        $this->subject->_set('authenticationRepository', $authenticationRepository);

        $this->subject->createAction($authentication);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenAuthenticationToView(): void
    {
        $authentication = new \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('authentication', $authentication);

        $this->subject->editAction($authentication);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenAuthenticationInAuthenticationRepository(): void
    {
        $authentication = new \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication();

        $authenticationRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository::class)
            ->onlyMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationRepository->expects(self::once())->method('update')->with($authentication);
        $this->subject->_set('authenticationRepository', $authenticationRepository);

        $this->subject->updateAction($authentication);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenAuthenticationFromAuthenticationRepository(): void
    {
        $authentication = new \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication();

        $authenticationRepository = $this->getMockBuilder(\AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationRepository->expects(self::once())->method('remove')->with($authentication);
        $this->subject->_set('authenticationRepository', $authenticationRepository);

        $this->subject->deleteAction($authentication);
    }
}
