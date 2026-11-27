<?php
/**
 * Contact Submissions Index
 *
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Contact> $contacts
 */
?>

<div class="contacts index content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1><?= __('Contact Submissions') ?></h1>
    </div>

    <?php if (empty($contacts)): ?>
        <p style="color: #999; padding: 20px; text-align: center;">No contact submissions found.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #eee;">
            <thead>
                <tr style="background: #f9f9f9; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px; text-align: left; font-weight: 700; color: #333;">Name</th>
                    <th style="padding: 12px; text-align: left; font-weight: 700; color: #333;">Email</th>
                    <th style="padding: 12px; text-align: left; font-weight: 700; color: #333;">Subject</th>
                    <th style="padding: 12px; text-align: left; font-weight: 700; color: #333;">Status</th>
                    <th style="padding: 12px; text-align: left; font-weight: 700; color: #333;">Submitted</th>
                    <th style="padding: 12px; text-align: center; font-weight: 700; color: #333;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                    <?php
                    $fullName = trim($contact->first_name . ' ' . ($contact->middle_name ? $contact->middle_name . ' ' : '') . $contact->last_name);
                    $statusColor = match($contact->status) {
                        'new' => '#ff6b6b',
                        'read' => '#ffd43b',
                        'responded' => '#51cf66',
                        'closed' => '#a8aeff',
                        default => '#666',
                    };
                    $statusBg = match($contact->status) {
                        'new' => '#ffe6e6',
                        'read' => '#fff3cd',
                        'responded' => '#e6f9f2',
                        'closed' => '#f0f0ff',
                        default => '#f5f5f5',
                    };
                    ?>
                    <tr style="border-bottom: 1px solid #eee; hover: background: #f9f9f9;">
                        <td style="padding: 12px; color: #333;"><?= h($fullName) ?></td>
                        <td style="padding: 12px; color: #666;"><?= h($contact->email) ?></td>
                        <td style="padding: 12px; color: #333;"><?= h($contact->subject) ?></td>
                        <td style="padding: 12px;">
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: <?= $statusBg ?>; color: <?= $statusColor ?>; text-transform: uppercase;">
                                <?= h($contact->status) ?>
                            </span>
                        </td>
                        <td style="padding: 12px; color: #999; font-size: 13px;"><?= h($contact->created->setTimezone('Asia/Manila')->format('M d, Y g:i a')) ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <?= $this->Html->link('View', ['action' => 'view', $contact->id], ['style' => 'color: #00afab; text-decoration: none; margin-right: 12px;']) ?>
                            <?= $this->Html->link('Edit', ['action' => 'edit', $contact->id], ['style' => 'color: #6b2d7b; text-decoration: none; margin-right: 12px;']) ?>
                            <?= $this->Form->postLink('Delete', ['action' => 'delete', $contact->id], ['confirm' => 'Are you sure?', 'style' => 'color: #ff6b6b; text-decoration: none;']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div style="margin-top: 20px; padding: 16px; background: #f9f9f9; border-radius: 8px;">
            <ul class="pagination" style="list-style: none; padding: 0; margin: 0; display: flex; gap: 8px;">
                <?= $this->Paginator->first('« First') ?>
                <?= $this->Paginator->prev('‹ Previous') ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next('Next ›') ?>
                <?= $this->Paginator->last('Last »') ?>
            </ul>
            <p style="margin: 10px 0 0; color: #999; font-size: 13px;"><?= $this->Paginator->counter('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total') ?></p>
        </div>
    <?php endif; ?>
</div>
