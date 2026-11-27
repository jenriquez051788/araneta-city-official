<?php
/**
 * Forgot password form: request a reset link by email.
 *
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Forgot Password');
?>
<div class="mb-3">
    <p class="text-muted small mb-0">
        Enter your account email and we'll send you a link to reset your password.
    </p>
</div>

<?= $this->Form->create(null, ['url' => ['controller' => 'Users', 'action' => 'forgotPassword']]) ?>
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <?= $this->Form->control('email', [
        'type' => 'email',
        'label' => false,
        'id' => 'email',
        'class' => 'form-control',
        'placeholder' => 'Enter your email',
        'required' => true,
        'autofocus' => true,
    ]) ?>
</div>

<div class="mb-3">
    <?= $this->Form->button('Send Reset Link', ['class' => 'btn btn-primary d-grid w-100']) ?>
</div>
<?= $this->Form->end() ?>

<div class="text-center">
    <?= $this->Html->link('Back to Sign In', ['controller' => 'Users', 'action' => 'login'], ['class' => 'small']) ?>
</div>
