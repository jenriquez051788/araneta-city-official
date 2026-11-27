<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property int $role_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string $password
 * @property string|null $password_reset_token
 * @property \Cake\I18n\DateTime|null $password_reset_expires
 * @property int $status
 * @property \Cake\I18n\DateTime|null $last_login
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Role $role
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'role_id' => true,
        'first_name' => true,
        'last_name' => true,
        'email' => true,
        'password' => true,
        'password_reset_token' => true,
        'password_reset_expires' => true,
        'status' => true,
        'last_login' => true,
        'created' => true,
        'modified' => true,
        'role' => true,
    ];

    /**
     * Fields that are excluded from JSON/array versions of the entity.
     *
     * @var list<string>
     */
    protected array $_hidden = [
        'password',
        'password_reset_token',
    ];

    /**
     * Automatically hash passwords when they are set.
     *
     * @param string $password Plain-text password.
     * @return string|null Hashed password, or null if empty.
     */
    protected function _setPassword(string $password): ?string
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }

        return null;
    }

    /**
     * Convenience accessor for the user's full name.
     *
     * @return string
     */
    protected function _getFullName(): string
    {
        return trim(sprintf('%s %s', (string)$this->first_name, (string)$this->last_name));
    }
}
