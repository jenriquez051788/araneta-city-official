<?php
/**
 * Araneta City public homepage.
 *
 * Rendered by MainpagesController::home() at the site root ('/').
 * Renders within templates/layout/default.php, which already provides
 * the <head>, header/nav, footer, and script tags - this view only
 * contributes the page content.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Mainpage|null $mainpage
 * @var array $happenings Active happenings from the API
 * @var array $news News stories from the API
 * @var array $movies Latest movies from the TicketNet API
 */

// Homepage popup modal configuration (single place to edit content / toggle).
$popupModal = [
    'enabled' => true,
    'title' => 'Move Your Christmas World',
    'message' => 'Nov. 25, 2026 to Jan. 31, 2027',
    'image' => 'popup/FA-POSTER-1.jpg', // relative path under webroot/img, or null
    // Show only the image: the title stays for screen readers, the message
    // and CTA are not rendered. Ignored when 'image' is empty.
    'imageOnly' => true,
    'ctaLabel' => 'See Happenings',
    'ctaUrl' => '/happenings',
];

if (!function_exists('popupModalIsSafeUrl')) {
    /**
     * Accept only non-blank URLs starting with http://, https:// or /.
     *
     * @param string|null $url URL to check.
     * @return bool
     */
    function popupModalIsSafeUrl(?string $url): bool
    {
        if ($url === null || trim($url) === '') {
            return false;
        }

        return (bool)preg_match('#^(https?://|/)#i', $url);
    }
}
$heroHeading = $mainpage->hero_heading ?? 'The City of Firsts. Discover shopping, dining, entertainment, and more in the heart of Cubao.';
$heroImage = !empty($mainpage->hero_image) ? 'banner/' . $mainpage->hero_image : 'bg/hero/1.jpg';


if (!empty($mainpage->meta_title)) {
    $this->assign('title', $mainpage->meta_title);
}
if (!empty($mainpage->meta_description)) {
    $this->Html->meta('description', $mainpage->meta_description, ['block' => 'meta']);
}
if (!empty($mainpage->meta_keywords)) {
    $this->Html->meta('keywords', $mainpage->meta_keywords, ['block' => 'meta']);
}
?>
<?php if (!empty($popupModal['enabled'])) : ?>
<?php $popupImageOnly = !empty($popupModal['imageOnly']) && !empty($popupModal['image']); ?>
<!-- Homepage popup modal -->
<div id="popup-modal-overlay" class="popup-modal-overlay" hidden>
    <div id="popup-modal" class="popup-modal<?= $popupImageOnly ? ' popup-modal--image-only' : '' ?>" role="dialog" aria-modal="true" aria-labelledby="popup-modal-title">
        <button id="popup-modal-close" class="popup-modal-close" type="button" aria-label="Close">&times;</button>
        <?php if (!empty($popupModal['image'])) : ?>
            <img id="popup-modal-image" class="popup-modal-image"
                src="<?= h($this->Url->image($popupModal['image'])) ?>"
                alt="<?= $popupImageOnly ? h(trim($popupModal['title'] . ' ' . $popupModal['message'])) : '' ?>">
        <?php endif; ?>
        <h2 id="popup-modal-title" class="popup-modal-title<?= $popupImageOnly ? ' popup-modal-sr-only' : '' ?>"><?= h($popupModal['title']) ?></h2>
        <?php if (!$popupImageOnly) : ?>
        <p class="popup-modal-message"><?= h($popupModal['message']) ?></p>
        <?php endif; ?>
        <?php if (!$popupImageOnly && popupModalIsSafeUrl($popupModal['ctaUrl'] ?? null) && trim((string)($popupModal['ctaLabel'] ?? '')) !== '') : ?>
            <a id="popup-modal-cta" class="popup-modal-cta" href="<?= h($popupModal['ctaUrl']) ?>"><?= h($popupModal['ctaLabel']) ?></a>
        <?php endif; ?>
    </div>
</div>
    <style>
        .popup-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            padding: 16px;
            overflow-y: auto;
            background: rgba(0, 0, 0, 0.6);
        }

        .popup-modal-overlay[hidden] {
            display: none;
        }

        .popup-modal {
            position: relative;
            box-sizing: border-box;
            width: 100%;
            max-width: 480px;
            max-height: calc(100vh - 32px);
            overflow-x: hidden;
            overflow-y: auto;
            margin: auto;
            padding: 0 0 28px;
            background: #fff;
            color: #000;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.1);
            font-family: "Lato", sans-serif;
            text-align: center;
        }

        .popup-modal-close {
            position: sticky;
            top: 0;
            z-index: 2;
            display: block;
            width: 44px;
            height: 44px;
            min-width: 44px;
            margin: 0 0 -44px auto;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: #fff;
            color: #666;
            font-size: 28px;
            line-height: 44px;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .popup-modal-close:hover {
            background: #f0f0f0;
        }

        .popup-modal-image {
            display: block;
            width: 100%;
            max-width: 100%;
            height: auto;
            border-radius: 12px 12px 0 0;
        }

        .popup-modal-title {
            margin: 0;
            padding: 28px 24px 0 24px;
            font-family: "Raleway", sans-serif;
            font-weight: 700;
            font-size: 24px;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .popup-modal-message {
            margin: 12px 0 0;
            padding: 0 24px;
            font-size: 16px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .popup-modal-cta {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 24px;
            border-radius: 6px;
            background: #00afab;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-decoration: none;
        }

        .popup-modal--image-only .popup-modal-image {
            border-radius: 12px;
        }

        .popup-modal-sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        body.popup-modal-open {
            overflow: hidden;
        }
    </style>
    <script>
        (function () {
            'use strict';

            var STORAGE_KEY = 'popupModalDismissed';
            var DISMISSED_VALUE = 'dismissed';
            var isOpen = false;
            var previouslyFocused = null;
            var savedScrollY = 0;
            var overlayEl = null;
            var modalEl = null;
            var closeBtn = null;

            function safeGet(key) {
                try {
                    var value = window.sessionStorage.getItem(key);
                    return value === undefined ? null : value;
                } catch (e) {
                    return null;
                }
            }

            function safeSet(key, value) {
                try {
                    window.sessionStorage.setItem(key, value);
                    return true;
                } catch (e) {
                    return false;
                }
            }

            function shouldShowModal(flagValue) {
                return flagValue !== DISMISSED_VALUE;
            }

            function getFocusableElements(root) {
                if (!root) {
                    return [];
                }
                var nodes = root.querySelectorAll(
                    'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                );
                return Array.prototype.slice.call(nodes);
            }

            function trapTabKey(event, root) {
                if (!event || event.key !== 'Tab') {
                    return;
                }
                var focusable = getFocusableElements(root);
                if (focusable.length === 0) {
                    event.preventDefault();
                    return;
                }
                var first = focusable[0];
                var last = focusable[focusable.length - 1];
                var active = document.activeElement;
                var index = focusable.indexOf(active);

                if (event.shiftKey) {
                    if (active === first || index === -1) {
                        event.preventDefault();
                        last.focus();
                    }
                } else if (active === last || index === -1) {
                    event.preventDefault();
                    first.focus();
                }
            }

            function isOverlayClick(target, overlay, modal) {
                if (target === overlay) {
                    return true;
                }
                if (target === modal) {
                    return false;
                }
                return !(modal && typeof modal.contains === 'function' && modal.contains(target));
            }

            function openModal() {
                if (isOpen) {
                    return;
                }
                isOpen = true;
                previouslyFocused = document.activeElement;
                savedScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
                overlayEl.removeAttribute('hidden');
                document.body.classList.add('popup-modal-open');
                closeBtn.focus();
            }

            function closeModal() {
                if (!isOpen) {
                    return;
                }
                isOpen = false;
                overlayEl.setAttribute('hidden', '');
                document.body.classList.remove('popup-modal-open');
                safeSet(STORAGE_KEY, DISMISSED_VALUE);
                window.scrollTo(0, savedScrollY);

                var target = previouslyFocused;
                if (!target || typeof target.focus !== 'function' || !document.body.contains(target)) {
                    target = document.body;
                }
                try {
                    target.focus();
                } catch (e) {
                    document.body.focus();
                }
                previouslyFocused = null;
            }

            function init() {
                try {
                    overlayEl = document.getElementById('popup-modal-overlay');
                    modalEl = document.getElementById('popup-modal');
                    closeBtn = document.getElementById('popup-modal-close');
                    var titleEl = document.getElementById('popup-modal-title');
                    if (!overlayEl || !modalEl || !closeBtn || !titleEl) {
                        return;
                    }

                    closeBtn.addEventListener('click', closeModal);
                    closeBtn.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar') {
                            event.preventDefault();
                            closeModal();
                        }
                    });

                    overlayEl.addEventListener('click', function (event) {
                        if (isOverlayClick(event.target, overlayEl, modalEl)) {
                            closeModal();
                        }
                    });

                    document.addEventListener('keydown', function (event) {
                        if (!isOpen) {
                            return;
                        }
                        if (event.key === 'Escape' || event.key === 'Esc') {
                            closeModal();
                        } else if (event.key === 'Tab') {
                            trapTabKey(event, modalEl);
                        }
                    });

                    var imageEl = document.getElementById('popup-modal-image');
                    if (imageEl) {
                        imageEl.addEventListener('error', function () {
                            imageEl.style.display = 'none';
                        });
                    }

                    if (shouldShowModal(safeGet(STORAGE_KEY))) {
                        openModal();
                    }
                } catch (e) {
                    if (window.console && typeof console.warn === 'function') {
                        console.warn('Popup modal init failed', e);
                    }
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
<?php endif; ?>
<!-- wrapper-->
<div id="wrapper">
    <!-- content-->
    <div class="content">
        <!--  HERO SECTION  -->
        <section class="aci-hero">
            <div class="bg-tabs-wrap">
                <div class="bg bg_tabs" data-bg="<?= $this->Url->image($heroImage) ?>"></div>
                <video class="aci-hero-video" autoplay muted loop playsinline preload="metadata"
                    poster="<?= $this->Url->image($heroImage) ?>">
                    <source src="<?= $this->Url->build('/banner-vid/HeroBanner_Video_crf28_720p.mp4') ?>"
                        type="video/mp4">
                </video>
                <div class="aci-hero-overlay"></div>
            </div>
            <div class="aci-hero-container">
                <div class="aci-hero-content">

                    <h1 class="aci-hero-title">Where everything connects.</h1>
                </div>
            </div>
        </section>
        <!--  HERO SECTION END  -->
        <!--  QUICK NAV STRIP  -->
        <div class="quick-nav">
            <a href="<?= $this->Url->build('/shopping') ?>" class="quick-nav-item">
                <i class="fas fa-shopping-bag"></i>
                <span>Shop</span>
            </a>
            <a href="<?= $this->Url->build('/dining') ?>" class="quick-nav-item">
                <i class="fas fa-utensils"></i>
                <span>Dine</span>
            </a>
            <a href="<?= $this->Url->build('/services') ?>" class="quick-nav-item">
                <i class="fas fa-store-alt"></i>
                <span>Services</span>
            </a>
            <a href="<?= $this->Url->build('/events') ?>" class="quick-nav-item">
                <i class="fas fa-masks-theater"></i>
                <span>Events</span>
            </a>
            <a href="https://www.ticketnet.com.ph/gateway-cineplex-18-movies" target="_blank" rel="noopener"
                class="quick-nav-item">
                <i class="fas fa-film"></i>
                <span>Cinema</span>
            </a>

        </div>
        <!--  QUICK NAV STRIP END  -->
        <!--  MALL HOURS BAR  -->
        <!-- <div class="mall-hours-bar">
            <div class="container">
                <div class="mall-hours-inner">
                    <div class="mall-hours-head">
                        <i class="far fa-clock"></i>
                        <span>Mall Hours</span>
                    </div>
                    <div class="mall-hours-list">
                        <div class="mall-hours-item">
                            <span class="mall-hours-day">Sunday &ndash; Thursday</span>
                            <span class="mall-hours-time">10:00 AM &ndash; 9:00 PM</span>
                        </div>
                        <div class="mall-hours-item">
                            <span class="mall-hours-day">Friday &ndash; Saturday</span>
                            <span class="mall-hours-time">10:00 AM &ndash; 10:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!--  MALL HOURS BAR END  -->
        <!--  WHAT'S HAPPENING  -->
        <section class="slw-sec" id="sec1" style="background: #fff; padding: 60px 0;">
            <div class="container">
                <!-- Carousel with Card Info + Navigation -->
                <div class="listing-slider-wrap fl-wrap"
                    style="display: flex; gap: 40px; align-items: flex-start; margin: 0; width: 100%; flex-direction: column;">

                    <!-- Info Card (Left Side) -->
                    <div class="whats-on-card"
                        style="flex: 0 0 auto; background: #fff; padding: 45px 35px; border-radius: 12px; display: flex; flex-direction: column; gap: 28px; box-shadow: 0 2px 16px rgba(0,0,0,0.1); width: 100%; max-width: 340px;">
                        <div>
                            <h3
                                style="color: #00afab; margin: 0 0 16px 0; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                What's Happening?</h3>
                            <p style="color: #666; margin: 0; font-size: 16px; line-height: 1.6; font-weight: 400;">
                                Check out events and happenings at Araneta City.</p>
                        </div>
                        <a href="<?= $this->Url->build('/happenings') ?>" class="btn dec_btn"
                            style="display: inline-block; width: fit-content; padding: 11px 24px; border-radius: 6px; font-weight: 600; letter-spacing: 0.5px; font-size: 14px; background: #00afab; color: #fff;">View
                            all</a>
                    </div>

                    <!-- Carousel (Right Side) - 5 Items Grid -->
                    <div style="flex: 1; min-width: 0; position: relative; overflow: hidden; width: 100%;">
                        <div class="listing-slider fl-wrap" style="overflow: hidden;">
                            <div class="swiper-container happenings-swiper"
                                style="border-radius: 8px; overflow: hidden;">
                                <div class="swiper-wrapper" style="display: flex; gap: 2px;">
                                    <?php
                                    $happeningsArray = !empty($happenings) ? $happenings : [];
                                    // Debug output (remove in production)
                                    echo "<!-- DEBUG: Total happenings: " . count($happeningsArray) . " -->";
                                    if (empty($happeningsArray)):
                                        ?>
                                        <!-- Default/Empty State -->
                                        <div class="swiper-slide happening-slide">
                                            <div class="happening-card-grid"
                                                style="position: relative; overflow: hidden; border-radius: 8px; aspect-ratio: 575 / 324; width: 100%; background: linear-gradient(135deg, #f0f0f0 0%, #e8e8e8 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                                <div style="text-align: center; color: #bbb;">
                                                    <i class="fas fa-calendar-alt"
                                                        style="font-size: 36px; margin-bottom: 10px; display: block;"></i>
                                                    <p style="font-size: 14px; margin: 0;">No events</p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?php
                                        foreach ($happeningsArray as $happening):
                                            $imageUrl = !empty($happening['img_url'])
                                                ? env('API_BASE_URL') . ltrim(h($happening['img_url']), '/')
                                                : $this->Url->image('gallery/whats-happening/UPCOMING STORES in Araneta City.png');
                                            // Use event_title from API
                                            $title = h($happening['event_title'] ?? $happening['title'] ?? $happening['name'] ?? 'Event');
                                            $date = h($happening['display_date'] ?? $happening['date'] ?? '');
                                            // Link to the internal happening detail page when a slug
                                            // exists, otherwise fall back to the external link.
                                            $happeningSlug = $happening['slug'] ?? '';
                                            $link = !empty($happeningSlug)
                                                ? $this->Url->build('/happening/' . $happeningSlug)
                                                : h($happening['link'] ?? $happening['url'] ?? '#');
                                            ?>
                                            <div class="swiper-slide happening-slide">
                                                <a href="<?= $link ?>" class="happening-card-grid"
                                                    style="display: block; position: relative; overflow: hidden; border-radius: 8px; aspect-ratio: 575 / 324; width: 100%; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; text-decoration: none;">
                                                    <img src="<?= $imageUrl ?>" alt="<?= $title ?>"
                                                        style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;">
                                                    <div
                                                        style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.95) 100%); display: flex; flex-direction: column; justify-content: space-between; padding: 12px; opacity: 1;">
                                                        <div></div>
                                                        <div style="color: #fff; text-decoration: none;">
                                                            <h4
                                                                style="color: #fff; margin: 0 0 4px 0; font-size: 20px; font-weight: 600; line-height: 1.2; letter-spacing: -0.2px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-shadow: 0 1px 3px rgba(0,0,0,0.3);text-align: left;">
                                                                <?= $title ?>
                                                            </h4>
                                                            <p
                                                                style="color: rgba(255,255,255,0.95); margin: 0; font-size: 18px; display: flex; align-items: center; justify-content: flex-start; font-weight: 500; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                                <?php if (!empty($date)): ?>
                                                                    <i class="fas fa-calendar-days"
                                                                        style="margin-right: 4px; font-size: 9px;"></i><span
                                                                        style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;"><?= $date ?></span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="happenings-carousel-prev2"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; opacity: 1; box-shadow: 0 2px 8px rgba(0,0,0,0.1); z-index: 10;"
                                title="Previous">
                                <i class="fas fa-chevron-left" style="color: #666; font-size: 16px;"></i>
                            </div>
                            <div class="happenings-carousel-next2"
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; opacity: 1; box-shadow: 0 2px 8px rgba(0,0,0,0.1); z-index: 10;"
                                title="Next">
                                <i class="fas fa-chevron-right" style="color: #666; font-size: 16px;"></i>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </section>

        <!--  WHAT'S HAPPENING END  -->

        <!-- Initialize Happenings Carousel -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const happeningsSwiper = new Swiper('.happenings-swiper', {
                    slidesPerView: 'auto',
                    spaceBetween: 10,
                    loop: true,
                    initialSlide: 0,
                    observer: true,
                    observeParents: true,
                    navigation: {
                        nextEl: '.happenings-carousel-next2',
                        prevEl: '.happenings-carousel-prev2',
                    },
                    on: {
                        init: function () {
                            updateNavButtons(this);
                        },
                        afterInit: function () {
                            const swiper = this;
                            // Force to first real slide (most recent) after loop clones are set up
                            swiper.slideToLoop(0, 0);
                            // Fallback in case layout shifts after images load
                            setTimeout(function () {
                                swiper.update();
                                swiper.slideToLoop(0, 0);
                            }, 100);
                        },
                        slideChange: function () {
                            updateNavButtons(this);
                        },
                    },
                    breakpoints: {
                        320: {
                            slidesPerView: 'auto',
                            spaceBetween: 10,
                            centeredSlides: true,
                            slidesPerGroup: 1,
                        },
                        640: {
                            slidesPerView: 'auto',
                            spaceBetween: 10,
                            centeredSlides: false,
                            slidesPerGroup: 1,
                        },
                        1024: {
                            slidesPerView: 'auto',
                            spaceBetween: 10,
                            centeredSlides: false,
                            slidesPerGroup: 1,
                        },
                        1280: {
                            slidesPerView: 'auto',
                            spaceBetween: 10,
                            centeredSlides: false,
                            slidesPerGroup: 1,
                        },
                    },
                });

                function updateNavButtons(swiper) {
                    const prevBtn = document.querySelector('.happenings-carousel-prev2');
                    const nextBtn = document.querySelector('.happenings-carousel-next2');

                    // With loop enabled, buttons are always visible
                    prevBtn.style.opacity = '1';
                    prevBtn.style.pointerEvents = 'auto';
                    nextBtn.style.opacity = '1';
                    nextBtn.style.pointerEvents = 'auto';
                }
            });
        </script>

        <!-- <div class="sec-circle fl-wrap"></div> -->

        <!--  STATS SECTION  -->
        <!-- <section class="parallax-section small-par" data-scrollax-parent="true">
            <div class="bg par-elem" data-bg="<?= $this->Url->image('bg/1.jpg') ?>"
                data-scrollax="properties: { translateY: '30%' }"></div>
            <div class="overlay op7"></div>
            <div class="container">
                <div class="single-facts single-facts_2 fl-wrap">
                    <div class="inline-facts-wrap">
                        <div class="inline-facts">
                            <div class="milestone-counter">
                                <div class="stats animaper">
                                    <div class="num" data-content="0" data-num="500">500</div>
                                </div>
                            </div>
                            <h6>Shops & Restaurants</h6>
                        </div>
                    </div>
                    <div class="inline-facts-wrap">
                        <div class="inline-facts">
                            <div class="milestone-counter">
                                <div class="stats animaper">
                                    <div class="num" data-content="0" data-num="35">35</div>
                                </div>
                            </div>
                            <h6>Million Visitors Yearly</h6>
                        </div>
                    </div>
                    <div class="inline-facts-wrap">
                        <div class="inline-facts">
                            <div class="milestone-counter">
                                <div class="stats animaper">
                                    <div class="num" data-content="0" data-num="60">60</div>
                                </div>
                            </div>
                            <h6>Years of Legacy</h6>
                        </div>
                    </div>
                    <div class="inline-facts-wrap">
                        <div class="inline-facts">
                            <div class="milestone-counter">
                                <div class="stats animaper">
                                    <div class="num" data-content="0" data-num="50">50</div>
                                </div>
                            </div>
                            <h6>Events Monthly</h6>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!--  STATS SECTION END  -->
        <!--  POPULAR PLACES SECTION  -->
        <!-- CATEGORIES SECTION -->
        <section class="categories-section">
            <div class="container">
                <div class="discover-head">
                    <!-- <span class="discover-eyebrow">Discover</span> -->
                    <h2 class="discover-title">Discover Araneta City</h2>
                </div>
                <div class="discover-grid">
                    <a href="<?= $this->Url->build('/dining') ?>" class="discover-cell">
                        <div class="discover-cell-icon"><img
                                src="<?= $this->Url->image('categories/ACI_Website_Dine.png') ?>" alt="Dining"></div>
                        <h3 class="discover-cell-title">Dining</h3>
                        <p class="discover-cell-desc">Explore 350+ food brands and find your next favorite bite.</p>
                        <span class="discover-cell-btn">Explore</span>
                    </a>
                    <a href="<?= $this->Url->build('/shopping') ?>" class="discover-cell">
                        <div class="discover-cell-icon"><img
                                src="<?= $this->Url->image('categories/ACI_Website_Shop.png') ?>" alt="Shopping"></div>
                        <h3 class="discover-cell-title">Shopping</h3>
                        <p class="discover-cell-desc">Discover stores, brands, trends, and everything you need.</p>
                        <span class="discover-cell-btn">Explore</span>
                    </a>
                    <a href="<?= $this->Url->build('/services') ?>" class="discover-cell">
                        <div class="discover-cell-icon"><img src="<?= $this->Url->image('categories/Services.png') ?>"
                                alt="Services"></div>
                        <h3 class="discover-cell-title">Services</h3>
                        <p class="discover-cell-desc">Find essential services, from government offices to salons and
                            more.</p>
                        <span class="discover-cell-btn">Explore</span>
                    </a>
                    <a href="<?= $this->Url->build('/shows') ?>" class="discover-cell">
                        <div class="discover-cell-icon"><img
                                src="<?= $this->Url->image('categories/ACI_Website_Entertainment.png') ?>"
                                alt="Entertainment"></div>
                        <h3 class="discover-cell-title">Experience</h3>
                        <p class="discover-cell-desc">Enjoy the best of music, sports, and live entertainment.</p>
                        <span class="discover-cell-btn">Explore</span>
                    </a>
                    <a href="<?= $this->Url->build('/events') ?>" class="discover-cell">
                        <div class="discover-cell-icon"><img
                                src="<?= $this->Url->image('categories/ACI_Website_Events.png') ?>" alt="Events"></div>
                        <h3 class="discover-cell-title">Events</h3>
                        <p class="discover-cell-desc">Be part of the latest happenings across Araneta City’s malls.</p>
                        <span class="discover-cell-btn">Explore</span>
                    </a>

                    <a href="<?= $this->Url->build('/hotels') ?>" class="discover-cell">
                        <div class="discover-cell-icon"><img
                                src="<?= $this->Url->image('categories/ACI_Website_Hotels.png') ?>" alt="Stays"></div>
                        <h3 class="discover-cell-title">Stay</h3>
                        <p class="discover-cell-desc">Unwind in comfortable accommodations in the City of Firsts.</p>
                        <span class="discover-cell-btn">Explore</span>
                    </a>
                </div>
            </div>
        </section>
        <!-- CATEGORIES SECTION END -->



        <!-- Shows start-->
        <section class="cinema-section">
            <div class="cinema-section-bg">
                <img src="<?= $this->Url->image('banner/coli-3.jpg') ?>" alt="">
            </div>
            <div class="cinema-section-overlay"></div>
            <div class="container">
                <div class="cinema-head">
                    <!-- <span class="cinema-eyebrow">Araneta City Cinema</span> -->
                    <h2 class="cinema-title">Smart Araneta Coliseum & New Frontier Theater</h2>
                    <p class="cinema-desc">Catch world-class concerts, shows, and events at the metro’s premier
                        entertainment venues.</p>
                    <div class="cinema-cta">
                        <a href="<?= $this->Url->build('/shows') ?>" class="cinema-cta-link">View all events<i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>


                <?php $eventsList = !empty($events) ? array_slice($events, 0, 5) : []; ?>
                <?php if (empty($eventsList)): ?>
                    <p class="cinema-empty">No upcoming shows right now. Please check back soon.</p>
                <?php else: ?>
                    <div class="cinema-grid">
                        <?php foreach ($eventsList as $event): ?>
                            <?php
                            $eventTitle = h($event['title'] ?? 'Event');
                            $eventCdn = 'https://dohyi8q62t82e.cloudfront.net/';
                            if (!empty($event['poster_dir'])) {
                                $eventPoster = $eventCdn . ltrim(h($event['poster_dir']), '/');
                            } elseif (!empty($event['poster'])) {
                                $eventPoster = h($event['poster']);
                            } else {
                                $eventPoster = $this->Url->image('gallery/movies/Colony.jpg');
                            }
                            $eventDate = h($event['displaydate'] ?? ($event['dateofevent'] ?? ''));
                            $eventLink = !empty($event['detailPage']) ? h($event['detailPage']) : '#';
                            ?>
                            <a href="<?= $eventLink ?>" target="_blank" rel="noopener" class="cinema-card">
                                <div class="cinema-card-poster">
                                    <img src="<?= $eventPoster ?>" alt="<?= $eventTitle ?>" loading="lazy">
                                </div>
                                <div class="cinema-card-info">
                                    <h4 class="cinema-card-title"><?= $eventTitle ?></h4>
                                    <?php if (!empty($eventDate)): ?>
                                        <p class="cinema-card-date"><?= $eventDate ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>


            </div>
        </section>
        <!-- Shows end-->
        <!--  CINEPLEX18 SECTION  -->
        <section class="cinema-section">
            <div class="cinema-section-bg">
                <img src="<?= $this->Url->image('gallery/thumbnail/thumbnail-ticketbooth - gateway cineplex.jpg') ?>"
                    alt="">
            </div>
            <div class="cinema-section-overlay"></div>
            <div class="container">
                <div class="cinema-head">
                    <!-- <span class="cinema-eyebrow">Araneta City Cinema</span> -->
                    <h2 class="cinema-title">Gateway Cineplex 18</h2>
                    <p class="cinema-desc">Watch the latest blockbusters on state-of-the-art screens at the Philippines’
                        biggest cineplex.</p>
                    <div class="cinema-cta">
                        <a href="<?= $this->Url->build('/cinema') ?>" class="cinema-cta-link">View all movies<i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>


                <?php $moviesList = !empty($movies) ? array_slice($movies, 0, 5) : []; ?>
                <?php if (empty($moviesList)): ?>
                    <p class="cinema-empty">No movies showing right now. Please check back soon.</p>
                <?php else: ?>
                    <div class="cinema-grid">
                        <?php foreach ($moviesList as $movie): ?>
                            <?php
                            $movieTitle = h($movie['title'] ?? 'Movie');
                            $movieCdn = 'https://dohyi8q62t82e.cloudfront.net/';
                            if (!empty($movie['poster_dir'])) {
                                $moviePoster = $movieCdn . ltrim(h($movie['poster_dir']), '/');
                            } elseif (!empty($movie['poster'])) {
                                $moviePoster = h($movie['poster']);
                            } else {
                                $moviePoster = $this->Url->image('gallery/movies/Colony.jpg');
                            }
                            $movieDate = h($movie['displaydate'] ?? '');
                            $movieLink = !empty($movie['detailPage']) ? h($movie['detailPage']) : '#';
                            ?>
                            <a href="<?= $movieLink ?>" target="_blank" rel="noopener" class="cinema-card">
                                <div class="cinema-card-poster">
                                    <img src="<?= $moviePoster ?>" alt="<?= $movieTitle ?>" loading="lazy">
                                </div>
                                <div class="cinema-card-info">
                                    <h4 class="cinema-card-title"><?= $movieTitle ?></h4>
                                    <?php if (!empty($movieDate)): ?>
                                        <p class="cinema-card-date"><?= $movieDate ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>


            </div>
        </section>
        <!--  CINEPLEX18 SECTION END  -->
        <!--  WORLD KITCHENS & WORLD CELLAR  -->
        <section class="wkc-section">
            <div class="container">
                <div class="wkc-head">
                    <span class="wkc-eyebrow">Dining Destinations</span>
                    <h2 class="wkc-title">World Kitchens &amp; World Cellar</h2>
                    <p class="wkc-desc">Indulge at Araneta City’s newest dining destinations.</p>
                </div>
                <div class="wkc-split">
                    <!-- World Kitchens -->
                    <a href="https://worldkitchens.com.ph" target="_blank" class="wkc-panel wkc-panel--kitchens">
                        <img class="wkc-panel-img" src="/img/banner/wkwc/wk-section.png" alt="World Kitchens">

                    </a>
                    <!-- World Cellar -->
                    <a href="https://worldkitchens.com.ph/menu" target="_blank" class="wkc-panel wkc-panel--cellar">
                        <img class="wkc-panel-img" src="/img/banner/wkwc/wc-section.png" alt="World Cellar">

                    </a>
                </div>
            </div>
        </section>
        <!--  WORLD KITCHENS & WORLD CELLAR END  -->



        <!--  NEWS & STORIES SECTION  -->
        <?php if (!empty($news)): ?>
            <section class="stories-section">
                <div class="container">
                    <div class="stories-head" style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 class="stories-title">Featured Articles</h2>
                        <a href="/news" class="stories-seeall">
                            <span>See all articles</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <style>
                        .stories-seeall {
                            display: inline-flex;
                            align-items: center;
                            gap: 10px;
                            background: #fff;
                            color: #00afab;
                            font-size: 14px;
                            font-weight: 700;
                            padding: 12px 28px;
                            border-radius: 50px;
                            text-decoration: none;
                            transition: all 0.3s ease;
                            box-shadow: 0 4px 15px rgba(0, 175, 171, 0.15);
                            border: 2px solid #00afab;
                            position: relative;
                            overflow: hidden;
                            flex-shrink: 0;
                        }

                        .stories-seeall::before {
                            content: '';
                            position: absolute;
                            top: 0;
                            left: -100%;
                            width: 100%;
                            height: 100%;
                            background: rgba(0, 175, 171, 0.08);
                            transition: left 0.3s ease;
                            z-index: 0;
                        }

                        .stories-seeall:hover::before {
                            left: 100%;
                        }

                        .stories-seeall span,
                        .stories-seeall i {
                            position: relative;
                            z-index: 1;
                        }

                        .stories-seeall:hover {
                            background: #f5f5f5;
                            box-shadow: 0 8px 25px rgba(0, 175, 171, 0.25);
                            transform: translateY(-2px);
                            border-color: #00afab;
                        }

                        .stories-seeall i {
                            transition: transform 0.3s ease;
                        }

                        .stories-seeall:hover i {
                            transform: translateX(4px);
                        }

                        @media (max-width: 768px) {
                            .stories-head {
                                flex-direction: column;
                                gap: 20px;
                                align-items: flex-start !important;
                            }

                            .stories-seeall {
                                font-size: 13px;
                                padding: 10px 24px;
                                align-self: flex-start;
                            }
                        }
                    </style>
                    <?php
                    // Helper to build a story's data
                    $buildStory = function ($item) {
                        $img = !empty($item['img_url'])
                            ? env('API_BASE_URL') . ltrim($item['img_url'], '/')
                            : null;
                        $date = !empty($item['posted_date'])
                            ? date('M j, Y', strtotime($item['posted_date']))
                            : date('M j, Y', strtotime($item['created'] ?? 'now'));
                        return [
                            'img' => $img,
                            'title' => h($item['news_title'] ?? 'Untitled'),
                            'link' => '/news-article/' . h($item['slug'] ?? '#'),
                            'date' => $date,
                            'category' => !empty($item['category']) ? h($item['category']) : 'News',
                            'author' => !empty($item['author']) ? h($item['author']) : 'Araneta City',
                        ];
                    };
                    $newsList = array_values($news);
                    $fallbackImg = $this->Url->image('all/1.jpg');
                    ?>
                    <div class="articles-layout">
                        <!-- Big featured article (left) -->
                        <?php if (!empty($newsList[0])):
                            $s = $buildStory($newsList[0]); ?>
                            <a href="<?= $s['link'] ?>" class="article-feature">
                                <div class="article-feature-media">
                                    <img src="<?= $s['img'] ?? $fallbackImg ?>"
                                        onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';" alt="<?= $s['title'] ?>">
                                    <div class="article-feature-overlay"></div>
                                    <div class="article-feature-body">

                                        <h3 class="article-feature-title"><?= $s['title'] ?></h3>
                                        <p class="article-byline"><?= $s['date'] ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php endif; ?>

                        <!-- Stacked smaller articles (right) -->
                        <div class="article-list">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <?php if (!empty($newsList[$i])):
                                    $s = $buildStory($newsList[$i]); ?>
                                    <a href="<?= $s['link'] ?>" class="article-item">
                                        <div class="article-item-media">
                                            <img src="<?= $s['img'] ?? $fallbackImg ?>"
                                                onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';"
                                                alt="<?= $s['title'] ?>">
                                        </div>
                                        <div class="article-item-body">

                                            <h4 class="article-item-title"><?= $s['title'] ?></h4>
                                            <p class="article-byline"><?= $s['date'] ?></p>
                                        </div>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <!-- Debug: News section hidden because $news is empty -->
            <!-- Check logs for API response -->
        <?php endif; ?>
        <!--  NEWS & STORIES SECTION END  -->
        <!-- APP DOWNLOAD SECTION WITH GRADIENT BACKGROUND -->
        <!-- APP DOWNLOAD SECTION -->
        <section class="app-promo-section">
            <div class="container">
                <div class="app-promo-grid">

                    <!-- LEFT -->
                    <div class="app-promo-left">
                        <div class="app-promo-brand">
                            <img src="<?= $this->Url->image('app-section/applogo.png') ?>" alt="Araneta City"
                                class="app-promo-brand-icon">
                            <span class="app-promo-brand-name">Araneta City</span>
                            <span class="app-promo-brand-sub">Mobile App</span>
                        </div>

                        <h2 class="app-promo-headline">Download <br>the app now!</h2>

                        <div class="app-promo-qr-card">
                            <div class="app-promo-qr-left">

                                <img src="<?= $this->Url->image('app-section/qr-app.png') ?>" alt="Scan to download"
                                    class="app-promo-qr-img">
                            </div>
                            <span class="app-promo-or">or</span>
                            <div class="app-promo-qr-right">
                                <a href="https://apps.apple.com/us/app/araneta-city/id1475621864?ls=1" target="_blank"
                                    rel="noopener noreferrer" class="app-promo-store-row">
                                    <i class="fab fa-apple"></i>
                                    <span>Download on the<br><strong>App Store</strong></span>
                                    <span class="app-promo-rating">★ 4.8/5<small>App ratings</small></span>
                                </a>
                                <a href="https://play.google.com/store/apps/details?id=com.aranetacityapps"
                                    target="_blank" rel="noopener noreferrer" class="app-promo-store-row">
                                    <i class="fab fa-google-play"></i>
                                    <span>GET IT ON<br><strong>Google Play</strong></span>
                                    <span class="app-promo-rating">★ 4.7/5<small>App ratings</small></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: finished promo graphic -->
                    <div class="app-promo-right">
                        <a href="https://qrco.de/bbPerg" target="_blank" rel="noopener noreferrer">
                            <img src="<?= $this->Url->image('app-section/app-new.png') ?>"
                                alt="Araneta City App — All you need in one app">
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <style>
            .app-promo-section {
                padding: 70px 0;
                background: #fff;
            }

            .app-promo-grid {
                display: grid;
                grid-template-columns: 1fr 1.5fr;
                gap: 56px;
                align-items: center;
            }

            /* Left column */
            .app-promo-brand {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
            }

            .app-promo-brand-icon {
                width: 34px;
                height: 34px;
                object-fit: contain;
                flex-shrink: 0;
            }

            .app-promo-brand-name {
                font-size: 18px;
                font-weight: 800;
                color: #cd046c;
            }

            .app-promo-brand-sub {
                font-size: 14px;
                color: #cd046c;
            }

            .app-promo-headline {
                font-size: 44px;
                font-weight: 800;
                line-height: 1.1;
                letter-spacing: -0.02em;
                color: #cd046c;
                margin: 0 0 15px;
                text-align: left;
            }

            .app-promo-qr-card {
                background:
                    linear-gradient(rgb(28 0 17 / 67%), rgb(14 3 13 / 70%));
                border-radius: 16px;
                padding: 24px 26px;
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .app-promo-qr-left {
                display: flex;
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .app-promo-qr-label {
                font-size: 13px;
                color: rgba(255, 255, 255, 0.7);
                font-weight: 600;
            }

            .app-promo-qr-img {
                width: 110px;
                height: 115px;
                border-radius: 8px;
                background: #fff;
                padding: 0px;
                object-fit: contain;
            }

            .app-promo-or {
                color: rgba(255, 255, 255, 0.4);
                font-size: 13px;
                font-weight: 600;
            }

            .app-promo-qr-right {
                display: flex;
                flex-direction: column;
                gap: 10px;
                flex: 1;
                min-width: 220px;
            }

            .app-promo-store-row {
                display: flex;
                align-items: center;
                gap: 12px;
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgb(255 255 255);
                border-radius: 10px;
                padding: 10px 14px;
                text-decoration: none;
                color: #fff;
            }

            .app-promo-store-row i {
                font-size: 20px;
                color: #fff;
                flex-shrink: 0;
            }

            .app-promo-store-row span {
                font-size: 11.5px;
                color: rgba(255, 255, 255, 0.75);
                line-height: 1.3;
                flex: 1;
                text-align: left;
            }

            .app-promo-store-row strong {
                font-size: 14px;
                color: #fff;
            }

            .app-promo-rating {
                font-size: 12.5px;
                font-weight: 700;
                color: #00afab;
                text-align: right;
                flex-shrink: 0;
            }

            .app-promo-rating small {
                display: block;
                font-size: 10px;
                font-weight: 500;
                color: rgba(255, 255, 255, 0.5);
            }

            /* Hide ratings on mobile */
            @media (max-width: 450px) {
                .app-promo-rating {
                    display: none;
                }
            }

            /* Right: finished promo graphic */
            .app-promo-right {
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 20px 50px rgba(147, 53, 141, 0.18);
            }

            .app-promo-right img {
                width: 100%;
                height: auto;
                object-fit: contain;
                display: block;
            }

            @media (max-width:960px) {
                .app-promo-grid {
                    grid-template-columns: 1fr;
                    gap: 36px;
                }

                .app-promo-headline {
                    font-size: 34px;
                }

            }

            @media (max-width:960px) {

                .app-promo-qr-right {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    flex: 1;
                    min-width: 129px;
                }
            }
        </style>
        <!-- APP DOWNLOAD SECTION END -->
        <!-- APP DOWNLOAD SECTION END -->

        <!--  APP DOWNLOAD SECTION  -->
        <!-- <section class="gradient-bg hidden-section" data-scrollax-parent="true">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="colomn-text pad-top-column-text fl-wrap">
                            <div class="colomn-text-title">
                                <h3>Download the Araneta City App</h3>
                                <p>Get exclusive deals, navigate the city with ease, find stores and restaurants, check
                                    event schedules, and earn rewards. Your complete Araneta City guide in your pocket.
                                </p>
                                <a href="#" class="down-btn color3-bg"><i class="fab fa-apple"></i> Apple Store</a>
                                <a href="#" class="down-btn color3-bg"><i class="fab fa-android"></i> Google Play</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="collage-image">
                            <img src="<?= $this->Url->image('banner/aciapp.png') ?>" class="main-collage-image" alt="">


                        </div>
                    </div>
                </div>
            </div>
            <div class="gradient-bg-figure" style="right:-30px;top:10px;"></div>
            <div class="gradient-bg-figure" style="left:-20px;bottom:30px;"></div>
            <div class="circle-wrap" style="left:270px;top:120px;" data-scrollax="properties: { translateY: '-200px' }">
                <div class="circle_bg-bal circle_bg-bal_small"></div>
            </div>
            <div class="circle-wrap" style="right:420px;bottom:-70px;"
                data-scrollax="properties: { translateY: '150px' }">
                <div class="circle_bg-bal circle_bg-bal_big"></div>
            </div>
            <div class="circle-wrap" style="left:420px;top:-70px;" data-scrollax="properties: { translateY: '100px' }">
                <div class="circle_bg-bal circle_bg-bal_big"></div>
            </div>
        </section> -->
        <!--  APP DOWNLOAD SECTION END  -->
    </div>
    <!--content end-->

    <!-- FLOATING APP WIDGET -->
    <div id="floating-app-widget" class="floating-app-widget"
        style="position: fixed; top: 100px; right: 30px; z-index: 999; display: flex; flex-direction: column; align-items: center; gap: 10px; animation: slideInUp 0.5s ease-out;">
        <!-- Close Button -->
        <button id="close-floating-app" class="close-floating-app"
            style="position: absolute; top: -10px; right: -10px; width: 36px; height: 36px; border-radius: 50%; background: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease; font-size: 18px; color: #666; font-weight: bold; z-index: 10;">
            ✕
        </button>

        <!-- App Mockup Container -->
        <div class="floating-app-container" style="position: relative; text-align: center;">
            <a href="https://qrco.de/bbPerg" target="_blank" rel="noopener noreferrer"
                style="display: inline-block; text-decoration: none;">
                <img src="<?= $this->Url->image('banner/aciapp.png') ?>" alt="Araneta City App"
                    style="width: 220px; height: auto; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.25)); cursor: pointer; transition: transform 0.3s ease;">
            </a>
        </div>
    </div>

    <style>
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .floating-app-widget {
            animation: slideInUp 0.5s ease-out;
        }

        #close-floating-app:hover {
            background: #f0f0f0 !important;
            transform: scale(1.1);
        }

        .floating-app-container a:hover img {
            transform: scale(1.08);
        }

        .floating-app-cta:hover {
            transform: translateX(-50%) scale(1.05) !important;
            box-shadow: 0 6px 16px rgba(255, 20, 147, 0.6) !important;
        }

        /* Mobile: Make widget smaller and reposition */
        @media (max-width: 768px) {
            .floating-app-widget {
                top: 120px !important;
                right: 20px !important;
            }

            .floating-app-container img {
                width: 160px !important;
            }

            .floating-app-cta {
                font-size: 12px !important;
                padding: 7px 14px !important;
            }

            #close-floating-app {
                width: 32px !important;
                height: 32px !important;
                font-size: 16px !important;
            }
        }

        /* Small mobile: Hide on very small screens */
        @media (max-width: 480px) {
            .floating-app-widget {
                top: 110px !important;
                right: 15px !important;
            }

            .floating-app-container img {
                width: 140px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const closeBtn = document.getElementById('close-floating-app');
            const widget = document.getElementById('floating-app-widget');

            if (closeBtn && widget) {
                closeBtn.addEventListener('click', function () {
                    widget.style.animation = 'slideInUp 0.5s ease-out reverse forwards';
                    setTimeout(function () {
                        widget.style.display = 'none';
                        // Store in sessionStorage so it doesn't show again during this session
                        sessionStorage.setItem('floatingAppClosed', 'true');
                    }, 500);
                });

                // Check if user already closed it this session
                if (sessionStorage.getItem('floatingAppClosed') === 'true') {
                    widget.style.display = 'none';
                }
            }
        });
    </script>
    <!-- FLOATING APP WIDGET END -->

</div>
<!-- wrapper end-->