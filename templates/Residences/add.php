<?php
/**
 * Add residence.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Residence $residence
 */
$this->assign('title', 'Add Residence');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Residence</h4>
    <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card">
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
                <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => false, 'class' => 'form-control', 'default' => 0]) ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Featured</label>
                <?= $this->Form->control('featured', [
                    'options' => [1 => 'Yes', 0 => 'No'],
                    'default' => 0,
                    'label' => false,
                    'class' => 'form-select',
                ]) ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <?= $this->Form->control('status', [
                    'options' => [1 => 'Active', 0 => 'Disabled'],
                    'default' => 1,
                    'label' => false,
                    'class' => 'form-select',
                ]) ?>
            </div>
        </div>

        <div class="alert alert-info mt-4 mb-0">
            <i class="mdi mdi-information-outline me-1"></i>
            Save the residence first, then add Phases/Towers and Gallery photos from the Edit page.
        </div>

        <div class="mt-4">
            <?= $this->Form->button('Save Residence', ['class' => 'btn btn-primary']) ?>
            <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
