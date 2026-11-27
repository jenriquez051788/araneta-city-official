<?php
/**
 * Contact Submission View
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contact $contact
 */
?>

<div class="contact view content">
    <div style="margin-bottom: 24px;">
        <h1 style="margin: 0 0 8px; font-size: 32px; font-weight: 800; color: #1a1a1a;">
            <?= h($contact->first_name . ' ' . $contact->last_name) ?>
        </h1>
        <p style="margin: 0; color: #888; font-size: 14px;">
            Submitted on <?= h($contact->created->setTimezone('Asia/Manila')->format('F j, Y \a\t g:i a')) ?>
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Main content -->
        <div>
            <!-- Card: Subject -->
            <div style="border: 1px solid #eee; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0 0 12px; font-size: 16px; font-weight: 700; color: #1a1a1a;">Subject</h2>
                <p style="margin: 0; color: #555; font-size: 15px; line-height: 1.6;">
                    <?= h($contact->subject) ?>
                </p>
            </div>

            <!-- Card: Message -->
            <div style="border: 1px solid #eee; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0 0 12px; font-size: 16px; font-weight: 700; color: #1a1a1a;">Message</h2>
                <p style="margin: 0; color: #555; font-size: 15px; line-height: 1.8; white-space: pre-wrap;">
                    <?= h($contact->message) ?>
                </p>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Card: Contact Information -->
            <div style="border: 1px solid #eee; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #1a1a1a; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;">Contact Information</h2>
                
                <div style="margin-bottom: 16px;">
                    <p style="margin: 0 0 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #999; letter-spacing: 0.5px;">Email</p>
                    <p style="margin: 0; color: #00afab;"><a href="mailto:<?= h($contact->email) ?>" style="color: #00afab; text-decoration: none;"><?= h($contact->email) ?></a></p>
                </div>

                <div style="margin-bottom: 16px;">
                    <p style="margin: 0 0 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #999; letter-spacing: 0.5px;">Phone</p>
                    <p style="margin: 0; color: #333;"><a href="tel:<?= h($contact->contact_number) ?>" style="color: #333; text-decoration: none;"><?= h($contact->contact_number) ?></a></p>
                </div>

                <?php if (!empty($contact->middle_name)): ?>
                    <div>
                        <p style="margin: 0 0 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #999; letter-spacing: 0.5px;">Full Name</p>
                        <p style="margin: 0; color: #333;"><?= h($contact->first_name . ' ' . $contact->middle_name . ' ' . $contact->last_name) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Card: Status -->
            <div style="border: 1px solid #eee; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #1a1a1a; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;">Status</h2>
                
                <?php
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
                <span style="display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; background: <?= $statusBg ?>; color: <?= $statusColor ?>; text-transform: uppercase;">
                    <?= h($contact->status) ?>
                </span>
                
                <div style="margin-top: 16px;">
                    <?= $this->Html->link('Change Status', ['action' => 'edit', $contact->id], ['style' => 'display: inline-block; background: #6b2d7b; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;']) ?>
                </div>
            </div>

            <!-- Card: Actions -->
            <div style="border: 1px solid #eee; border-radius: 10px; padding: 20px;">
                <h2 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #1a1a1a; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;">Actions</h2>
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <?= $this->Html->link('Back to List', ['action' => 'index'], ['style' => 'display: block; background: #1a1a1a; color: #fff; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; text-align: center;']) ?>
                    
                    <?= $this->Form->postLink('Delete', ['action' => 'delete', $contact->id], ['confirm' => 'Are you sure?', 'style' => 'display: block; background: #ff6b6b; color: #fff; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; text-align: center;']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
