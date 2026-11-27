<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Residence Entity
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $subtitle
 * @property string|null $description
 * @property int|null $towers_count
 * @property int|null $apartments_count
 * @property int|null $residents_count
 * @property string|null $developer
 * @property string|null $location
 * @property string|null $location_description
 * @property string|null $image_url
 * @property string|null $website
 * @property string|null $facebook
 * @property string|null $instagram
 * @property string|null $twitter
 * @property bool $featured
 * @property bool $status
 * @property int $sort_order
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\ResidenceGallery[] $residence_gallery
 * @property \App\Model\Entity\ResidencePhase[] $residence_phases
 */
class Residence extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'title' => true,
        'slug' => true,
        'subtitle' => true,
        'description' => true,
        'towers_count' => true,
        'apartments_count' => true,
        'residents_count' => true,
        'developer' => true,
        'location' => true,
        'location_description' => true,
        'image_url' => true,
        'website' => true,
        'facebook' => true,
        'instagram' => true,
        'twitter' => true,
        'featured' => true,
        'status' => true,
        'sort_order' => true,
        'created' => true,
        'modified' => true,
        'residence_gallery' => true,
        'residence_phases' => true,
    ];
}
