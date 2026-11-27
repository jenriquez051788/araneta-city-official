<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Mainpage> $mainpages
 */
?>
<div class="mainpages index content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold py-3 mb-0"><?= __('Mainpages') ?></h4>
        <?= $this->Html->link(
            '<i class="mdi mdi-plus me-1"></i>' . __('New Mainpage'),
            ['action' => 'add'],
            ['class' => 'btn btn-primary waves-effect waves-light', 'escape' => false]
        ) ?>
    </div>

    <div class="card">
        <h5 class="card-header"><?= __('Table Basic') ?></h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('title', __('Title')) ?></th>
                        <th><?= $this->Paginator->sort('slug', __('Slug')) ?></th>
                        <th><?= $this->Paginator->sort('hero_heading', __('Hero Heading')) ?></th>
                        <th><?= $this->Paginator->sort('status', __('Status')) ?></th>
                        <th><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php foreach ($mainpages as $mainpage): ?>
                    <tr>
                        <td>
                            <i class="mdi mdi-file-document-outline mdi-20px text-primary me-3"></i>
                            <span class="fw-medium"><?= h($mainpage->title) ?></span>
                        </td>
                        <td><?= h($mainpage->slug) ?></td>
                        <td><?= h($mainpage->hero_heading) ?></td>
                        <td>
                            <?php if ($mainpage->status) : ?>
                                <span class="badge rounded-pill bg-label-success me-1"><?= __('Active') ?></span>
                            <?php else : ?>
                                <span class="badge rounded-pill bg-label-secondary me-1"><?= __('Inactive') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <?= $this->Html->link(
                                        '<i class="mdi mdi-eye-outline me-1"></i> ' . __('View'),
                                        ['action' => 'view', $mainpage->id],
                                        ['class' => 'dropdown-item waves-effect', 'escape' => false]
                                    ) ?>
                                    <?= $this->Html->link(
                                        '<i class="mdi mdi-pencil-outline me-1"></i> ' . __('Edit'),
                                        ['action' => 'edit', $mainpage->id],
                                        ['class' => 'dropdown-item waves-effect', 'escape' => false]
                                    ) ?>
                                    <?= $this->Form->postLink(
                                        '<i class="mdi mdi-trash-can-outline me-1"></i> ' . __('Delete'),
                                        ['action' => 'delete', $mainpage->id],
                                        [
                                            'confirm' => __('Are you sure you want to delete # {0}?', $mainpage->id),
                                            'class' => 'dropdown-item waves-effect',
                                            'escape' => false,
                                        ]
                                    ) ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
            <p class="mb-0"><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
            <ul class="pagination mb-0">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
        </div>
    </div>
</div>