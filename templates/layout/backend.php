<?php
/**
 * Backend (admin) layout for the Materialize vertical-menu admin template.
 *
 * Head, vendor CSS/JS, and core scripts are wired up to match the reference
 * "vertical-menu-template-no-customizer" starter kit. Page-specific content
 * (sidebar menu items, page body) is provided by $this->fetch('content').
 *
 * @var \App\View\AppView $this
 */

$cakeDescription = 'Araneta City - Admin';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="<?= $this->Url->build('/', ['fullBase' => false]) ?>"
    data-template="vertical-menu-template-no-customizer-starter">

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>
        <?= $this->fetch('title') ? $this->fetch('title') . ' - ' : '' ?><?= $cakeDescription ?>
    </title>
    <meta name="description" content="">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= $this->Url->image('AranetaCityFavicon2024.png') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">

    <!-- Menu waves -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.min.css">

    <!-- Core CSS -->
    <?= $this->Html->css(['backend/core', 'backend/theme-default', 'backend/demo']) ?>

    <!-- Page CSS -->
    <?= $this->fetch('css') ?>

    <!-- Helpers -->
    <?= $this->Html->script('backend/helpers') ?>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->
    <?= $this->Html->script('backend/config') ?>

    <?= $this->fetch('meta') ?>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="<?= $this->Url->build('/admin') ?>" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="<?= $this->Url->image('AranetaCityFavicon2024.png') ?>" alt="" width="24"
                                height="24">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold">Araneta City</span>
                    </a>
                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-none d-xl-block">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.4854 4.88844C11.0081 4.41121 10.2344 4.41121 9.75715 4.88844L4.51028 10.1353C4.03297 10.6126 4.03297 11.3865 4.51028 11.8638L9.75715 17.1107C10.2344 17.5879 11.0081 17.5879 11.4854 17.1107C11.9626 16.6334 11.9626 15.8597 11.4854 15.3824L7.96672 11.8638C7.48942 11.3865 7.48942 10.6126 7.96672 10.1353L11.4854 6.61667C11.9626 6.13943 11.9626 5.36568 11.4854 4.88844Z"
                                fill="currentColor" fill-opacity="0.6" />
                            <path
                                d="M15.8683 4.88844L10.6214 10.1353C10.1441 10.6126 10.1441 11.3865 10.6214 11.8638L15.8683 17.1107C16.3455 17.5879 17.1192 17.5879 17.5965 17.1107C18.0737 16.6334 18.0737 15.8597 17.5965 15.3824L14.0778 11.8638C13.6005 11.3865 13.6005 10.6126 14.0778 10.1353L17.5965 6.61667C18.0737 6.13943 18.0737 5.36568 17.5965 4.88844C17.1192 4.41121 16.3455 4.41121 15.8683 4.88844Z"
                                fill="currentColor" fill-opacity="0.38" />
                        </svg>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <?php
                // Determine the current user's role slug for menu visibility.
                $identity = $this->getRequest()->getAttribute('identity');
                $roleSlug = null;
                if ($identity !== null) {
                    $u = $identity->getOriginalData();
                    $roleSlug = $u->role->slug ?? null;
                }
                $currentController = $this->request->getParam('controller');
                ?>
                <ul class="menu-inner py-1">
                    <!-- Mainpages -->
                    <li class="menu-item <?= $currentController === 'Mainpages' ? 'active' : '' ?>">
                        <a href="<?= $this->Url->build(['controller' => 'Mainpages', 'action' => 'index']) ?>"
                            class="menu-link">
                            <i class="menu-icon mdi mdi-home-outline"></i>
                            <div>Mainpages</div>
                        </a>
                    </li>

                    <!-- Contacts -->
                    <li class="menu-item <?= $currentController === 'Contacts' ? 'active' : '' ?>">
                        <a href="<?= $this->Url->build(['controller' => 'Contacts', 'action' => 'index']) ?>"
                            class="menu-link">
                            <i class="menu-icon mdi mdi-email-multiple-outline"></i>
                            <div>Contact Submissions</div>
                        </a>
                    </li>

                    <!-- Team Members -->
                    <li class="menu-item <?= $currentController === 'TeamMembers' ? 'active' : '' ?>">
                        <a href="<?= $this->Url->build(['controller' => 'TeamMembers', 'action' => 'index']) ?>"
                            class="menu-link">
                            <i class="menu-icon mdi mdi-account-tie-outline"></i>
                            <div>Team Members</div>
                        </a>
                    </li>


                    <?php if (in_array($roleSlug, ['superadmin', 'admin'], true)): ?>
                        <!-- Users -->
                        <li class="menu-item <?= $currentController === 'Users' ? 'active' : '' ?>">
                            <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'index']) ?>"
                                class="menu-link">
                                <i class="menu-icon mdi mdi-account-multiple-outline"></i>
                                <div>Users</div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($roleSlug === 'superadmin'): ?>
                        <!-- Roles -->
                        <li class="menu-item <?= $currentController === 'Roles' ? 'active' : '' ?>">
                            <a href="<?= $this->Url->build(['controller' => 'Roles', 'action' => 'index']) ?>"
                                class="menu-link">
                                <i class="menu-icon mdi mdi-shield-account-outline"></i>
                                <div>Roles</div>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0);">
                            <i class="mdi mdi-menu mdi-24px"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <?php
                            $navUser = $identity ? $identity->getOriginalData() : null;
                            $navName = $navUser ? trim(($navUser->first_name ?? '') . ' ' . ($navUser->last_name ?? '')) : '';
                            if ($navName === '' && $navUser) {
                                $navName = $navUser->email;
                            }
                            $navRoleName = $navUser->role->name ?? '';
                            ?>
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?= $this->Url->image('avatar/1.jpg') ?>" alt
                                            class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-item-text">
                                            <div class="fw-semibold"><?= h($navName) ?></div>
                                            <small class="text-muted"><?= h($navRoleName) ?></small>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>">
                                            <i class="mdi mdi-logout me-2"></i>
                                            <span>Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content') ?>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                                <div>
                                    &copy; <?= date('Y') ?>, made with <span class="text-danger">&#10084;</span> by
                                    Araneta City
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <?= $this->Html->script('jquery.min') ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <?= $this->Html->script('backend/bootstrap') ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1/dist/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2/hammer.min.js"></script>
    <?= $this->Html->script('backend/menu') ?>

    <!-- Main JS -->
    <?= $this->Html->script('backend/main') ?>

    <!-- Page JS -->
    <?= $this->fetch('script') ?>
</body>

</html>