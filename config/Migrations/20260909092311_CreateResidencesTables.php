<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateResidencesTables extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/guides/writing-migrations/migration-methods.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        // ============ RESIDENCES TABLE ============
        $this->table('residences', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'integer', ['autoIncrement' => true])
            ->addColumn('title', 'string', ['limit' => 200, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 200, 'null' => false])
            ->addColumn('subtitle', 'string', ['limit' => 500, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('towers_count', 'integer', ['null' => true])
            ->addColumn('apartments_count', 'integer', ['null' => true])
            ->addColumn('residents_count', 'integer', ['null' => true])
            ->addColumn('developer', 'string', ['limit' => 200, 'null' => true])
            ->addColumn('location', 'string', ['limit' => 200, 'null' => true])
            ->addColumn('location_description', 'text', ['null' => true])
            ->addColumn('image_url', 'string', ['limit' => 500, 'null' => true])
            ->addColumn('website', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('facebook', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('instagram', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('twitter', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('featured', 'boolean', ['default' => false])
            ->addColumn('status', 'boolean', ['default' => true])
            ->addColumn('sort_order', 'integer', ['default' => 0])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['slug'], ['unique' => true])
            ->addIndex(['status'])
            ->addIndex(['featured'])
            ->create();

        // ============ RESIDENCE PHASES TABLE ============
        // For storing multiple phases/towers per residence
        $this->table('residence_phases', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'integer', ['autoIncrement' => true])
            ->addColumn('residence_id', 'integer', ['null' => false])
            ->addColumn('phase_name', 'string', ['limit' => 200, 'null' => false])
            ->addColumn('sort_order', 'integer', ['default' => 0])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('residence_id', 'residences', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addIndex(['residence_id'])
            ->addIndex(['sort_order'])
            ->create();

        // ============ RESIDENCE GALLERY TABLE ============
        // For storing multiple photos per residence
        $this->table('residence_gallery', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'integer', ['autoIncrement' => true])
            ->addColumn('residence_id', 'integer', ['null' => false])
            ->addColumn('image_filename', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('image_dir', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('image_url', 'string', ['limit' => 500, 'null' => false])
            ->addColumn('caption', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('alt_text', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('sort_order', 'integer', ['default' => 0])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('residence_id', 'residences', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addIndex(['residence_id'])
            ->addIndex(['sort_order'])
            ->create();
    }
}
