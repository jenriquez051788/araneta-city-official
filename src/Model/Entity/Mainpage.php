<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Mainpage Entity
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $hero_heading
 * @property string|null $hero_image
 * @property string|null $content
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property int $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class Mainpage extends Entity
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
        'hero_heading' => true,
        'hero_image' => true,
        'hero_image_dir' => true,
        'hero_image_size' => true,
        'hero_image_type' => true,
        'content' => true,
        'meta_title' => true,
        'meta_description' => true,
        'meta_keywords' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
    ];
}
