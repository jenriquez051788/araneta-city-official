<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Contacts Controller
 *
 * @property \App\Model\Table\ContactsTable $Contacts
 */
class ContactsController extends AppController
{
    /**
     * Before filter callback
     *
     * @param \Cake\Event\EventInterface $event The beforeFilter event.
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        // This controller is admin-only. Public visitors submit inquiries via
        // Mainpages::contact(), which saves directly to the Contacts table.
        $adminActions = ['index', 'view', 'edit', 'delete'];
        if (in_array($this->request->getParam('action'), $adminActions)) {
            // Check if user is authenticated
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
    }

    /**
     * Index method - List all contact submissions
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->viewBuilder()->setLayout('backend');
        $query = $this->Contacts->find()->orderBy(['Contacts.created' => 'DESC']);
        $contacts = $this->paginate($query);

        $this->set(compact('contacts'));
    }

    /**
     * View method - Show single contact submission
     *
     * @param int|null $id Contact id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?int $id = null)
    {
        $this->viewBuilder()->setLayout('backend');
        $contact = $this->Contacts->get($id, contain: []);
        
        // Mark as read if it's new
        if ($contact->status === 'new') {
            $contact->status = 'read';
            $this->Contacts->save($contact);
        }
        
        $this->set(compact('contact'));
    }

    /**
     * Edit method - Update contact status or notes
     *
     * @param int|null $id Contact id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?int $id = null)
    {
        $this->viewBuilder()->setLayout('backend');
        $contact = $this->Contacts->get($id, contain: []);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            // Only allow updating status
            $contact = $this->Contacts->patchEntity($contact, $data, ['fieldList' => ['status']]);

            if ($this->Contacts->save($contact)) {
                $this->Flash->success(__('The contact status has been updated.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The contact could not be updated. Please, try again.'));
        }

        $this->set(compact('contact'));
    }

    /**
     * Delete method
     *
     * @param int|null $id Contact id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?int $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $contact = $this->Contacts->get($id);

        if ($this->Contacts->delete($contact)) {
            $this->Flash->success(__('The contact has been deleted.'));
        } else {
            $this->Flash->error(__('The contact could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
