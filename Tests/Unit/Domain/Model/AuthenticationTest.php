<?php
declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author Kevin Choong <choong.kvn@gmail.com>
 * @author Sebastian Böttger <boettger@cs.uni-kassel.de>
 */
class AuthenticationTest extends UnitTestCase
{
    /**
     * @var \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication::class,
            ['dummy']
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function dummyTestToNotLeaveThisFileEmpty(): void
    {
        self::markTestIncomplete();
    }
}
