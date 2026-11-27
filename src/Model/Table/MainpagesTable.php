<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Mainpages Model
 *
 * @method \App\Model\Entity\Mainpage newEmptyEntity()
 * @method \App\Model\Entity\Mainpage newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Mainpage> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Mainpage get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Mainpage findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Mainpage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Mainpage> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Mainpage|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Mainpage saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Mainpage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Mainpage>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Mainpage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Mainpage> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Mainpage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Mainpage>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Mainpage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Mainpage> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MainpagesTable extends Table
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

        $this->setTable('mainpages');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('title')
            ->maxLength('title', 200)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 200)
            ->requirePresence('slug', 'create')
            ->notEmptyString('slug')
            ->add('slug', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('hero_heading')
            ->maxLength('hero_heading', 255)
            ->allowEmptyString('hero_heading');

        $validator
            ->scalar('hero_image')
            ->maxLength('hero_image', 255)
            ->allowEmptyFile('hero_image');

        $validator
            ->scalar('content')
            ->maxLength('content', 16777215)
            ->allowEmptyString('content');

        $validator
            ->scalar('meta_title')
            ->maxLength('meta_title', 200)
            ->allowEmptyString('meta_title');

        $validator
            ->scalar('meta_description')
            ->maxLength('meta_description', 300)
            ->allowEmptyString('meta_description');

        $validator
            ->scalar('meta_keywords')
            ->maxLength('meta_keywords', 300)
            ->allowEmptyString('meta_keywords');

        $validator
            ->notEmptyString('status');

        return $validator;
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
