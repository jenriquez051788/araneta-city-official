<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ResidencePhasesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ResidencePhasesTable Test Case
 */
class ResidencePhasesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ResidencePhasesTable
     */
    protected $ResidencePhases;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.ResidencePhases',
        'app.Residences',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ResidencePhases') ? [] : ['className' => ResidencePhasesTable::class];
        $this->ResidencePhases = $this->getTableLocator()->get('ResidencePhases', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ResidencePhases);

        parent::tearDown();
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ResidencePhasesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
