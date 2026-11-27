<?php
/**
 * Contact Submission Edit (Status Update)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contact $contact
 */
?>

<div class="contact edit content">
    <div style="margin-bottom: 24px;">
        <h1 style="margin: 0 0 8px; font-size: 32px; font-weight: 800; color: #1a1a1a;">
            Update Status
        </h1>
        <p style="margin: 0; color: #888; font-size: 14px;">
            Contact: <?= h($contact->first_name . ' ' . $contact->last_name) ?> (<?= h($contact->email) ?>)
        </p>
    </div>

    <div style="max-width: 500px;">
        <?= $this->Form->create($contact) ?>
        
        <div style="border: 1px solid #eee; border-radius: 10px; padding: 24px; margin-bottom: 20px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Status</label>
                
                <?= $this->Form->control('status', [
                    'options' => [
                        'new' => 'New',
                        'read' => 'Read',
                        'responded' => 'Responded',
                        'closed' => 'Closed',
                    ],
                    'label' => false,
                    'style' => 'width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; color: #333;',
                ]) ?>
            </div>

            <div style="display: flex; gap: 12px;">
                <?= $this->Form->button('Update Status', ['style' => 'flex: 1; background: #6b2d7b; color: #fff; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;']) ?>
                <?= $this->Html->link('Cancel', ['action' => 'view', $contact->id], ['style' => 'flex: 1; display: inline-block; background: #e9e9e9; color: #333; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; text-align: center;']) ?>
            </div>
        </div>

        <?= $this->Form->end() ?>

        <!-- Original Message Info -->
        <div style="border: 1px solid #eee; border-radius: 10px; padding: 20px; background: #f9f9f9;">
            <h3 style="margin: 0 0 12px; font-size: 14px; font-weight: 700; color: #1a1a1a;">Original Message</h3>
            <p style="margin: 0 0 8px; color: #666; font-size: 13px;">
                <strong>Subject:</strong> <?= h($contact->subject) ?>
            </p>
            <p style="margin: 0; color: #666; font-size: 13px; line-height: 1.6; white-space: pre-wrap; max-height: 200px; overflow-y: auto;">
                <?= h($contact->message) ?>
            </p>
        </div>
    </div>
</div>
