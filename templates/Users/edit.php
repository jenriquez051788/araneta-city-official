<?php
/**
 * Edit user.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \Cake\Collection\CollectionInterface|string[] $roles
 */
$this->assign('title', 'Edit User');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit User</h4>
    <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?= $this->Form->create($user) ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <?= $this->Form->control('first_name', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <?= $this->Form->control('last_name', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <?= $this->Form->control('email', ['type' => 'email', 'label' => false, 'class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <?= $this->Form->control('role_id', ['options' => $roles, 'label' => false, 'class' => 'form-select', 'required' => true]) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">New Password</label>
                <?= $this->Form->control('password', ['type' => 'password', 'label' => false, 'class' => 'form-control', 'value' => '', 'autocomplete' => 'new-password']) ?>
                <small class="text-muted">Leave blank to keep the current password.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <?= $this->Form->control('status', [
                    'options' => [1 => 'Active', 0 => 'Disabled'],
                    'label' => false,
                    'class' => 'form-select',
                ]) ?>
            </div>
        </div>
        <div class="mt-4">
            <?= $this->Form->button('Save Changes', ['class' => 'btn btn-primary']) ?>
            <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
