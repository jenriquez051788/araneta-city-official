<?php
/**
 * View team member.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TeamMember $teamMember
 */
$this->assign('title', 'Team Member Details');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Team Member Details</h4>
    <div>
        <a href="<?= $this->Url->build(['action' => 'edit', $teamMember->id]) ?>" class="btn btn-primary">
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
                    <td><?= h($teamMember->name) ?></td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td><?= h($teamMember->title ?: '—') ?></td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td><span class="badge bg-label-primary"><?= h($teamMember->department ?? '—') ?></span></td>
                </tr>
                <tr>
                    <th>Group Role</th>
                    <td><span class="badge bg-label-info"><?= h($teamMember->group_role ?? '—') ?></span></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php if ((int)$teamMember->status === 1): ?>
                            <span class="badge bg-label-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-label-secondary">Disabled</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Sort Order</th>
                    <td><?= h($teamMember->sort_order) ?></td>
                </tr>
                <tr>
                    <th>Created</th>
                    <td><?= $teamMember->created ? h($teamMember->created->format('M d, Y H:i')) : '—' ?></td>
                </tr>
                <tr>
                    <th>Modified</th>
                    <td><?= $teamMember->modified ? h($teamMember->modified->format('M d, Y H:i')) : '—' ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
