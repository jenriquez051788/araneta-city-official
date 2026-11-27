<?php
/**
 * Edit team member.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TeamMember $teamMember
 */
$this->assign('title', 'Edit Team Member');

$departments = [
    'ACI, Inc.' => 'ACI, Inc.',
    'Uniprom, Inc.' => 'Uniprom, Inc.',
    'Progressive Dev. Corp.' => 'Progressive Dev. Corp.',
    'Araneta Hotels' => 'Araneta Hotels',
    'Philippine Pizza' => 'Philippine Pizza',
];

$groupRoles = [
    'Leadership' => 'Leadership',
    'Executive Vice Presidents' => 'Executive Vice Presidents',
    'Senior Vice Presidents' => 'Senior Vice Presidents',
    'Vice Presidents' => 'Vice Presidents',
    'Assistant Vice Presidents' => 'Assistant Vice Presidents',
    'Controllers' => 'Controllers',
    'Officers' => 'Officers',
    'Consultants' => 'Consultants',
];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Team Member</h4>
    <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?= $this->Form->create($teamMember) ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <?= $this->Form->control('name', ['label' => false, 'class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Title</label>
                <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Department <span class="text-danger">*</span></label>
                <?= $this->Form->control('department', ['options' => $departments, 'label' => false, 'class' => 'form-select', 'empty' => 'Select a department', 'required' => true]) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Group Role</label>
                <?= $this->Form->control('group_role', ['options' => $groupRoles, 'label' => false, 'class' => 'form-select', 'empty' => 'Select a group role']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sort Order</label>
                <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => false, 'class' => 'form-control']) ?>
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
            <?= $this->Form->button('Save Team Member', ['class' => 'btn btn-primary']) ?>
            <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
