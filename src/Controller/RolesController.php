<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Utility\Text;

/**
 * Roles Controller
 *
 * Role management is restricted to the superadmin role only, since roles
 * govern access across the whole CMS.
 *
 * @property \App\Model\Table\RolesTable $Roles
 */
class RolesController extends AppController
{
    /**
     * Roles allowed to manage roles.
     *
     * @var list<string>
     */
    private array $managerRoles = ['superadmin'];

    /**
     * Before filter: use the backend layout.
     *
     * @param \Cake\Event\EventInterface $event The event.
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        // All role-management actions require a logged-in user.
        $this->Authentication->setConfig('requireIdentity', true);

        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Guards actions to the superadmin role.
     *
     * @return \Cake\Http\Response|null Redirect when access is denied.
     */
    private function ensureManager(): ?Response
    {
        $this->Authorization->skipAuthorization();

        if (!$this->userHasRole($this->managerRoles)) {
            $this->Flash->error('Only a Super Admin can manage roles.');

            return $this->redirect(['controller' => 'Mainpages', 'action' => 'index']);
        }

        return null;
    }

    /**
     * Index: list all roles.
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $roles = $this->Roles->find()
            ->orderBy(['Roles.id' => 'ASC'])
            ->all();
        $this->set(compact('roles'));

        return null;
    }

    /**
     * View a single role and its users.
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null
     */
    public function view(?string $id = null): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $role = $this->Roles->get($id, contain: ['Users']);
        $this->set(compact('role'));

        return null;
    }

    /**
     * Add a new role.
     *
     * @return \Cake\Http\Response|null
     */
    public function add(): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $role = $this->Roles->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (empty($data['slug']) && !empty($data['name'])) {
                $data['slug'] = Text::slug(strtolower((string)$data['name']));
            }
            $role = $this->Roles->patchEntity($role, $data);
            if ($this->Roles->save($role)) {
                $this->Flash->success('The role has been saved.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The role could not be saved. Please check the form.');
        }
        $this->set(compact('role'));

        return null;
    }

    /**
     * Edit an existing role.
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null
     */
    public function edit(?string $id = null): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $role = $this->Roles->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (empty($data['slug']) && !empty($data['name'])) {
                $data['slug'] = Text::slug(strtolower((string)$data['name']));
            }
            $role = $this->Roles->patchEntity($role, $data);
            if ($this->Roles->save($role)) {
                $this->Flash->success('The role has been saved.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The role could not be saved. Please check the form.');
        }
        $this->set(compact('role'));

        return null;
    }

    /**
     * Delete a role. Core roles (superadmin/admin/marketing) and roles that
     * still have users assigned cannot be deleted.
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $this->request->allowMethod(['post', 'delete']);
        $role = $this->Roles->get($id, contain: ['Users']);

        $protected = ['superadmin', 'admin', 'marketing'];
        if (in_array($role->slug, $protected, true)) {
            $this->Flash->error('Core roles cannot be deleted.');

            return $this->redirect(['action' => 'index']);
        }

        if (!empty($role->users)) {
            $this->Flash->error('This role still has users assigned and cannot be deleted.');

            return $this->redirect(['action' => 'index']);
        }

        if ($this->Roles->delete($role)) {
            $this->Flash->success('The role has been deleted.');
        } else {
            $this->Flash->error('The role could not be deleted. Please try again.');
        }

        return $this->redirect(['action' => 'index']);
    }
}
