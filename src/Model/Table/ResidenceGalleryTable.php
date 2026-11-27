<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ResidenceGallery Model
 *
 * @property \App\Model\Table\ResidencesTable&\Cake\ORM\Association\BelongsTo $Residences
 *
 * @method \App\Model\Entity\ResidenceGallery newEmptyEntity()
 * @method \App\Model\Entity\ResidenceGallery newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ResidenceGallery> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ResidenceGallery get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ResidenceGallery findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ResidenceGallery patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ResidenceGallery> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ResidenceGallery|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ResidenceGallery saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ResidenceGallery>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ResidenceGallery>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ResidenceGallery>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ResidenceGallery> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ResidenceGallery>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ResidenceGallery>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ResidenceGallery>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ResidenceGallery> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ResidenceGalleryTable extends Table
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

        $this->setTable('residence_gallery');
        $this->setDisplayField('image_url');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Residences', [
            'foreignKey' => 'residence_id',
            'joinType' => 'INNER',
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
        $rules->add($rules->existsIn(['residence_id'], 'Residences'), ['errorField' => 'residence_id']);

        return $rules;
    }
}
