<?php
/**
 * Edit role.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
$this->assign('title', 'Edit Role');
$isCore = in_array($role->slug, ['superadmin', 'admin', 'marketing'], true);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Role</h4>
    <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-1"></i> Back
    </a>
</div>

<?php if ($isCore): ?>
    <div class="alert alert-info">This is a core role. Its slug is locked to keep authorization working.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?= $this->Form->create($role) ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <?= $this->Form->control('name', ['label' => false, 'class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Slug</label>
                <?= $this->Form->control('slug', [
                    'label' => false,
                    'class' => 'form-control',
                    'readonly' => $isCore,
                ]) ?>
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <?= $this->Form->control('description', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 2]) ?>
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
