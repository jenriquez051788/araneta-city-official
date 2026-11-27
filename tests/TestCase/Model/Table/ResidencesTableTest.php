<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ResidencesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ResidencesTable Test Case
 */
class ResidencesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ResidencesTable
     */
    protected $Residences;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Residences',
        'app.ResidenceGallery',
        'app.ResidencePhases',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Residences') ? [] : ['className' => ResidencesTable::class];
        $this->Residences = $this->getTableLocator()->get('Residences', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Residences);

        parent::tearDown();
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ResidencesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
