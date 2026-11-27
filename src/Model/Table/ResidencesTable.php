<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Residences Model
 *
 * @property \App\Model\Table\ResidenceGalleryTable&\Cake\ORM\Association\HasMany $ResidenceGallery
 * @property \App\Model\Table\ResidencePhasesTable&\Cake\ORM\Association\HasMany $ResidencePhases
 *
 * @method \App\Model\Entity\Residence newEmptyEntity()
 * @method \App\Model\Entity\Residence newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Residence> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Residence get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Residence findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Residence patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Residence> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Residence|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Residence saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Residence>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Residence>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Residence>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Residence> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Residence>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Residence>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Residence>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Residence> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ResidencesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('residences');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ResidenceGallery', [
            'foreignKey' => 'residence_id',
        ]);
        $this->hasMany('ResidencePhases', [
            'foreignKey' => 'residence_id',
        ]);
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['slug']), ['errorField' => 'slug']);

        return $rules;
    }
}
