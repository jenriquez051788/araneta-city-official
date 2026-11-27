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
            <?= $this->Html->link(__('Edit Mainpage'), ['action' => 'edit', $mainpage->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Mainpage'), ['action' => 'delete', $mainpage->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mainpage->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Mainpages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Mainpage'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="mainpages view content">
            <h3><?= h($mainpage->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($mainpage->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Slug') ?></th>
                    <td><?= h($mainpage->slug) ?></td>
                </tr>
                <tr>
                    <th><?= __('Hero Heading') ?></th>
                    <td><?= h($mainpage->hero_heading) ?></td>
                </tr>
                <tr>
                    <th><?= __('Hero Image') ?></th>
                    <td><?= h($mainpage->hero_image) ?></td>
                </tr>
                <tr>
                    <th><?= __('Meta Title') ?></th>
                    <td><?= h($mainpage->meta_title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Meta Description') ?></th>
                    <td><?= h($mainpage->meta_description) ?></td>
                </tr>
                <tr>
                    <th><?= __('Meta Keywords') ?></th>
                    <td><?= h($mainpage->meta_keywords) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($mainpage->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= $this->Number->format($mainpage->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($mainpage->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($mainpage->modified) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Content') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($mainpage->content)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>