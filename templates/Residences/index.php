<?php
/**
 * Residences index.
 *
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Residence> $residences
 */
$this->assign('title', 'Residences');
$residencesList = is_array($residences) ? $residences : iterator_to_array($residences);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Residences</h4>
    <a href="<?= $this->Url->build(['action' => 'add']) ?>" class="btn btn-primary">
        <i class="mdi mdi-plus me-1"></i> Add Residence
    </a>
</div>

<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Developer</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php foreach ($residencesList as $residence): ?>
                    <tr>
                        <td><strong><?= h($residence->title) ?></strong></td>
                        <td><?= h($residence->location ?: '—') ?></td>
                        <td><span class="badge bg-label-primary"><?= h($residence->developer ?? '—') ?></span></td>
                        <td>
                            <?php if ((int)$residence->featured === 1): ?>
                                <span class="badge bg-label-warning">Featured</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int)$residence->status === 1): ?>
                                <span class="badge bg-label-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td><?= h($residence->sort_order) ?></td>
                        <td class="text-end">
                            <a href="<?= $this->Url->build(['action' => 'view', $residence->id]) ?>"
                                class="btn btn-sm btn-icon" title="View"><i class="mdi mdi-eye-outline"></i></a>
                            <a href="<?= $this->Url->build(['action' => 'edit', $residence->id]) ?>"
                                class="btn btn-sm btn-icon" title="Edit"><i class="mdi mdi-pencil-outline"></i></a>
                            <?= $this->Form->postLink(
                                '<i class="mdi mdi-trash-can-outline"></i>',
                                ['action' => 'delete', $residence->id],
                                [
                                    'escape' => false,
                                    'class' => 'btn btn-sm btn-icon text-danger',
                                    'title' => 'Delete',
                                    'confirm' => sprintf('Delete residence "%s"?', $residence->title),
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($residencesList)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No residences yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($this->Paginator->hasPage(2) || $this->Paginator->total() > 0): ?>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            <?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} of {{count}} total')) ?>
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?= $this->Paginator->first('<i class="mdi mdi-chevron-double-left"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->prev('<i class="mdi mdi-chevron-left"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next('<i class="mdi mdi-chevron-right"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->last('<i class="mdi mdi-chevron-double-right"></i>', ['escape' => false]) ?>
            </ul>
        </nav>
    </div>
<?php endif; ?>
