<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * Residences seed.
 */
class ResidencesSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/5/guides/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'title' => 'Azure Urban Residences',
                'slug' => 'azure-urban-residences',
                'subtitle' => 'Modern Living in the Heart of Araneta City',
                'description' => 'Azure Urban Residences offers contemporary apartments with state-of-the-art amenities.',
                'towers_count' => 3,
                'apartments_count' => 150,
                'residents_count' => 450,
                'developer' => 'Progressive Development Corp',
                'location' => 'Block A, Araneta City',
                'image_url' => 'https://example.com/azure.jpg',
                'website' => 'https://azure.example.com',
                'facebook' => 'https://facebook.com/azureurban',
                'instagram' => 'https://instagram.com/azureurban',
                'twitter' => 'https://twitter.com/azureurban',
                'featured' => true,
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Prestige Tower',
                'slug' => 'prestige-tower',
                'subtitle' => 'Premium Residential Excellence',
                'description' => 'Prestige Tower stands as a beacon of luxury and comfort for discerning residents.',
                'towers_count' => 2,
                'apartments_count' => 120,
                'residents_count' => 360,
                'developer' => 'Araneta Hotels & Resorts',
                'location' => 'Block B, Araneta City',
                'image_url' => 'https://example.com/prestige.jpg',
                'website' => 'https://prestige.example.com',
                'facebook' => 'https://facebook.com/prestigetower',
                'instagram' => 'https://instagram.com/prestigetower',
                'twitter' => 'https://twitter.com/prestigetower',
                'featured' => true,
                'status' => true,
                'sort_order' => 2,
            ],
        ];

        $table = $this->table('residences');
        $table->insert($data)->save();
    }
}
