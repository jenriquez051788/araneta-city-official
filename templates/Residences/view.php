<?php
/**
 * View residence.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Residence $residence
 */
$this->assign('title', 'View Residence');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><?= h($residence->title) ?></h4>
    <div>
        <a href="<?= $this->Url->build(['action' => 'edit', $residence->id]) ?>" class="btn btn-primary">
            <i class="mdi mdi-pencil-outline me-1"></i> Edit
        </a>
        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <?php if (!empty($residence->image_url)): ?>
                    <img src="<?= h($residence->image_url) ?>" alt="<?= h($residence->title) ?>"
                        class="img-fluid rounded mb-3" style="max-height: 300px; width: 100%; object-fit: cover;">
                <?php endif; ?>
                <table class="table table-borderless">
                    <tr><th style="width: 200px;">Title</th><td><?= h($residence->title) ?></td></tr>
                    <tr><th>Slug</th><td><?= h($residence->slug) ?></td></tr>
                    <tr><th>Subtitle</th><td><?= h($residence->subtitle ?: '—') ?></td></tr>
                    <tr><th>Description</th><td><?= nl2br(h($residence->description ?: '—')) ?></td></tr>
                    <tr><th>Developer</th><td><?= h($residence->developer ?: '—') ?></td></tr>
                    <tr><th>Location</th><td><?= h($residence->location ?: '—') ?></td></tr>
                    <tr><th>Website</th><td><?= h($residence->website ?: '—') ?></td></tr>
                </table>
            </div>
        </div>

        <?php if (!empty($residence->residence_phases)): ?>
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Phases / Towers</h5></div>
                <div class="card-body">
                    <?php foreach ($residence->residence_phases as $phase): ?>
                        <span class="badge bg-label-primary me-1 mb-1"><?= h($phase->phase_name) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($residence->residence_gallery)): ?>
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Gallery</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($residence->residence_gallery as $photo): ?>
                            <div class="col-md-4">
                                <img src="<?= h($photo->image_url) ?>" class="img-fluid rounded"
                                    alt="<?= h($photo->alt_text) ?>" style="height: 140px; width: 100%; object-fit: cover;">
                                <p class="small text-muted mt-1 mb-0"><?= h($photo->caption ?: '') ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Statistics</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Towers</th><td><?= h($residence->towers_count ?? '—') ?></td></tr>
                    <tr><th>Apartments</th><td><?= $residence->apartments_count !== null ? h(number_format((int)$residence->apartments_count)) : '—' ?></td></tr>
                    <tr><th>Residents</th><td><?= $residence->residents_count !== null ? h(number_format((int)$residence->residents_count)) : '—' ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Settings</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Featured</th><td><?= (int)$residence->featured === 1 ? 'Yes' : 'No' ?></td></tr>
                    <tr><th>Status</th><td><?= (int)$residence->status === 1 ? 'Active' : 'Disabled' ?></td></tr>
                    <tr><th>Sort Order</th><td><?= h($residence->sort_order) ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
