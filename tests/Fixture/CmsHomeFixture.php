<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CmsHomeFixture
 */
class CmsHomeFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'cms_home';
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
                'hero_title' => 'Lorem ipsum dolor sit amet',
                'hero_heading' => 'Lorem ipsum dolor sit amet',
                'hero_subheading' => 'Lorem ipsum dolor sit amet',
                'hero_image' => 'Lorem ipsum dolor sit amet',
                'hero_video' => 'Lorem ipsum dolor sit amet',
                'quick_nav_visible' => 1,
                'featured_news_title' => 'Lorem ipsum dolor sit amet',
                'featured_news_limit' => 1,
                'featured_happenings_title' => 'Lorem ipsum dolor sit amet',
                'featured_happenings_limit' => 1,
                'app_download_visible' => 1,
                'app_download_heading' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'app_download_description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'meta_title' => 'Lorem ipsum dolor sit amet',
                'meta_description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'meta_keywords' => 'Lorem ipsum dolor sit amet',
                'is_published' => 1,
                'created' => 1788932632,
                'modified' => 1788932632,
            ],
        ];
        parent::init();
    }
}
