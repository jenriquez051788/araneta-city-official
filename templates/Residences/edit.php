<?php
/**
 * Edit residence.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Residence $residence
 */
$this->assign('title', 'Edit Residence');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Residence: <?= h($residence->title) ?></h4>
    <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <?= $this->Form->create($residence) ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Slug <span class="text-danger">*</span></label>
                <?= $this->Form->control('slug', ['label' => false, 'class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="col-md-12">
                <label class="form-label">Subtitle</label>
                <?= $this->Form->control('subtitle', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <?= $this->Form->control('description', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 5]) ?>
            </div>

            <div class="col-md-4">
                <label class="form-label">Towers Count</label>
                <?= $this->Form->control('towers_count', ['type' => 'number', 'label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Apartments Count</label>
                <?= $this->Form->control('apartments_count', ['type' => 'number', 'label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Residents Count</label>
                <?= $this->Form->control('residents_count', ['type' => 'number', 'label' => false, 'class' => 'form-control']) ?>
            </div>

            <div class="col-md-6">
                <label class="form-label">Developer</label>
                <?= $this->Form->control('developer', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Location</label>
                <?= $this->Form->control('location', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-12">
                <label class="form-label">Location Description</label>
                <?= $this->Form->control('location_description', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 2]) ?>
            </div>
            <div class="col-md-12">
                <label class="form-label">Featured Image URL</label>
                <?= $this->Form->control('image_url', ['label' => false, 'class' => 'form-control']) ?>
            </div>

            <div class="col-md-6">
                <label class="form-label">Website</label>
                <?= $this->Form->control('website', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Facebook</label>
                <?= $this->Form->control('facebook', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Instagram</label>
                <?= $this->Form->control('instagram', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Twitter / X</label>
                <?= $this->Form->control('twitter', ['label' => false, 'class' => 'form-control']) ?>
            </div>

            <div class="col-md-4">
                <label class="form-label">Sort Order</label>
                <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Featured</label>
                <?= $this->Form->control('featured', [
                    'options' => [1 => 'Yes', 0 => 'No'],
                    'label' => false,
                    'class' => 'form-select',
                ]) ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <?= $this->Form->control('status', [
                    'options' => [1 => 'Active', 0 => 'Disabled'],
                    'label' => false,
                    'class' => 'form-select',
                ]) ?>
            </div>
        </div>

        <div class="mt-4">
            <?= $this->Form->button('Save Residence', ['class' => 'btn btn-primary']) ?>
            <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<!-- Phases / Towers -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Phases / Towers</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($residence->residence_phases)): ?>
            <div class="table-responsive mb-3">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Phase Name</th>
                            <th>Order</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($residence->residence_phases as $phase): ?>
                            <tr>
                                <td><?= h($phase->phase_name) ?></td>
                                <td><?= h($phase->sort_order) ?></td>
                                <td class="text-end">
                                    <?= $this->Form->postLink(
                                        '<i class="mdi mdi-trash-can-outline"></i>',
                                        ['action' => 'deletePhase', $phase->id],
                                        [
                                            'escape' => false,
                                            'class' => 'btn btn-sm btn-icon text-danger',
                                            'title' => 'Delete',
                                            'confirm' => sprintf('Delete phase "%s"?', $phase->phase_name),
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No phases added yet.</p>
        <?php endif; ?>

        <?= $this->Form->create(null, ['url' => ['action' => 'addPhase', $residence->id]]) ?>
        <div class="row g-2 align-items-end">
            <div class="col-md-7">
                <label class="form-label">Phase Name</label>
                <?= $this->Form->control('phase_name', ['label' => false, 'class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort Order</label>
                <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => false, 'class' => 'form-control', 'value' => 0]) ?>
            </div>
            <div class="col-md-2">
                <?= $this->Form->button('<i class="mdi mdi-plus me-1"></i> Add', ['class' => 'btn btn-primary w-100', 'escapeTitle' => false]) ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<!-- Gallery -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gallery Photos</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($residence->residence_gallery)): ?>
            <div class="row g-3 mb-3">
                <?php foreach ($residence->residence_gallery as $photo): ?>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <img src="<?= h($photo->image_url) ?>" class="card-img-top"
                                alt="<?= h($photo->alt_text) ?>" style="height: 140px; object-fit: cover;">
                            <div class="card-body p-2">
                                <p class="mb-1 small"><strong><?= h($photo->caption ?: '—') ?></strong></p>
                                <p class="mb-1 small text-muted">Order: <?= h($photo->sort_order) ?></p>
                                <?= $this->Form->postLink(
                                    '<i class="mdi mdi-trash-can-outline"></i> Delete',
                                    ['action' => 'deleteGallery', $photo->id],
                                    [
                                        'escape' => false,
                                        'class' => 'btn btn-sm btn-outline-danger w-100',
                                        'confirm' => 'Delete this gallery photo?',
                                    ]
                                ) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No gallery photos added yet.</p>
        <?php endif; ?>

        <?= $this->Form->create(null, ['url' => ['action' => 'addGallery', $residence->id]]) ?>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Image URL <span class="text-danger">*</span></label>
                <?= $this->Form->control('image_url', ['label' => false, 'class' => 'form-control', 'required' => true]) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Caption</label>
                <?= $this->Form->control('caption', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Alt Text</label>
                <?= $this->Form->control('alt_text', ['label' => false, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort Order</label>
                <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => false, 'class' => 'form-control', 'value' => 0]) ?>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <?= $this->Form->button('<i class="mdi mdi-plus me-1"></i> Add Photo', ['class' => 'btn btn-primary w-100', 'escapeTitle' => false]) ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
