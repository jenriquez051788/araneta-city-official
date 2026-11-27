<?php
/**
 * Login layout - standalone centered card for the CMS sign-in screen.
 * Reuses the backend (Sneat) core CSS so the form matches the admin theme.
 *
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html>
<html lang="en" class="light-style" dir="ltr" data-theme="theme-default"
    data-assets-path="<?= $this->Url->build('/', ['fullBase' => false]) ?>">

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->fetch('title') ?: 'Sign In' ?> - Araneta City Admin</title>

    <link rel="icon" type="image/png" href="<?= $this->Url->image('AranetaCityFavicon2024.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">

    <?= $this->Html->css(['backend/core', 'backend/theme-default', 'backend/demo']) ?>
    <?= $this->Html->script('backend/helpers') ?>
    <?= $this->Html->script('backend/config') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner" style="max-width: 400px; margin: 0 auto; min-height: 100vh; display: flex; align-items: center;">
                <div class="card w-100">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4">
                            <a href="<?= $this->Url->build('/') ?>" class="app-brand-link gap-2">
                                <img src="<?= $this->Url->image('AranetaCityFavicon2024.png') ?>" alt="Araneta City"
                                    width="42" height="42">
                                <span class="app-brand-text demo text-body fw-bold ms-1">Araneta City</span>
                            </a>
                        </div>

                        <h4 class="mb-1 text-center">Welcome back</h4>
                        <p class="mb-4 text-center text-muted">Sign in to the CMS</p>

                        <?= $this->Flash->render() ?>

                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Html->script('jquery.min') ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <?= $this->Html->script('backend/bootstrap') ?>
    <?= $this->fetch('script') ?>
</body>

</html>
