<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ResidencePhasesFixture
 */
class ResidencePhasesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'residence_id' => 1,
                'phase_name' => 'Lorem ipsum dolor sit amet',
                'sort_order' => 1,
                'created' => 1788945888,
            ],
        ];
        parent::init();
    }
}
