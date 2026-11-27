<?php
/**
 * Privacy Policy — static content (ACI, Inc.).
 * Modern layout matching the about.php design.
 *
 * @var \App\View\AppView $this
 */

// Section definitions: anchor id, title, and HTML body.
$sections = [
    [
        'id' => 'statement',
        'title' => 'Statement of Policy',
        'body' => '<p>ACI, Inc., otherwise known as ACI (the "Company" or "Araneta City"), is committed to protecting the privacy of personal information you may provide us on this website (the "Site"), as we are conscious of our responsibilities as a "data controller" under Republic Act No. 10173 or the "Data Privacy Act of 2012."</p>
        <p>We believe it is important for you to know how we treat your personal information. The terms of this Privacy Policy apply to all users of this Site. If you do not agree with the terms of this Privacy Policy, you should immediately stop using this Site. Your use of this Site therefore shall be construed as your acceptance of the terms of this Privacy Policy.</p>',
    ],
    [
        'id' => 'personal-information',
        'title' => 'Personal Information',
        'body' => '<p>Personal information is data that can be used to identify a single person. You may be asked to provide your personal information anytime you are in contact with Araneta City or an Araneta City affiliated company.</p>
        <p>You are not required to provide the personal information that we have requested, but if you choose not to do so, in many cases Araneta City will not be able to adequately process and provide you with our products or services, or respond to any queries you may have. ACI may collect the following personal information from you when you manually or electronically submit to us your complaints, inquiries, or requests:</p>
        <ul>
            <li>Full name</li>
            <li>Email address</li>
            <li>Contact numbers</li>
        </ul>',
    ],
    [
        'id' => 'use',
        'title' => 'Use of Information',
        'body' => '<p>The collected personal information is utilized for documentation and processing purposes within the Company and its affiliates pursuant to the Data Sharing Agreement executed among the companies in accordance with the Data Privacy Act of 2012. The personal information enables ACI to properly address complaints, forward them to appropriate internal units for action and response, and provide clients or complainants with appropriate updates and advisories in a legitimate format and in an orderly and timely manner.</p>
        <p>Araneta City and its affiliates may share this personal information with each other and use it consistent with this Privacy Policy and the Data Privacy Act of 2012. Araneta City may also combine it with other information to provide and improve our products, services, content, and advertising.</p>',
    ],
    [
        'id' => 'analytics',
        'title' => 'Website Analytics',
        'body' => '<p>ACI uses Google Analytics, a third-party service, to analyze the web traffic data for us. Data generated is not shared with any other party. Only non-identifiable web traffic data are analyzed, including:</p>
        <ul>
            <li>Your IP address</li>
            <li>The search terms you used</li>
            <li>The date and time you visited the site</li>
            <li>Geolocation</li>
            <li>The referring site or platform (if any) through which you clicked through to this site</li>
            <li>Your operating system</li>
            <li>Web browser type</li>
        </ul>',
    ],
    [
        'id' => 'cookies',
        'title' => 'Cookies',
        'body' => '<p>Our Site may use technologies such as "cookies" to provide visitors with tailored information upon each visit. Cookies are a common part of commercial websites that allow small text files to be sent by a website, accepted by a web browser, and then placed on your hard drive in order to recognize repeat visits to the Site.</p>
        <p>Every time you visit our Site, our servers, through cookies, collect basic technical information such as your domain name, the address of the last URL visited prior to clicking through to the Site, and your browser and operating system. If you like, you can set your browser to notify you before you receive a cookie so you have the chance to accept it, and you can also set your browser to turn off all cookies. You do not need to enable cookies to visit our Site; however, some parts of the Site and some services may be more difficult or impossible to use if cookies are disabled. To emphasize, cookies are not linked to any personal information while on this Site.</p>',
    ],
    [
        'id' => 'protection',
        'title' => 'Protection Measures',
        'body' => '<p>Only authorized ACI personnel have access to the personal information. The personal information will be stored in a database for two (2) years, after which physical records, if any, shall be disposed of through shredding, while digital files shall be anonymized.</p>',
    ],
    [
        'id' => 'access',
        'title' => 'Access, Correction, and Opt-out',
        'body' => '<p>You have the right to ask for a copy of any personal information we hold about you, as well as to ask for it to be corrected if you think it is wrong. To do so, please contact our Data Protection Officer at the contact details provided.</p>
        <p>In addition, each time we send you marketing information by email, we will provide an opportunity for you to unsubscribe from receiving further information.</p>',
    ],
    [
        'id' => 'feedback',
        'title' => 'Feedback on our Privacy Notice',
        'body' => '<p>If you have suggestions with regard to our privacy notice, you may reach our Data Protection Officer, Mondays to Fridays, 9:00 a.m. to 6:00 p.m., through email at <a href="mailto:dpo@aranetagroup.com">dpo@aranetagroup.com</a> or through telephone no. (02) 8-588-4056.</p>',
    ],
];
?>

<style>
    html {
        scroll-behavior: smooth;
    }

    .pol-page {
        background: #fff;
        text-align: left;
        padding: 46px 0 70px;
    }

    .pol-page * {
        text-align: left;
    }

    .pol-wrap {
        width: 92%;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* Header */
    .pol-eyebrow {
        display: inline-block;
        color: #6b2d7b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .pol-title {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 8px;
        letter-spacing: -0.5px;
        line-height: 1.05;
    }

    .pol-meta {
        font-size: 14px;
        color: #999;
        margin: 0 0 36px;
    }

    /* Two-column */
    .pol-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 40px;
        align-items: start;
    }

    /* Content */
    .pol-section {
        padding-bottom: 30px;
        margin-bottom: 30px;
        border-bottom: 1px solid #f0f0f0;
        scroll-margin-top: 90px;
    }

    .pol-section:last-child {
        border-bottom: none;
    }

    .pol-section-title {
        font-size: 22px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 14px;
        letter-spacing: -0.2px;
    }

    .pol-body {
        font-size: 15px;
        line-height: 1.75;
        color: #666;
    }

    .pol-body p {
        margin: 0 0 14px;
    }

    .pol-body p:last-child {
        margin-bottom: 0;
    }

    .pol-body ul {
        margin: 0 0 14px;
        padding-left: 22px;
    }

    .pol-body li {
        margin-bottom: 6px;
        color: #444;
    }

    /* Registration callout */
    .pol-callout {
        background: #f4eef6;
        border: 1px solid #e6d6ec;
        border-radius: 12px;
        padding: 22px 24px;
        margin-bottom: 34px;
    }

    .pol-callout-seals {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .pol-callout-seals img.pol-cert-img {
        max-width: 100%;
        width: 420px;
        height: auto;
    }

    .pol-callout-seals img.pol-seal-img {
        max-width: 100%;
        width: 250px;
        height: auto;
        flex-shrink: 0;
    }

    .pol-callout-title {
        font-size: 15px;
        font-weight: 800;
        color: #6b2d7b;
        margin: 0 0 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pol-callout p {
        font-size: 13.5px;
        line-height: 1.65;
        color: #6a5570;
        margin: 0 0 8px;
    }

    .pol-callout p:last-child {
        margin-bottom: 0;
        font-weight: 700;
    }

    /* Sidebar */
    .pol-side {
        position: sticky;
        top: 90px;
    }

    .pol-side-card {
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .pol-side-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #9a9a9a;
        margin: 0 0 14px;
    }

    .pol-toc {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .pol-toc li {
        margin-bottom: 2px;
    }

    .pol-toc a {
        display: block;
        font-size: 14px;
        color: #555;
        text-decoration: none;
        padding: 7px 10px;
        border-radius: 8px;
        border-left: 2px solid transparent;
        transition: all 0.2s ease;
    }

    .pol-toc a:hover {
        background: #f4f9f9;
        color: #00afab;
        border-left-color: #00afab;
    }

    .pol-dpo-title {
        font-size: 16px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 12px;
    }

    .pol-dpo-row {
        display: flex;
        gap: 10px;
        font-size: 13.5px;
        color: #666;
        line-height: 1.5;
        padding: 7px 0;
    }

    .pol-dpo-row i {
        color: #00afab;
        margin-top: 3px;
        flex-shrink: 0;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .pol-layout {
            grid-template-columns: 1fr;
        }

        .pol-side {
            position: static;
        }

        .pol-title {
            font-size: 32px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="pol-page">
            <div class="pol-wrap">
                <!-- Header -->

                <h1 class="pol-title">Privacy Policy</h1>
                <p class="pol-meta">ACI, Inc. &middot; Compliant with the Data Privacy Act of 2012 (RA 10173)</p>

                <div class="pol-layout">
                    <!-- Content -->
                    <div class="pol-main">
                        <!-- NPC registration callout -->
                        <div class="pol-callout">
                            <div class="pol-callout-seals">
                                <img class="pol-cert-img" src="/img/privacy/RegistrationCert_2026.jpg"
                                    alt="NPC Certificate of Registration">
                                <img class="pol-seal-img" src="/img/privacy/seal.png" alt="NPC Seal">
                            </div>
                            <p class="pol-callout-title"><i class="fas fa-certificate"></i> NPC Certificate of
                                Registration</p>
                            <p>This is to certify that ACI, Inc., with business address at 25F Gateway Tower, Araneta
                                Center, Quezon City, Metro Manila, has duly complied with the registration requirements
                                of the Data Privacy Act of 2012, its Implementing Rules and Regulations, and all related
                                issuances.</p>
                            <p>Effective until 14 July 2027.</p>
                        </div>

                        <?php foreach ($sections as $section): ?>
                            <div class="pol-section" id="<?= h($section['id']) ?>">
                                <h2 class="pol-section-title"><?= h($section['title']) ?></h2>
                                <div class="pol-body"><?= $section['body'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Sidebar -->
                    <div class="pol-side">
                        <div class="pol-side-card">
                            <p class="pol-side-title">On This Page</p>
                            <ul class="pol-toc">
                                <?php foreach ($sections as $section): ?>
                                    <li><a href="#<?= h($section['id']) ?>"><?= h($section['title']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="pol-side-card">
                            <h3 class="pol-dpo-title">Data Protection Officer</h3>
                            <div class="pol-dpo-row">
                                <i class="fas fa-clock"></i>
                                <span>Mondays to Fridays<br>9:00 a.m. &ndash; 6:00 p.m.</span>
                            </div>
                            <div class="pol-dpo-row">
                                <i class="fas fa-envelope"></i>
                                <span><a href="mailto:dpo@aranetagroup.com">dpo@aranetagroup.com</a></span>
                            </div>
                            <div class="pol-dpo-row">
                                <i class="fas fa-phone"></i>
                                <span>(02) 8-588-4056</span>
                            </div>
                            <div class="pol-dpo-row">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>25F Gateway Tower, Araneta Center, Quezon City</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>