<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'Araneta City - The City of Firsts';

// Buildings for the header search dropdown (set in AppController::beforeRender).
// Fallback to an empty array so error pages / other layouts don't break.
$headerBuildings = $headerBuildings ?? [];

// The homepage has a full-bleed dark hero, so the header can start transparent
// and fade into the Ayala navy on scroll. Other pages keep a solid header.
$request = $this->getRequest();
$isHomePage = $request->getParam('controller') === 'Mainpages'
    && $request->getParam('action') === 'home';
$bodyClass = $isHomePage ? 'home-page' : 'inner-page';

// The international phone input (intlTelInput) is only used by the contact
// form. Load its CSS/JS only there, so the custom init script doesn't run
// on pages without the #mobilePhone-1 element (which throws a JS error).
$isContactPage = $request->getParam('controller') === 'Mainpages'
    && $request->getParam('action') === 'contact';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->Html->charset() ?>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-49JT8WP41B"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-49JT8WP41B');
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $cakeDescription ?></title>
    <meta name="keywords"
        content="Araneta City, Gateway Mall, Ali Mall, Smart Araneta Coliseum, Cubao, Quezon City, Philippines">
    <meta name="description"
        content="Araneta City - The City of Firsts. Explore malls, dining, entertainment, events, hotels, and offices in the heart of Cubao, Quezon City.">
    <!-- Favicons (generated set in webroot/img/favicon/). Google uses the
         PNG/SVG icon declared here for the search-result icon; it must be a
         square icon and crawlable. Google caches it and updates on recrawl. -->
    <link rel="icon" type="image/png" sizes="96x96" href="<?= $this->Url->image('favicon/favicon-96x96.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= $this->Url->image('favicon/favicon.svg') ?>">
    <link rel="shortcut icon" href="<?= $this->Url->image('favicon/favicon.ico') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $this->Url->image('favicon/apple-touch-icon.png') ?>">
    <link rel="manifest" href="<?= $this->Url->image('favicon/site.webmanifest') ?>">


    <!-- Site font: Poppins (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
    <?php if ($isContactPage): ?>
        <link href="<?php echo $this->Url->build('intl/intlTelInput.css') ?>" rel="stylesheet" type="text/css" />
    <?php endif; ?>

    <?= $this->Html->css(['reset', 'plugins']) ?>
    <?php
    // Cache-bust the CSS files we actively edit so style updates always load
    // (no stale browser cache).
    $cssVer = function (string $file): int {
        $path = WWW_ROOT . 'css' . DS . $file . '.css';
        return file_exists($path) ? filemtime($path) : time();
    };
    ?>
    <?= $this->Html->css('style', ['?' => ['v' => $cssVer('style')]]) ?>
    <?= $this->Html->css('color', ['?' => ['v' => $cssVer('color')]]) ?>
    <?= $this->Html->css('custom', ['?' => ['v' => $cssVer('custom')]]) ?>
    <!-- Font Awesome Free (overrides missing local Pro font files from plugins.css) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- v4/v5 icon name compatibility shim (covers older names like fa-dot-circle-o) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/v4-shims.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css">

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>

<style>
    .aci-footer-seal-col {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .aci-footer-seal-img {
        width: 250px;
        height: auto;
        display: block;
    }
</style>

<body class="<?= $bodyClass ?>">
    <!--loader-->
    <!--loader removed-->
    <!-- header -->
    <header class="main-header">
        <!-- logo-->
        <a href="<?= $this->Url->build('/') ?>" class="logo-holder"><img src="<?= $this->Url->image('aci-white.png') ?>"
                alt="Araneta City"></a>
        <!-- logo end-->
        <!-- Right-side header items all float right, so DOM order runs RIGHT to LEFT:
                 hamburger (mobile only, outer right), then Search, then What's On. -->
        <!-- nav-button-wrap-->
        <div class="nav-button-wrap color-bg">
            <div class="nav-button">
                <span></span><span></span><span></span>
            </div>
        </div>
        <!-- nav-button-wrap end-->
        <!-- header-search_btn-->
        <div class="header-search_btn show-search-button"><i class="fas fa-search"></i></div>
        <!-- header-search_btn end-->
        <!-- header opt -->
        <a href="<?= $this->Url->build('/events') ?>" class="add-list color-bg">What's Happening <span><i
                    class="fas fa-calendar-days"></i></span></a>
        <!-- HIDDEN: Favorite and Sign In button -->
        <!-- <div class="cart-btn show-header-modal" data-microtip-position="bottom" role="tooltip" aria-label="Your Favorites"><i class="fas fa-heart"></i><span class="cart-counter green-bg"></span></div> -->
        <!-- <div class="show-reg-form modal-open avatar-img"><i class="fas fa-user"></i>Sign In</div> -->
        <!-- header opt end-->
        <!-- navigation -->
        <div class="nav-holder main-menu">
            <!-- Mobile drawer extras (hidden on desktop, shown inside the slide-in drawer) -->
            <div class="mob-drawer-top mob-drawer-only">
                <a href="<?= $this->Url->build('/') ?>" class="mob-drawer-logo">
                    <img src="<?= $this->Url->image('aci-white.png') ?>" alt="Araneta City">
                </a>
                <button type="button" class="mob-drawer-close" aria-label="Close menu"><i
                        class="fas fa-times"></i></button>
            </div>
            <p class="mob-drawer-intro mob-drawer-only">
                Discover what's new in shopping, dining, and entertainment across Araneta City.
            </p>
            <nav>
                <ul class="no-list-style">
                    <li>
                        <a href="<?= $this->Url->build('/') ?>">Home</a>
                    </li>
                    <li>
                        <a href="#">Explore <i class="fa fa-chevron-down"></i></a>
                        <ul>
                            <li><a href="<?= $this->Url->build('/cinema') ?>">Cinema</a></li>
                            <li><a href="<?= $this->Url->build('/shows') ?>">Shows</a></li>
                            <li><a href="<?= $this->Url->build('/shopping') ?>">Directory</a></li>
                            <li><a href="<?= $this->Url->build('/parking') ?>">Parking Guide</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Destinations<i class="fa fa-chevron-down"></i></a>
                        <ul>
                            <li><a href="<?= $this->Url->build('/malls') ?>">Malls</a></li>
                            <li><a href="<?= $this->Url->build('/hotels') ?>">Hotels</a></li>
                            <li><a href="<?= $this->Url->build('/offices') ?>">Offices</a></li>
                            <li><a href="<?= $this->Url->build('/residences') ?>">Residences</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Entertainment<i class="fa fa-chevron-down"></i></a>
                        <ul>
                            <li><a href="https://www.smartaranetacoliseum.com/" target="_blank">Smart Araneta
                                    Coliseum</a></li>
                            <li><a href="https://www.newfrontiertheater.com/" target="_blank">New Frontier Theater</a>
                            </li>
                            <li><a href="https://www.ticketnet.com.ph/gateway-cineplex-18-movies"
                                    target="_blank">Gateway Cineplex 18</a></li>
                            <li><a href="https://bbpilipinas.com/" target="_blank">Binibining Pilipinas</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= $this->Url->build('/news') ?>">News &amp; Blog</a>
                    </li>
                    <li>
                        <a href="https://careers.aranetacity.com/" target="_blank">Careers</a>
                    </li>
                    
                    <li class="mob-nav-only">
                        <a href="<?= $this->Url->build('/events') ?>">What's Happening</a>
                    </li>
                </ul>
            </nav>
            <!-- Mobile drawer call-to-action buttons (hidden since moved to nav) -->
            <div class="mob-drawer-cta mob-drawer-only" style="display: none;">
                <!-- <a href="<?= $this->Url->build('/events') ?>" class="mob-cta-primary">What's On</a> -->
                <a href="<?= $this->Url->build('/events') ?>" class="mob-cta-secondary">What's Happening</a>
            </div>
        </div>
        <!-- navigation end -->
        <!-- Mobile drawer backdrop overlay -->
        <div class="mob-drawer-overlay"></div>
        <!-- header-search_container -->
        <div class="header-search_container header-search vis-search">
            <div class="container small-container">
                <form class="header-search-input-wrap fl-wrap aci-search-form" method="GET"
                    action="<?= $this->Url->build(['controller' => 'Search', 'action' => 'search']) ?>"
                    onsubmit="return validateSearch(this);">
                    <div class="aci-search-field aci-search-field--text">
                        <label><i class="fas fa-keyboard"></i></label>
                        <input type="text" name="q" placeholder="Search stores, restaurants, events..." value="" />
                    </div>

                    <div class="aci-search-field aci-search-field--select">
                        <select name="category">
                            <option value="">All Categories</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Dining">Dining</option>
                            <option value="Services">Services</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Events">Events</option>
                            <option value="Shows">Shows</option>
                            <option value="Movies">Movies</option>
                            <option value="News">News</option>
                            <option value="Happenings">Happenings</option>
                        </select>
                    </div>

                    <div class="aci-search-field aci-search-field--select">
                        <select name="building">
                            <option value="">All Buildings</option>
                            <?php if (!empty($headerBuildings)): ?>
                                <?php foreach ($headerBuildings as $bId => $bName): ?>
                                    <option value="<?= h($bId) ?>"><?= h($bName) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="aci-search-submit"><i class="fas fa-search"></i> Search</button>
                </form>
                <div class="header-search_close color-bg"><i class="fas fa-arrow-up"></i></div>
            </div>

            <style>
                .aci-search-form {
                    display: flex !important;
                    flex-wrap: nowrap;
                    align-items: stretch;
                    gap: 12px;
                    width: 100%;
                }

                .aci-search-field {
                    display: flex;
                    align-items: center;
                    background: #fff;
                    border-radius: 8px;
                    height: 56px;
                    padding: 0 16px;
                }

                .aci-search-field--text {
                    flex: 1 1 auto;
                    min-width: 0;
                }

                .aci-search-field--text label {
                    margin: 0 10px 0 0;
                    color: #00afab;
                    font-size: 16px;
                    display: flex;
                    align-items: center;
                }

                .aci-search-field--text input {
                    border: 0;
                    outline: none;
                    background: transparent;
                    width: 100%;
                    height: 100%;
                    font-size: 15px;
                    color: #333;
                }

                .aci-search-field--select {
                    flex: 0 0 200px;
                    padding: 0;
                }

                .aci-search-field--select select {
                    border: 0;
                    outline: none;
                    background: transparent;
                    width: 100%;
                    height: 100%;
                    padding: 0 16px;
                    font-size: 15px;
                    color: #333;
                    cursor: pointer;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23999' d='M6 8L0 2l1.5-1.5L6 5l4.5-4.5L12 2z'/%3E%3C/svg%3E");
                    background-repeat: no-repeat;
                    background-position: right 16px center;
                    padding-right: 40px;
                }

                .aci-search-submit {
                    flex: 0 0 auto;
                    height: 56px;
                    padding: 0 32px;
                    border: 0;
                    border-radius: 8px;
                    background: #00afab;
                    color: #fff;
                    font-size: 15px;
                    font-weight: 700;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    white-space: nowrap;
                    transition: background 0.2s ease;
                }

                .aci-search-submit:hover {
                    background: #009591;
                }

                /* Tablet */
                @media (max-width: 991px) {
                    .aci-search-form {
                        flex-wrap: wrap;
                    }

                    .aci-search-field--text {
                        flex: 1 1 100%;
                    }

                    .aci-search-field--select {
                        flex: 1 1 calc(50% - 6px);
                    }

                    .aci-search-submit {
                        flex: 1 1 100%;
                        justify-content: center;
                    }
                }

                /* Mobile */
                @media (max-width: 600px) {
                    .aci-search-field--select {
                        flex: 1 1 100%;
                    }
                }
            </style>
        </div>
        <!-- header-search_container end -->
        <!-- wishlist-wrap-->
        <!-- <div class="header-modal novis_wishlist">
                <div class="header-modal-container scrollbar-inner fl-wrap" data-simplebar>
                    <div class="widget-posts fl-wrap">
                        <ul class="no-list-style">
                            <li>
                                <div class="widget-posts-img"><a href="place-detail.html"><img src="<?= $this->Url->image('gallery/mall/smart-araneta.jpg') ?>" alt=""></a></div>
                                <div class="widget-posts-descr">
                                    <h4><a href="place-detail.html">Smart Araneta Coliseum</a></h4>
                                    <div class="geodir-category-location fl-wrap"><a href="#"><i class="fas fa-map-marker-alt"></i> Gen. Roxas Ave, Cubao, QC</a></div>
                                    <div class="widget-posts-descr-link"><a href="places.html">Entertainment</a></div>
                                    <div class="widget-posts-descr-score">4.8</div>
                                    <div class="clear-wishlist"><i class="fas fa-times-circle"></i></div>
                                </div>
                            </li>
                            <li>
                                <div class="widget-posts-img"><a href="place-detail.html"><img src="<?= $this->Url->image('gallery/hotels/Novotel.jpg') ?>" alt=""></a></div>
                                <div class="widget-posts-descr">
                                    <h4><a href="place-detail.html">Novotel Manila Araneta City</a></h4>
                                    <div class="geodir-category-location fl-wrap"><a href="#"><i class="fas fa-map-marker-alt"></i> Gen. Aguinaldo Ave, Cubao, QC</a></div>
                                    <div class="widget-posts-descr-link"><a href="places.html">Hotels</a></div>
                                    <div class="widget-posts-descr-score">4.5</div>
                                    <div class="clear-wishlist"><i class="fas fa-times-circle"></i></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="header-modal-top fl-wrap">
                    <h4>Your Favorites : <span><strong></strong> Places</span></h4>
                    <div class="close-header-modal"><i class="fas fa-times"></i></div>
                </div>
            </div> -->
        <!--wishlist-wrap end -->
    </header>
    <!-- header end-->

    <?= $this->fetch('content') ?>

    <!--footer (SM-style: colored footer with big monogram watermark on the right) -->
    <footer class="aci-footer">
        <div class="container">
            <div class="aci-footer-grid">

                <!-- Contact / address + socials -->
                <div class="aci-footer-contact">
                    <h4>CONTACT</h4>
                    <ul class="aci-contact-list">
                        
                        <!--<li><i class="fas fa-map-marker-alt"></i> Gen. Roxas Ave, Cubao, Quezon City 1109, Philippines
                        </li>-->
                        <li><i class="fas fa-envelope"></i> <a
                                href="mailto:info@aranetagroup.com">info@aranetagroup.com</a></li>
                        <!-- <li><i class="fas fa-phone"></i> <a href="tel:+6329111555">(02) 911-1555</a></li> -->
                    </ul>
                    <!-- Mall Hours -->
                    <div class="aci-footer-hours">
                        <h4><i class="far fa-clock"></i> Mall Hours</h4>
                        <p>Sunday&ndash;Thursday: 10AM&ndash;9PM</p>
                        <p>Friday&ndash;Saturday: 10AM&ndash;10PM</p>
                    </div>
                    <div class="aci-footer-social">
                        <h5>Follow us on:</h5>
                        <a href="https://www.facebook.com/AranetaCity/" target="_blank" rel="noopener"
                            aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/aranetacity/" target="_blank" rel="noopener"
                            aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://twitter.com/AranetaCity" target="_blank" rel="noopener" aria-label="X"><i
                                class="fab fa-x-twitter"></i></a>
                        <a href="https://www.youtube.com/@AranetaCity" target="_blank" rel="noopener"
                            aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="https://www.linkedin.com/company/aranetacity" target="_blank" rel="noopener"
                            aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://www.tiktok.com/@aranetacity" target="_blank" rel="noopener"
                            aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="https://www.threads.net/@aranetacity" target="_blank" rel="noopener"
                            aria-label="Threads"><i class="fa-brands fa-threads"></i></a>
                    </div>

                </div>

    

                <!-- Explore -->
                <div class="aci-footer-col">
                    <h4>Explore</h4>
                    <ul>
                        <li><a href="/about">About Araneta City</a></li>
                        <li><a href="https://careers.aranetacity.com/" target="_blank">Careers</a></li>
                        <li><a href="/contact">Contact Us</a></li>
                        <li><a href="/faqs">FAQs</a></li>
                        <li><a href="<?= $this->Url->build('/shopping') ?>">Directory</a></li>
                        <li><a href="<?= $this->Url->build('/events') ?>">Events</a></li>
                        <li><a href="<?= $this->Url->build('/transport') ?>">Getting Here</a></li>
                        <li><a href="<?= $this->Url->build('/parking') ?>">Parking Guide</a></li>
                    </ul>
                </div>

                <div class="aci-footer-seal-col">
                    <img src="<?= $this->Url->image('privacy/seal.png') ?>" alt="NPC DPO/DPS Registered Seal"
                        class="aci-footer-seal-img">
                </div>

            </div>

            <!-- Big logo trademark watermark (right side) -->
            <div class="aci-footer-monogram" aria-hidden="true">
                <img src="<?= $this->Url->image('aci-logo-white-a.png') ?>" alt="Araneta City">
            </div>
        </div>

        <!-- Sub-footer bar -->
        <div class="aci-subfooter">
            <div class="container">
                <div class="aci-subfooter-inner">
                    <span class="aci-copyright">&copy; 2026 Araneta City. All rights reserved.</span>
                    <ul class="aci-subfooter-nav">
                        <li><a href="/contact">Contact Us</a></li>
                        <li><a href="/faqs">FAQs</a></li>
                        <li><a href="/privacy-policy">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <!--footer end -->
    <a class="to-top"><i class="fas fa-chevron-up"></i></a>

    <?= $this->Html->script(['jquery.min', 'plugins', 'scripts']) ?>

    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->

    <?php if ($isContactPage): ?>
        <script src="<?php echo $this->Url->build('intl/intlTelInput.js') ?>"></script>
        <script src="<?php echo $this->Url->build('intl/intl-custom-script.js') ?>"></script>
    <?php endif; ?>

    <!-- Swiper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>
    <!-- Navbar scroll effect - change to white background when scrolling -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const header = document.querySelector('.main-header');
            if (!header) return;

            const onScroll = function () {
                if (window.scrollY > 100) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            };

            window.addEventListener('scroll', onScroll, { passive: true });
            // Apply correct state on load (e.g. when page is reloaded mid-scroll)
            onScroll();
        });
    </script>

    <!-- Mobile drawer: overlay + close handling (works with the existing
             .nav-button-wrap toggle in scripts.js which adds .vismobmenu). -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.querySelector('.main-menu');
            const overlay = document.querySelector('.mob-drawer-overlay');
            const navBtn = document.querySelector('.nav-button-wrap');
            const closeBtn = document.querySelector('.mob-drawer-close');
            if (!menu || !overlay) return;

            function closeDrawer() {
                menu.classList.remove('vismobmenu');
                overlay.classList.remove('is-open');
                if (navBtn) navBtn.classList.remove('vismobmenu_btn');
                document.body.style.overflow = '';
            }

            // Remove any stray cloned menu (.menusb) that the theme's scripts.js
            // may build. We use the raw <nav> directly as the drawer menu, so a
            // clone would only overlap it.
            function removeMenuClone() {
                menu.querySelectorAll('.menusb').forEach(function (el) {
                    el.remove();
                });
            }

            // Sync the overlay + body scroll-lock whenever the hamburger toggles the menu.
            // Read the final .vismobmenu state on the next tick so we don't depend on
            // whether this handler runs before or after the toggle in scripts.js.
            if (navBtn) {
                navBtn.addEventListener('click', function () {
                    removeMenuClone();
                    setTimeout(function () {
                        removeMenuClone();
                        const isOpen = menu.classList.contains('vismobmenu');
                        overlay.classList.toggle('is-open', isOpen);
                        document.body.style.overflow = isOpen ? 'hidden' : '';
                    }, 0);
                });
            }

            overlay.addEventListener('click', closeDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

            // Close when tapping an actual navigation link (ignore dropdown parents with '#').
            menu.querySelectorAll('nav a, .mob-drawer-cta a').forEach(function (a) {
                a.addEventListener('click', function () {
                    const href = a.getAttribute('href') || '';
                    if (href && href !== '#') closeDrawer();
                });
            });

            // Close on Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && menu.classList.contains('vismobmenu')) closeDrawer();
            });
        });
    </script>

    <?= $this->fetch('script') ?>

    <!-- Search form validation -->
    <script>
        function validateSearch(form) {
            const query = form.querySelector('input[name="q"]').value.trim();
            const category = form.querySelector('select[name="category"]').value;
            const building = form.querySelector('select[name="building"]').value;
            if (!query && !category && !building) {
                alert('Please enter a search term or select a category or building');
                return false;
            }
            return true;
        }
    </script>

</body>

</html>