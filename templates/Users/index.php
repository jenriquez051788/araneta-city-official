<?php
/**
 * Users index.
 *
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\User> $users
 */
$this->assign('title', 'Users');
$usersList = is_array($users) ? $users : iterator_to_array($users);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Users</h4>
    <a href="<?= $this->Url->build(['action' => 'add']) ?>" class="btn btn-primary">
        <i class="mdi mdi-plus me-1"></i> Add User
    </a>
</div>

<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php foreach ($usersList as $user): ?>
                    <tr>
                        <td><strong><?= h($user->full_name ?: '—') ?></strong></td>
                        <td><?= h($user->email) ?></td>
                        <td><span class="badge bg-label-primary"><?= h($user->role->name ?? '—') ?></span></td>
                        <td>
                            <?php if ((int)$user->status === 1): ?>
                                <span class="badge bg-label-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $user->last_login ? h($user->last_login->format('M d, Y H:i')) : '—' ?></td>
                        <td class="text-end">
                            <a href="<?= $this->Url->build(['action' => 'view', $user->id]) ?>"
                                class="btn btn-sm btn-icon" title="View"><i class="mdi mdi-eye-outline"></i></a>
                            <a href="<?= $this->Url->build(['action' => 'edit', $user->id]) ?>"
                                class="btn btn-sm btn-icon" title="Edit"><i class="mdi mdi-pencil-outline"></i></a>
                            <?= $this->Form->postLink(
                                '<i class="mdi mdi-trash-can-outline"></i>',
                                ['action' => 'delete', $user->id],
                                [
                                    'escape' => false,
                                    'class' => 'btn btn-sm btn-icon text-danger',
                                    'title' => 'Delete',
                                    'confirm' => sprintf('Delete user "%s"?', $user->email),
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($usersList)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No users yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
