<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ResidenceGalleryFixture
 */
class ResidenceGalleryFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'residence_gallery';
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
                'image_filename' => 'Lorem ipsum dolor sit amet',
                'image_dir' => 'Lorem ipsum dolor sit amet',
                'image_url' => 'Lorem ipsum dolor sit amet',
                'caption' => 'Lorem ipsum dolor sit amet',
                'alt_text' => 'Lorem ipsum dolor sit amet',
                'sort_order' => 1,
                'created' => 1788945898,
            ],
        ];
        parent::init();
    }
}
