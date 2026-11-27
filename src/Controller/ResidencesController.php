<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Residences Controller
 *
 * @property \App\Model\Table\ResidencesTable $Residences
 */
class ResidencesController extends AppController
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
     * Index method - List all residences
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->viewBuilder()->setLayout('backend');
        $query = $this->Residences->find()
            ->orderBy(['Residences.sort_order' => 'ASC', 'Residences.title' => 'ASC']);
        $residences = $this->paginate($query);

        $this->set(compact('residences'));
    }

    /**
     * View method
     *
     * @param string|null $id Residence id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->setLayout('backend');
        $residence = $this->Residences->get($id, contain: [
            'ResidencePhases' => ['sort' => ['ResidencePhases.sort_order' => 'ASC']],
            'ResidenceGallery' => ['sort' => ['ResidenceGallery.sort_order' => 'ASC']],
        ]);
        $this->set(compact('residence'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->viewBuilder()->setLayout('backend');
        $residence = $this->Residences->newEmptyEntity();
        if ($this->request->is('post')) {
            $residence = $this->Residences->patchEntity($residence, $this->request->getData(), [
                'associated' => ['ResidencePhases', 'ResidenceGallery'],
            ]);
            if ($this->Residences->save($residence)) {
                $this->Flash->success(__('The residence has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The residence could not be saved. Please, try again.'));
        }
        $this->set(compact('residence'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Residence id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->setLayout('backend');
        $residence = $this->Residences->get($id, contain: ['ResidencePhases', 'ResidenceGallery']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $residence = $this->Residences->patchEntity($residence, $this->request->getData(), [
                'associated' => ['ResidencePhases', 'ResidenceGallery'],
            ]);
            if ($this->Residences->save($residence)) {
                $this->Flash->success(__('The residence has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The residence could not be saved. Please, try again.'));
        }
        $this->set(compact('residence'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Residence id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $residence = $this->Residences->get($id);
        if ($this->Residences->delete($residence)) {
            $this->Flash->success(__('The residence has been deleted.'));
        } else {
            $this->Flash->error(__('The residence could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Add phase method
     *
     * @param string|null $residenceId Residence id.
     * @return \Cake\Http\Response|null Redirects to edit.
     */
    public function addPhase($residenceId = null)
    {
        $this->request->allowMethod(['post']);
        $this->Residences->get($residenceId);
        $phase = $this->Residences->ResidencePhases->newEntity($this->request->getData());
        $phase->residence_id = (int)$residenceId;

        if ($this->Residences->ResidencePhases->save($phase)) {
            $this->Flash->success(__('Phase added successfully.'));
        } else {
            $this->Flash->error(__('Could not add phase.'));
        }

        return $this->redirect(['action' => 'edit', $residenceId]);
    }

    /**
     * Delete phase method
     *
     * @param string|null $phaseId Phase id.
     * @return \Cake\Http\Response|null Redirects to edit.
     */
    public function deletePhase($phaseId = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $phase = $this->Residences->ResidencePhases->get($phaseId);
        $residenceId = $phase->residence_id;

        if ($this->Residences->ResidencePhases->delete($phase)) {
            $this->Flash->success(__('Phase deleted successfully.'));
        } else {
            $this->Flash->error(__('Could not delete phase.'));
        }

        return $this->redirect(['action' => 'edit', $residenceId]);
    }

    /**
     * Add gallery image method
     *
     * @param string|null $residenceId Residence id.
     * @return \Cake\Http\Response|null Redirects to edit.
     */
    public function addGallery($residenceId = null)
    {
        $this->request->allowMethod(['post']);
        $this->Residences->get($residenceId);
        $gallery = $this->Residences->ResidenceGallery->newEntity($this->request->getData());
        $gallery->residence_id = (int)$residenceId;

        if ($this->Residences->ResidenceGallery->save($gallery)) {
            $this->Flash->success(__('Gallery image added successfully.'));
        } else {
            $this->Flash->error(__('Could not add gallery image.'));
        }

        return $this->redirect(['action' => 'edit', $residenceId]);
    }

    /**
     * Delete gallery image method
     *
     * @param string|null $galleryId Gallery id.
     * @return \Cake\Http\Response|null Redirects to edit.
     */
    public function deleteGallery($galleryId = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gallery = $this->Residences->ResidenceGallery->get($galleryId);
        $residenceId = $gallery->residence_id;

        if ($this->Residences->ResidenceGallery->delete($gallery)) {
            $this->Flash->success(__('Gallery image deleted successfully.'));
        } else {
            $this->Flash->error(__('Could not delete gallery image.'));
        }

        return $this->redirect(['action' => 'edit', $residenceId]);
    }
}
