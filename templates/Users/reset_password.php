<?php
/**
 * Reset password form: set a new password (with confirmation).
 *
 * @var \App\View\AppView $this
 * @var string $token The raw reset token from the emailed link.
 */
$this->assign('title', 'Reset Password');
?>
<div class="mb-3">
    <p class="text-muted small mb-0">
        Choose a new password for your account. It must be at least 8 characters long.
    </p>
</div>

<?= $this->Form->create(null, [
    'url' => ['controller' => 'Users', 'action' => 'resetPassword', $token],
]) ?>

<div class="mb-3 form-password-toggle">
    <label class="form-label" for="password">New Password</label>
    <?= $this->Form->control('password', [
        'type' => 'password',
        'label' => false,
        'id' => 'password',
        'class' => 'form-control',
        'placeholder' => '&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;',
        'required' => true,
        'minlength' => 8,
        'autofocus' => true,
    ]) ?>
</div>

<div class="mb-3 form-password-toggle">
    <label class="form-label" for="confirm_password">Verify Password</label>
    <?= $this->Form->control('confirm_password', [
        'type' => 'password',
        'label' => false,
        'id' => 'confirm_password',
        'class' => 'form-control',
        'placeholder' => '&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;',
        'required' => true,
        'minlength' => 8,
    ]) ?>
</div>

<div class="mb-3">
    <?= $this->Form->button('Change Password', ['class' => 'btn btn-primary d-grid w-100']) ?>
</div>
<?= $this->Form->end() ?>

<div class="text-center">
    <?= $this->Html->link('Back to Sign In', ['controller' => 'Users', 'action' => 'login'], ['class' => 'small']) ?>
</div>
