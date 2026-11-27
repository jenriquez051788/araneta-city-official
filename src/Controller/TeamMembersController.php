<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * TeamMembers Controller
 *
 * @property \App\Model\Table\TeamMembersTable $TeamMembers
 */
class TeamMembersController extends AppController
{
    /**
     * Before filter callback - admin-only access
     *
     * @param \Cake\Event\EventInterface $event The beforeFilter event.
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        // This controller is admin-only.
        if (!$this->Authentication->getIdentity()) {
            $this->Flash->error(__('You must be logged in to access this page.'));
            $this->redirect(['controller' => 'Users', 'action' => 'login']);
            $event->stopPropagation();
            return;
        }

        // Check role authorization
        if (!$this->userHasRole(['superadmin', 'admin'])) {
            throw new \Cake\Http\Exception\ForbiddenException('You do not have permission to access this page.');
        }
    }

    /**
     * Index method - List all team members
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->viewBuilder()->setLayout('backend');
        $query = $this->TeamMembers->find()
            ->orderBy(['TeamMembers.department' => 'ASC', 'TeamMembers.sort_order' => 'ASC']);
        $teamMembers = $this->paginate($query);

        $this->set(compact('teamMembers'));
    }

    /**
     * View method
     *
     * @param string|null $id Team Member id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->setLayout('backend');
        $teamMember = $this->TeamMembers->get($id, contain: []);
        $this->set(compact('teamMember'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->viewBuilder()->setLayout('backend');
        $teamMember = $this->TeamMembers->newEmptyEntity();
        if ($this->request->is('post')) {
            $teamMember = $this->TeamMembers->patchEntity($teamMember, $this->request->getData());
            if ($this->TeamMembers->save($teamMember)) {
                $this->Flash->success(__('The team member has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The team member could not be saved. Please, try again.'));
        }
        $this->set(compact('teamMember'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Team Member id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->setLayout('backend');
        $teamMember = $this->TeamMembers->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $teamMember = $this->TeamMembers->patchEntity($teamMember, $this->request->getData());
            if ($this->TeamMembers->save($teamMember)) {
                $this->Flash->success(__('The team member has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The team member could not be saved. Please, try again.'));
        }
        $this->set(compact('teamMember'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Team Member id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $teamMember = $this->TeamMembers->get($id);
        if ($this->TeamMembers->delete($teamMember)) {
            $this->Flash->success(__('The team member has been deleted.'));
        } else {
            $this->Flash->error(__('The team member could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
