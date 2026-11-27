<?php
/**
 * Roles index.
 *
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Role> $roles
 */
$this->assign('title', 'Roles');
$rolesList = is_array($roles) ? $roles : iterator_to_array($roles);
$protected = ['superadmin', 'admin', 'marketing'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Roles</h4>
    <a href="<?= $this->Url->build(['action' => 'add']) ?>" class="btn btn-primary">
        <i class="mdi mdi-plus me-1"></i> Add Role
    </a>
</div>

<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php foreach ($rolesList as $role): ?>
                    <tr>
                        <td><strong><?= h($role->name) ?></strong></td>
                        <td><code><?= h($role->slug) ?></code></td>
                        <td><?= h($role->description ?: '—') ?></td>
                        <td>
                            <?php if ((int)$role->status === 1): ?>
                                <span class="badge bg-label-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= $this->Url->build(['action' => 'view', $role->id]) ?>"
                                class="btn btn-sm btn-icon" title="View"><i class="mdi mdi-eye-outline"></i></a>
                            <a href="<?= $this->Url->build(['action' => 'edit', $role->id]) ?>"
                                class="btn btn-sm btn-icon" title="Edit"><i class="mdi mdi-pencil-outline"></i></a>
                            <?php if (!in_array($role->slug, $protected, true)): ?>
                                <?= $this->Form->postLink(
                                    '<i class="mdi mdi-trash-can-outline"></i>',
                                    ['action' => 'delete', $role->id],
                                    [
                                        'escape' => false,
                                        'class' => 'btn btn-sm btn-icon text-danger',
                                        'title' => 'Delete',
                                        'confirm' => sprintf('Delete role "%s"?', $role->name),
                                    ]
                                ) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rolesList)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No roles yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
