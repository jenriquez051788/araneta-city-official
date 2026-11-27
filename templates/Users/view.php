<?php
/**
 * View user.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'User Details');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">User Details</h4>
    <div>
        <a href="<?= $this->Url->build(['action' => 'edit', $user->id]) ?>" class="btn btn-primary">
            <i class="mdi mdi-pencil-outline me-1"></i> Edit
        </a>
        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <th style="width: 200px;">Name</th>
                    <td><?= h($user->full_name ?: '—') ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= h($user->email) ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><span class="badge bg-label-primary"><?= h($user->role->name ?? '—') ?></span></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php if ((int)$user->status === 1): ?>
                            <span class="badge bg-label-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-label-secondary">Disabled</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Last Login</th>
                    <td><?= $user->last_login ? h($user->last_login->format('M d, Y H:i')) : '—' ?></td>
                </tr>
                <tr>
                    <th>Created</th>
                    <td><?= h($user->created->format('M d, Y H:i')) ?></td>
                </tr>
                <tr>
                    <th>Modified</th>
                    <td><?= h($user->modified->format('M d, Y H:i')) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
