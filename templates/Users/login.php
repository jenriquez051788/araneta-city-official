<?php
/**
 * CMS login form.
 *
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Sign In');
?>
<?= $this->Form->create(null, ['url' => ['controller' => 'Users', 'action' => 'login']]) ?>
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

<div class="mb-3 form-password-toggle">
    <label class="form-label" for="password">Password</label>
    <?= $this->Form->control('password', [
        'type' => 'password',
        'label' => false,
        'id' => 'password',
        'class' => 'form-control',
        'placeholder' => '&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;',
        'required' => true,
    ]) ?>
</div>

<div class="mb-3 text-end">
    <?= $this->Html->link('Forgot password?', ['controller' => 'Users', 'action' => 'forgotPassword'], ['class' => 'small']) ?>
</div>

<div class="mb-3">
    <?= $this->Form->button('Sign In', ['class' => 'btn btn-primary d-grid w-100']) ?>
</div>
<?= $this->Form->end() ?>
