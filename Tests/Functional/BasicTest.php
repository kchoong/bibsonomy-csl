<?php
declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Tests\Functional;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case
 *
 * @author Kevin Choong <choong.kvn@gmail.com>
 * @author Sebastian Böttger <boettger@cs.uni-kassel.de>
 */
class BasicTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/bibsonomy_csl',
    ];

    /**
     * Just a dummy to show that at least one test is actually executed
     *
     * @test
     */
    public function dummy(): void
    {
        $this->assertTrue(true);
    }
}
