<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ResidenceGalleryTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ResidenceGalleryTable Test Case
 */
class ResidenceGalleryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ResidenceGalleryTable
     */
    protected $ResidenceGallery;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.ResidenceGallery',
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
        $config = $this->getTableLocator()->exists('ResidenceGallery') ? [] : ['className' => ResidenceGalleryTable::class];
        $this->ResidenceGallery = $this->getTableLocator()->get('ResidenceGallery', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ResidenceGallery);

        parent::tearDown();
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ResidenceGalleryTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
