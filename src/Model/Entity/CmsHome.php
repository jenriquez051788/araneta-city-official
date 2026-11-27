<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CmsHome Entity
 *
 * @property int $id
 * @property string|null $hero_title
 * @property string|null $hero_heading
 * @property string|null $hero_subheading
 * @property string|null $hero_image
 * @property string|null $hero_video
 * @property bool $quick_nav_visible
 * @property string|null $featured_news_title
 * @property int $featured_news_limit
 * @property string|null $featured_happenings_title
 * @property int $featured_happenings_limit
 * @property bool $app_download_visible
 * @property string|null $app_download_heading
 * @property string|null $app_download_description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property bool $is_published
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class CmsHome extends Entity
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
        'hero_title' => true,
        'hero_heading' => true,
        'hero_subheading' => true,
        'hero_image' => true,
        'hero_video' => true,
        'quick_nav_visible' => true,
        'featured_news_title' => true,
        'featured_news_limit' => true,
        'featured_happenings_title' => true,
        'featured_happenings_limit' => true,
        'app_download_visible' => true,
        'app_download_heading' => true,
        'app_download_description' => true,
        'meta_title' => true,
        'meta_description' => true,
        'meta_keywords' => true,
        'is_published' => true,
        'created' => true,
        'modified' => true,
    ];
}
