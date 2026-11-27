<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TeamMembers Model
 *
 * @method \App\Model\Entity\TeamMember newEmptyEntity()
 * @method \App\Model\Entity\TeamMember newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\TeamMember> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TeamMember get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\TeamMember findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\TeamMember patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\TeamMember> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TeamMember|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\TeamMember saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\TeamMember>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TeamMember>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TeamMember>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TeamMember> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TeamMember>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TeamMember>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TeamMember>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TeamMember> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TeamMembersTable extends Table
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

        $this->setTable('team_members');
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
            ->scalar('name')
            ->maxLength('name', 200)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('title')
            ->maxLength('title', 200)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('department')
            ->maxLength('department', 150)
            ->allowEmptyString('department');

        $validator
            ->scalar('group_role')
            ->maxLength('group_role', 150)
            ->allowEmptyString('group_role');

        $validator
            ->allowEmptyString('status');

        $validator
            ->integer('sort_order')
            ->allowEmptyString('sort_order');

        return $validator;
    }
}
