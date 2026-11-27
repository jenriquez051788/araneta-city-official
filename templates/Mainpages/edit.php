<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Mainpage $mainpage
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $mainpage->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $mainpage->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Mainpages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="card mb-4">
            <h5 class="card-header"><?= __('Edit Mainpage') ?></h5>
            <div class="card-body">
                <?= $this->Form->create($mainpage, ['type' => 'file', 'url' => ['action' => 'edit', $mainpage->id]]) ?>

                <div class="form-floating form-floating-outline mb-4">
                    <?= $this->Form->control('title', [
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __('Title'),
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                    <label for="title"><?= __('Title') ?></label>
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <?= $this->Form->control('slug', [
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __('Slug'),
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                    <label for="slug"><?= __('Slug') ?></label>
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <?= $this->Form->control('hero_heading', [
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __('Hero Heading'),
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                    <label for="hero-heading"><?= __('Hero Heading') ?></label>
                </div>

                <div class="mb-4">
                    <label for="hero-image" class="form-label"><?= __('Hero Image') ?></label>
                    <?= $this->Form->file('hero_image', [
                        'class' => 'form-control',
                        'accept' => 'image/*',
                        'id' => 'hero-image',
                    ]) ?>
                    <small class="form-text text-muted">Supported formats: JPG, PNG, GIF (Max 5MB)</small>
                    <?php if (!empty($mainpage->hero_image)): ?>
                        <div class="mt-2">
                            <small class="form-text"><?= __('Current image: ') ?><?= h($mainpage->hero_image) ?></small>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <?= $this->Form->control('content', [
                        'label' => false,
                        'class' => 'form-control h-px-150',
                        'placeholder' => __('Content'),
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                    <label for="content"><?= __('Content') ?></label>
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <?= $this->Form->control('meta_title', [
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __('Meta Title'),
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                    <label for="meta-title"><?= __('Meta Title') ?></label>
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <?= $this->Form->control('meta_description', [
                        'label' => false,
                        'class' => 'form-control h-px-100',
                        'placeholder' => __('Meta Description'),
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                    <label for="meta-description"><?= __('Meta Description') ?></label>
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <?= $this->Form->control('meta_keywords', [
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __('Meta Keywords'),
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                    <label for="meta-keywords"><?= __('Meta Keywords') ?></label>
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <?= $this->Form->control('status', [
                        'label' => false,
                        'class' => 'form-select',
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                    <label for="status"><?= __('Status') ?></label>
                </div>

                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary mt-2']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>