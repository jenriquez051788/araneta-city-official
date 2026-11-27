<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ResidencePhase Entity
 *
 * @property int $id
 * @property int $residence_id
 * @property string $phase_name
 * @property int $sort_order
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Residence $residence
 */
class ResidencePhase extends Entity
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
        'phase_name' => true,
        'sort_order' => true,
        'created' => true,
        'residence' => true,
    ];
}
