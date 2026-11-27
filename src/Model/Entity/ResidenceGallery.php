<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ResidenceGallery Entity
 *
 * @property int $id
 * @property int $residence_id
 * @property string|null $image_filename
 * @property string|null $image_dir
 * @property string $image_url
 * @property string|null $caption
 * @property string|null $alt_text
 * @property int $sort_order
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Residence $residence
 */
class ResidenceGallery extends Entity
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
        'residence_id' => true,
        'image_filename' => true,
        'image_dir' => true,
        'image_url' => true,
        'caption' => true,
        'alt_text' => true,
        'sort_order' => true,
        'created' => true,
        'residence' => true,
    ];
}
