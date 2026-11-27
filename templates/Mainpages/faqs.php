<?php
/**
 * FAQs — static content (Araneta City).
 * Accordion layout, styled to match the about/policy pages.
 *
 * @var \App\View\AppView $this
 */

$faqs = [
    [
        'q' => 'Where is Araneta City located?',
        'a' => '<p>When we say we\'re literally the center of the metro, we\'re not kidding. The 35-hectare Araneta City sits squarely at the heart of Quezon City, at the crossroads of two major roadways &mdash; EDSA and Aurora Boulevard.</p>
        <p>Commuters can conveniently access it from the North-South and East-West corridors of Metro Manila: via jeepney and bus routes, as well as two major train systems, the MRT-3 and LRT-2.</p>',
    ],
    [
        'q' => 'What can I do at Araneta City?',
        'a' => '<p>Have you got all day to read this? Araneta City has 2,108 tenants in over 1.1 million square meters of shopping area, so you can pretty much shop \'til you drop. And when you feel like dropping, remember there are 300+ food tenants all over the center to nourish you for another round of shopping.</p>
        <p>On certain days you could choose to catch some shows or a sports game at either the Smart Araneta Coliseum or New Frontier Theater. If movies are more your jam, we\'ve got two Cineplexes with a total of 14 cinemas playing the latest blockbusters. And this is just the tip of the proverbial iceberg.</p>',
    ],
    [
        'q' => 'Can I work or live at Araneta City?',
        'a' => '<p>We\'re glad you asked. For long-term plans, you might want to work here in one of the numerous BPO offices. The rise of CyberPark is in full swing, with CyberPark Tower 1 (CPT1) ready for occupancy and several offices about to open, and CPT2 construction ongoing.</p>
        <p>Or you could move in for the total experience &mdash; we already have 10 of the planned 19 residential towers that comprise Manhattan Garden City already up and lived in.</p>',
    ],
    [
        'q' => 'How do I get to Araneta City?',
        'a' => '<p>Via MRT-3 on EDSA, go to Cubao station from either Monumento (if you are coming from the north) or Taft (if you are coming from the south). LRT-2 also has a Cubao station a few stops from Santolan station from the east, and a little farther from Recto station from the west.</p>
        <p>Buses also ply the EDSA route coming from as far as Baguio from the north, and even Mindanao via the Batangas-Laguna route from the south; and Cogeo, Marikina, and Antipolo jeeps abound along Aurora Boulevard. Both commuter routes hit the EDSA / Aurora Blvd. intersection, just a block away from the entry point of Araneta City.</p>
        <p>Car owners may follow these commuter routes but, for your sanity, we suggest you take side roads and minor roads (like P. Tuazon or Main Avenue) into Araneta City &mdash; with Waze as your trusty partner!</p>',
    ],
    [
        'q' => 'What malls are in Araneta City?',
        'a' => '<p>We have the multi-awarded Gateway Mall, whose construction sparked the renaissance of Araneta City. There is also Ali Mall, which was the first mall in the country, named after the late boxing icon Muhammad Ali &mdash; who not only fought the famed "Thrilla in Manila" bout at the Araneta Coliseum but also came back to inaugurate it. And finally, there\'s the New Farmers Plaza, a shopping destination for bargain hunters.</p>',
    ],
];
?>

<style>
    .faq-page {
        background: #fff;
        text-align: left;
        padding: 46px 0 70px;
    }

    .faq-page * {
        text-align: left;
    }

    .faq-wrap {
        width: 92%;
        max-width: 840px;
        margin: 0 auto;
    }

    /* Header */
    .faq-eyebrow {
        display: inline-block;
        color: #6b2d7b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .faq-title {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.5px;
        line-height: 1.05;
    }

    .faq-subtitle {
        font-size: 16px;
        color: #777;
        margin: 0 0 40px;
        line-height: 1.5;
    }

    /* Accordion */
    .faq-list {
        border-top: 1px solid #eee;
    }

    .faq-item {
        border-bottom: 1px solid #eee;
    }

    .faq-q {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        width: 100%;
        background: none;
        border: none;
        cursor: pointer;
        padding: 22px 4px;
        text-align: left;
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        transition: color 0.2s ease;
    }

    .faq-q:hover {
        color: #00afab;
    }

    .faq-q-icon {
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #eef6f5;
        color: #00afab;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: transform 0.3s ease, background 0.2s ease, color 0.2s ease;
    }

    .faq-item.is-open .faq-q-icon {
        transform: rotate(45deg);
        background: #00afab;
        color: #fff;
    }

    .faq-a {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s ease;
    }

    .faq-a-inner {
        padding: 0 4px 24px;
        font-size: 15px;
        line-height: 1.75;
        color: #666;
    }

    .faq-a-inner p {
        margin: 0 0 12px;
    }

    .faq-a-inner p:last-child {
        margin-bottom: 0;
    }

    /* Contact callout */
    .faq-cta {
        margin-top: 44px;
        background: #f7f7f9;
        border-radius: 14px;
        padding: 30px 32px;
        text-align: center;
    }

    .faq-cta * {
        text-align: center;
    }

    .faq-cta-title {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 8px;
    }

    .faq-cta-text {
        font-size: 14px;
        color: #888;
        margin: 0 auto 18px;
        max-width: 420px;
        line-height: 1.5;
    }

    .faq-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #6b2d7b;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 12px 26px;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.25s ease;
    }

    .faq-cta-btn:hover {
        background: #582465;
        color: #fff;
    }

    @media (max-width: 600px) {
        .faq-title {
            font-size: 32px;
        }

        .faq-q {
            font-size: 16px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="faq-page">
            <div class="faq-wrap">
                <!-- Header -->
                <span class="faq-eyebrow">Help Center</span>
                <h1 class="faq-title">Frequently Asked Questions</h1>
                <p class="faq-subtitle">Everything you need to know about getting to, shopping at, and enjoying Araneta
                    City.</p>

                <!-- Accordion -->
                <div class="faq-list">
                    <?php foreach ($faqs as $i => $faq): ?>
                        <div class="faq-item <?= $i === 0 ? 'is-open' : '' ?>">
                            <button type="button" class="faq-q" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                                <span><?= h($faq['q']) ?></span>
                                <span class="faq-q-icon"><i class="fas fa-plus"></i></span>
                            </button>
                            <div class="faq-a">
                                <div class="faq-a-inner"><?= $faq['a'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Still have questions -->
                <div class="faq-cta">
                    <h2 class="faq-cta-title">Still have questions?</h2>
                    <p class="faq-cta-text">Our team is happy to help. Reach out and we'll get back to you.</p>
                    <a href="/contact" class="faq-cta-btn">Contact Us &rarr;</a>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

<script>
    (function () {
        'use strict';

        function setOpen(item, open) {
            var answer = item.querySelector('.faq-a');
            var btn = item.querySelector('.faq-q');
            if (open) {
                item.classList.add('is-open');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                if (btn) btn.setAttribute('aria-expanded', 'true');
            } else {
                item.classList.remove('is-open');
                answer.style.maxHeight = null;
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }

        function init() {
            var items = document.querySelectorAll('.faq-item');
            if (!items.length) return;

            // Open the first item by default.
            items.forEach(function (item, idx) {
                if (item.classList.contains('is-open')) {
                    setOpen(item, true);
                }
                var btn = item.querySelector('.faq-q');
                if (!btn) return;
                btn.addEventListener('click', function () {
                    var isOpen = item.classList.contains('is-open');
                    // Close all, then toggle this one (single-open accordion).
                    items.forEach(function (other) { setOpen(other, false); });
                    if (!isOpen) setOpen(item, true);
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
