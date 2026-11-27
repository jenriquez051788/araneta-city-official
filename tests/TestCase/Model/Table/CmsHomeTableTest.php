<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsHomeTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsHomeTable Test Case
 */
class CmsHomeTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsHomeTable
     */
    protected $CmsHome;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.CmsHome',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CmsHome') ? [] : ['className' => CmsHomeTable::class];
        $this->CmsHome = $this->getTableLocator()->get('CmsHome', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CmsHome);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\CmsHomeTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
