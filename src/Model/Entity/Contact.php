<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Contact Entity
 *
 * @property int $id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string $contact_number
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property string $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class Contact extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string>
     */
    protected array $_accessible = [
        'first_name' => true,
        'middle_name' => true,
        'last_name' => true,
        'contact_number' => true,
        'email' => true,
        'subject' => true,
        'message' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Get full name
     *
     * @return string
     */
    protected function _getFullName(): string
    {
        $parts = [$this->first_name];
        if (!empty($this->middle_name)) {
            $parts[] = $this->middle_name;
        }
        $parts[] = $this->last_name;
        return implode(' ', $parts);
    }

    /**
     * Strip all HTML/BBCode-style tags from a value and normalise it to plain
     * text. Runs automatically via the _set* mutators below whenever a field
     * is assigned (patchEntity, newEntity, or direct set), so spam markup like
     * <center>, <font color=red>, <a href>, <list> can never be stored.
     *
     * @param mixed $value The incoming value.
     * @return mixed The sanitized string, or the value unchanged if not a string.
     */
    protected function sanitizeText(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        // Remove proper HTML tags.
        $value = strip_tags($value);
        // Decode entities so nothing sneaks through encoded.
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        // Strip leftover angle-bracket fragments (e.g. "<size16", "<list")
        // that aren't valid tags but are used by spam.
        $value = preg_replace('/<[^>]*>?/', '', $value);

        return trim($value);
    }

    /**
     * Field mutators: sanitize each text field on assignment.
     */
    protected function _setFirstName(mixed $value): mixed
    {
        return $this->sanitizeText($value);
    }

    protected function _setMiddleName(mixed $value): mixed
    {
        return $this->sanitizeText($value);
    }

    protected function _setLastName(mixed $value): mixed
    {
        return $this->sanitizeText($value);
    }

    protected function _setContactNumber(mixed $value): mixed
    {
        return $this->sanitizeText($value);
    }

    protected function _setEmail(mixed $value): mixed
    {
        return $this->sanitizeText($value);
    }

    protected function _setSubject(mixed $value): mixed
    {
        return $this->sanitizeText($value);
    }

    protected function _setMessage(mixed $value): mixed
    {
        return $this->sanitizeText($value);
    }
}
