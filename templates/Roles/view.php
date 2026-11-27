<?php
/**
 * View role.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
$this->assign('title', 'Role Details');
$users = $role->users ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Role Details</h4>
    <div>
        <a href="<?= $this->Url->build(['action' => 'edit', $role->id]) ?>" class="btn btn-primary">
            <i class="mdi mdi-pencil-outline me-1"></i> Edit
        </a>
        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <th style="width: 200px;">Name</th>
                    <td><?= h($role->name) ?></td>
                </tr>
                <tr>
                    <th>Slug</th>
                    <td><code><?= h($role->slug) ?></code></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= h($role->description ?: '—') ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php if ((int)$role->status === 1): ?>
                            <span class="badge bg-label-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-label-secondary">Disabled</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h5 class="card-header">Users with this role (<?= count($users) ?>)</h5>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= h(trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: '—') ?></td>
                        <td><?= h($u->email) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">No users assigned.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
