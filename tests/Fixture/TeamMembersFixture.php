<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TeamMembersFixture
 */
class TeamMembersFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'title' => 'Lorem ipsum dolor sit amet',
                'department' => 'Lorem ipsum dolor sit amet',
                'group_role' => 'Lorem ipsum dolor sit amet',
                'status' => 1,
                'sort_order' => 1,
                'created' => 1788943722,
                'modified' => 1788943722,
            ],
        ];
        parent::init();
    }
}
