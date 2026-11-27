<?php
/**
 * Contact Page — static content + inquiry form.
 * Modern layout matching the about/policy design.
 *
 * @var \App\View\AppView $this
 */
?>

<style>
    .ct-page {
        background: #fff;
        text-align: left;
        padding: 46px 0 70px;
    }

    .ct-page * {
        text-align: left;
    }

    .ct-wrap {
        width: 92%;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* Header */
    .ct-eyebrow {
        display: inline-block;
        color: #6b2d7b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .ct-title {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.5px;
        line-height: 1.05;
    }

    .ct-subtitle {
        font-size: 16px;
        color: #777;
        margin: 0 0 40px;
        max-width: 520px;
        line-height: 1.5;
    }

    /* Two-column */
    .ct-layout {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 30px;
        align-items: start;
    }

    /* Form card */
    .ct-form-card {
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 30px 32px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .ct-form-title {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 20px;
    }

    .ct-field {
        margin-bottom: 18px;
    }

    .ct-field.ct-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .ct-field.ct-row-three {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
    }

    .ct-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 7px;
    }

    .ct-input,
    .ct-textarea {
        width: 100%;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        color: #1a1a1a;
        background: #fff;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .ct-input:focus,
    .ct-textarea:focus {
        border-color: #00afab;
        box-shadow: 0 0 0 3px rgba(0, 175, 171, 0.12);
    }

    .ct-textarea {
        resize: vertical;
        min-height: 130px;
    }

    .ct-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #00afab;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        padding: 13px 30px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.25s ease, transform 0.2s ease;
    }

    .ct-submit:hover {
        background: #008b88;
        transform: translateY(-2px);
    }

    .ct-form-note {
        font-size: 12.5px;
        color: #999;
        margin: 14px 0 0;
    }

    /* Info side */
    .ct-info-card {
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 24px 26px;
        margin-bottom: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .ct-info-title {
        font-size: 16px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 16px;
    }

    .ct-info-row {
        display: flex;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #f4f4f4;
    }

    .ct-info-row:last-child {
        border-bottom: none;
    }

    .ct-info-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eef6f5;
        color: #00afab;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .ct-info-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #9a9a9a;
        margin: 0 0 3px;
    }

    .ct-info-value {
        font-size: 14px;
        color: #444;
        margin: 0;
        line-height: 1.5;
    }

    .ct-info-value a {
        color: #444;
        text-decoration: none;
    }

    .ct-info-value a:hover {
        color: #00afab;
    }

    /* Socials */
    .ct-socials {
        display: flex;
        gap: 10px;
    }

    .ct-social {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #00afab;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        text-decoration: none;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .ct-social:hover {
        transform: translateY(-2px);
        background: #008b88;
        color: #fff;
    }

    /* Map */
    .ct-map {
        margin-top: 44px;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #eee;
    }

    .ct-map iframe {
        display: block;
        width: 100%;
        height: 380px;
        border: 0;
    }

    /* Responsive */
    @media (max-width: 860px) {
        .ct-layout {
            grid-template-columns: 1fr;
        }

        .ct-title {
            font-size: 32px;
        }

        .ct-field.ct-row-three {
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
    }

    @media (max-width: 480px) {
        .ct-field.ct-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .ct-field.ct-row-three {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .ct-map iframe {
            height: 300px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="ct-page">
            <div class="ct-wrap">
                <!-- Header -->
                <span class="ct-eyebrow">Get in Touch</span>
                <h1 class="ct-title">Contact Us</h1>
                <p class="ct-subtitle">Have a question or an inquiry? Send us a message and our team will get back to
                    you as soon as possible.</p>

                <div class="ct-layout">
                    <!-- Form -->
                    <div class="ct-form-card">
                        <h2 class="ct-form-title">Send us a message</h2>

                        <?= $this->Flash->render() ?>

                        <?= $this->Form->create(null, ['id' => 'inquiry-form-1', 'url' => ['controller' => 'Mainpages', 'action' => 'contact']]) ?>
                        <!-- First Name, Middle Name & Last Name Row -->
                        <div id="returnMessageForm1"></div>
                        <div class="ct-field ct-row-three">
                            <div>
                                <label class="ct-label" for="ct-firstname">First Name <span
                                        style="color:red;">*</span></label>
                                <input class="ct-input" type="text" id="ct-firstname" name="first_name"
                                    placeholder="Your first name" required>
                            </div>
                            <div>
                                <label class="ct-label" for="ct-middlename">Middle Name <span
                                        style="color:#999; font-weight:400;">(Optional)</span></label>
                                <input class="ct-input" type="text" id="ct-middlename" name="middle_name"
                                    placeholder="Your middle name">
                            </div>
                            <div>
                                <label class="ct-label" for="ct-lastname">Last Name <span
                                        style="color:red;">*</span></label>
                                <input class="ct-input" type="text" id="ct-lastname" name="last_name"
                                    placeholder="Your last name" required>
                            </div>
                        </div>

                        <!-- Contact Number & Email Address Row -->
                        <div class="ct-field ct-row">
                            <div>
                                <label class="ct-label" for="ct-phone">Mobile Number <span
                                        style="color:red;">*</span></label>
                                <input class="ct-input" type="tel" id="mobilePhone-1" name="contact_number"
                                    placeholder="(09XX) XXX-XXXX" required>
                            </div>
                            <div>
                                <label class="ct-label" for="ct-email">Email Address <span
                                        style="color:red;">*</span></label>
                                <input class="ct-input" type="email" id="ct-email" name="email"
                                    placeholder="you@example.com" required>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="ct-field">
                            <label class="ct-label" for="ct-subject">Subject <span style="color:red;">*</span></label>
                            <input class="ct-input" type="text" id="ct-subject" name="subject"
                                placeholder="What is this about?" required>
                        </div>

                        <!-- Message -->
                        <div class="ct-field">
                            <label class="ct-label" for="ct-message">Message <span style="color:red;">*</span></label>
                            <textarea class="ct-textarea" id="ct-message" name="message" placeholder="Your message..."
                                required></textarea>
                        </div>

                        <div class="ct-field ct-consent">
                            <label
                                style="display:flex; align-items:flex-start; gap:8px; font-size:12px; color:#444; cursor:pointer;">
                                <input type="checkbox" id="ct-consent" name="consent" required style="margin-top:3px;">
                                <span>I agree to the <a href="/privacy-policy" style="color:#00afab;">Privacy Policy</a>
                                    and consent to being contacted regarding my inquiry. <span
                                        style="color:red;">*</span></span>
                            </label>
                        </div>

                        <button type="submit" class="ct-submit" id="Submit">Send Message &rarr;</button>

                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                        <?= $this->Form->end() ?>
                    </div>

                    <!-- Info -->
                    <div class="ct-info">
                        <div class="ct-info-card">
                            <h3 class="ct-info-title">Contact Information</h3>
                            <div class="ct-info-row">
                                <div class="ct-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <p class="ct-info-label">Address</p>
                                    <p class="ct-info-value">25th&ndash;26th Floors, Gateway Tower,<br>Araneta City,
                                        Quezon City</p>
                                </div>
                            </div>
                            <div class="ct-info-row">
                                <div class="ct-info-icon"><i class="fas fa-phone"></i></div>
                                <div>
                                    <p class="ct-info-label">Phone</p>
                                    <p class="ct-info-value"><a href="tel:+63285884000">(02) 8588-4000</a></p>
                                </div>
                            </div>
                            <div class="ct-info-row">
                                <div class="ct-info-icon"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <p class="ct-info-label">Email</p>
                                    <p class="ct-info-value"><a
                                            href="mailto:info@aranetagroup.com">info@aranetagroup.com</a></p>
                                </div>
                            </div>
                            <div class="ct-info-row">
                                <div class="ct-info-icon"><i class="far fa-clock"></i></div>
                                <div>
                                    <p class="ct-info-label">Office Hours</p>
                                    <p class="ct-info-value">Mondays to Fridays<br>9:00 a.m. &ndash; 6:00 p.m.</p>
                                </div>
                            </div>
                        </div>

                        <div class="ct-info-card">
                            <h3 class="ct-info-title">Follow Us</h3>
                            <div class="ct-socials">
                                <a href="https://www.facebook.com/AranetaCity/" target="_blank" rel="noopener"
                                    class="ct-social" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://www.instagram.com/aranetacity/" target="_blank" rel="noopener"
                                    class="ct-social" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="https://twitter.com/AranetaCity" target="_blank" rel="noopener"
                                    class="ct-social" aria-label="X"><i class="fab fa-x-twitter"></i></a>
                                <a href="https://www.youtube.com/@AranetaCity" target="_blank" rel="noopener"
                                    class="ct-social" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                <a href="https://www.linkedin.com/company/aranetacity" target="_blank" rel="noopener"
                                    class="ct-social" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                <a href="https://www.tiktok.com/@aranetacity" target="_blank" rel="noopener"
                                    class="ct-social" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                                <a href="https://www.threads.net/@aranetacity" target="_blank" rel="noopener"
                                    class="ct-social" aria-label="Threads"><i class="fa-brands fa-threads"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="ct-map">
                    <iframe src="https://www.google.com/maps?q=Araneta+City,+Cubao,+Quezon+City&output=embed"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Araneta City map"></iframe>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=6Ld5irMtAAAAADtczK77DcU85PV5qB59IXmHsQ_t"></script>

<script type="text/javascript">
    window.addEventListener('load', function () {
        (function ($) {
            $("#Submit").prop('disabled', true);

            setInterval(function () {
                validate();
            }, 1000);

            function validate() {
                const errors = [];

                if ($("#ct-firstname").val() == "") {
                    errors.push("Please enter a valid First Name");
                }
                if ($("#ct-lastname").val() == "") {
                    errors.push("Please enter a valid Last Name");
                }
                if ($("#ct-email").val() == "") {
                    errors.push("Please enter a valid email");
                }
                if ($("#mobilePhone-1").val() == "" || (typeof iti1 !== "undefined" && !iti1.isValidNumberPrecise())) {
                    errors.push("Please enter a valid mobile phone");
                }
                if ($("#ct-subject").val() == "") {
                    errors.push("Please enter a subject");
                }
                if ($("#ct-message").val() == "") {
                    errors.push("Please enter a message");
                }
                if (!$("#ct-consent").is(":checked")) {
                    errors.push("Please check the consent box");
                }

                $("#Submit").prop('disabled', errors.length > 0);
            }

            $("#inquiry-form-1").on("submit", function (e) {
                e.preventDefault();
                const form = this;

                // Prevent double-submit: disable immediately and bail if already submitting
                if ($("#Submit").prop('disabled') && form.dataset.submitting === "true") {
                    return;
                }
                form.dataset.submitting = "true";
                $("#Submit").prop('disabled', true).text('Sending...');

                grecaptcha.ready(function () {
                    grecaptcha.execute('6Ld5irMtAAAAADtczK77DcU85PV5qB59IXmHsQ_t', { action: 'contact_form' })
                        .then(function (token) {
                            $("#g-recaptcha-response").val(token);
                            form.submit();
                        })
                        .catch(function () {
                            // Re-enable on failure so the user can retry
                            form.dataset.submitting = "false";
                            $("#Submit").prop('disabled', false).text('Send Message →');
                        });
                });
            });
        })(jQuery);
    });
</script>