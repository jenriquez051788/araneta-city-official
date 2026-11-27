<?php
/**
 * About Page — The Araneta Group (static content).
 * Styled to match the clean detail-page design.
 *
 * @var \App\View\AppView $this
 */
?>

<style>
    .ab-page {
        background: #fff;
        text-align: left;
    }

    .ab-page * {
        text-align: left;
    }

    .ab-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Page header (clean, no banner) */
    .ab-pagehead {
        padding: 46px 0 10px;
    }

    .ab-eyebrow {
        display: inline-block;
        color: #6b2d7b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .ab-pagetitle {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.05;
    }

    /* Main two-column */
    .ab-main-sec {
        padding: 30px 0 60px;
    }

    .ab-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 40px;
        align-items: start;
    }

    .ab-block {
        margin-bottom: 36px;
    }

    .ab-block-title {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 16px;
        letter-spacing: -0.2px;
    }

    .ab-text {
        font-size: 15px;
        line-height: 1.75;
        color: #666;
    }

    .ab-text p {
        margin: 0 0 12px;
    }

    /* Vision & Mission cards */
    .ab-vm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .ab-vm-card {
        background: #f9f9fb;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 24px;
    }

    .ab-vm-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #00afab;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    .ab-vm-text {
        font-size: 14px;
        line-height: 1.7;
        color: #666;
    }

    .ab-vm-text p {
        margin: 0 0 10px;
    }

    /* Sidebar */
    .ab-side-card {
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 22px 24px;
        margin-bottom: 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .ab-side-title {
        font-size: 18px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 14px;
    }

    .ab-side-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 0;
        font-size: 14px;
        border-bottom: 1px solid #f2f2f2;
    }

    .ab-side-row:last-child {
        border-bottom: none;
    }

    .ab-side-row .lbl {
        color: #666;
    }

    .ab-side-row .val {
        font-weight: 700;
        color: #1a1a1a;
        text-align: right;
    }

    .ab-side-text {
        font-size: 14px;
        line-height: 1.6;
        color: #666;
        margin: 0 0 16px;
    }

    .ab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #00afab;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        padding: 11px 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.25s ease;
    }

    .ab-btn:hover {
        background: #008b88;
        color: #fff;
    }

    .ab-contact-row {
        display: flex;
        gap: 10px;
        padding: 10px 0;
        font-size: 14px;
        color: #666;
        line-height: 1.5;
    }

    .ab-contact-row i {
        color: #00afab;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .ab-contact-row .lbl {
        font-weight: 700;
        color: #1a1a1a;
    }

    /* Centered section (values, business units) */
    .ab-csec {
        padding: 56px 0;
    }

    .ab-csec--gray {
        background: #f7f7f9;
    }

    .ab-chead {
        text-align: center;
        margin-bottom: 40px;
    }

    .ab-chead * {
        text-align: center;
    }

    .ab-chead-title {
        font-size: 30px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.3px;
    }

    .ab-chead-sub {
        font-size: 15px;
        color: #888;
        margin: 0 auto;
        max-width: 460px;
        line-height: 1.5;
    }

    /* Values grid */
    .ab-values {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
    }

    .ab-value {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 26px 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    }

    .ab-value-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #00afab;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 16px;
    }

    .ab-value-title {
        font-size: 17px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 8px;
    }

    .ab-value-text {
        font-size: 13.5px;
        line-height: 1.6;
        color: #888;
        margin: 0;
    }

    /* Business units grid */
    .ab-units {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }

    .ab-unit {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 26px 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
    }

    .ab-unit-icon {
        color: #00afab;
        font-size: 26px;
        margin-bottom: 16px;
    }

    .ab-unit-tag {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #00afab;
        margin: 0 0 4px;
    }

    .ab-unit-title {
        font-size: 18px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
    }

    .ab-unit-text {
        font-size: 13.5px;
        line-height: 1.6;
        color: #888;
        margin: 0 0 16px;
        flex: 1;
    }

    .ab-unit-link {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #1a1a1a;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ab-unit-link:hover {
        color: #00afab;
    }

    /* Management CTA */
    .ab-mgmt {
        background: #f7f7f9;
        padding: 60px 20px;
        text-align: center;
    }

    .ab-mgmt * {
        text-align: center;
    }

    .ab-mgmt-title {
        font-size: 30px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 14px;
        letter-spacing: -0.3px;
    }

    .ab-mgmt-text {
        font-size: 15px;
        line-height: 1.6;
        color: #888;
        margin: 0 auto 24px;
        max-width: 520px;
    }

    .ab-mgmt-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #6b2d7b;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 13px 28px;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.25s ease;
    }

    .ab-mgmt-btn:hover {
        background: #582465;
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .ab-layout {
            grid-template-columns: 1fr;
        }

        .ab-values {
            grid-template-columns: repeat(2, 1fr);
        }

        .ab-units {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .ab-vm-grid,
        .ab-values,
        .ab-units {
            grid-template-columns: 1fr;
        }

        .ab-hero-ghost {
            font-size: 34px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="ab-page">
            <!-- Header -->
            <div class="ab-pagehead">
                <div class="ab-wrap">
                    <span class="ab-eyebrow">The City of Firsts</span>
                    <h1 class="ab-pagetitle">About the Araneta Group</h1>
                </div>
            </div>

            <!-- Main + sidebar -->
            <div class="ab-main-sec">
                <div class="ab-wrap">
                    <div class="ab-layout">
                        <!-- Left -->
                        <div class="ab-maincol">
                            <div class="ab-block">
                                <h2 class="ab-block-title">Who We Are</h2>
                                <div class="ab-text">
                                    <p>The Araneta Group is a progressive and diversified group of companies anchored on
                                        property development, food service, leisure, and hospitality. Founded by the
                                        visionary J. Amado Araneta, the Araneta Group is known for pioneering projects
                                        that are the first, the biggest, and the best in their class. Currently at the
                                        helm of its growth and expansion is his son, Jorge L. Araneta.</p>
                                    <p>With a long history of embarking on pioneering business ventures, the Araneta
                                        Group is prepared to take up the challenges of the 21st century. The Group's
                                        12,000-strong workforce is committed to a culture of excellence, integrity,
                                        loyalty, and pride.</p>
                                    <p>The Araneta Group is composed of five strategic business units: ACI, Inc., PPI
                                        Holdings Inc., Uniprom Inc., Progressive Development Corporation, and Araneta
                                        Hotels Inc.</p>
                                </div>
                            </div>

                            <div class="ab-block">
                                <h2 class="ab-block-title">Our Vision &amp; Mission</h2>
                                <div class="ab-vm-grid">
                                    <div class="ab-vm-card">
                                        <div class="ab-vm-label"><i class="far fa-eye"></i> Our Vision</div>
                                        <div class="ab-vm-text">
                                            <p>We create inclusive communities that deliver memorable experiences worth
                                                sharing.</p>
                                        </div>
                                    </div>
                                    <div class="ab-vm-card">
                                        <div class="ab-vm-label"><i class="fas fa-bullseye"></i> Our Mission</div>
                                        <div class="ab-vm-text">
                                            <p>By providing the best products and services to the greatest number
                                                anchored on property, food, and entertainment, we will foster our
                                                continuing leadership.</p>
                                            <p>Cultivate an innovative, enabled, and empowered organization that will be
                                                the steward of our name, values, and business.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="ab-sidecol">
                            

                            <div class="ab-side-card">
                                <h3 class="ab-side-title">The Araneta Group Management</h3>
                                <p class="ab-side-text">Meet the leadership team steering the Group's five strategic
                                    business units.</p>
                                <a href="/about/management" class="ab-btn">View Management &rarr;</a>
                            </div>

                            <div class="ab-side-card">
                                <h3 class="ab-side-title">Contact Information</h3>
                                <div class="ab-contact-row">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><span class="lbl">Address:</span><br>25th&ndash;26th Floors Gateway Tower,
                                        Araneta City, Quezon City</span>
                                </div>
                                <div class="ab-contact-row">
                                    <i class="fas fa-phone"></i>
                                    <span><span class="lbl">Phone:</span> (02) 8588-4000</span>
                                </div>
                                <div class="ab-contact-row">
                                    <i class="fas fa-envelope"></i>
                                    <span><span class="lbl">Email:</span> info@aranetagroup.com</span>
                                </div>
                                <div class="ab-contact-row">
                                    <i class="fas fa-globe"></i>
                                    <span><span class="lbl">Website:</span> www.aranetacity.com</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Core Values -->
            <div class="ab-csec ab-csec--gray">
                <div class="ab-wrap">
                    <div class="ab-chead">
                        <h2 class="ab-chead-title">Our Core Values</h2>
                        <p class="ab-chead-sub">Four values guide how the Araneta Group builds, operates, and serves.</p>
                    </div>
                    <div class="ab-values">
                        <div class="ab-value">
                            <div class="ab-value-icon"><i class="fas fa-seedling"></i></div>
                            <h3 class="ab-value-title">Stewardship</h3>
                            <p class="ab-value-text">We protect and grow what has been entrusted to us &mdash; our name,
                                our communities, and the legacy of our founder.</p>
                        </div>
                        <div class="ab-value">
                            <div class="ab-value-icon"><i class="fas fa-medal"></i></div>
                            <h3 class="ab-value-title">Excellence</h3>
                            <p class="ab-value-text">We hold every product, service, and experience to the standard that
                                made us the first, the biggest, and the best in class.</p>
                        </div>
                        <div class="ab-value">
                            <div class="ab-value-icon"><i class="fas fa-lightbulb"></i></div>
                            <h3 class="ab-value-title">Innovation</h3>
                            <p class="ab-value-text">We keep pioneering &mdash; taking on new ventures and new ideas to
                                meet the challenges of the 21st century.</p>
                        </div>
                        <div class="ab-value">
                            <div class="ab-value-icon"><i class="fas fa-handshake"></i></div>
                            <h3 class="ab-value-title">Integrity</h3>
                            <p class="ab-value-text">We do business honestly and consistently, earning the trust of the
                                customers and partners we serve.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Units -->
            <div class="ab-csec">
                <div class="ab-wrap">
                    <div class="ab-chead">
                        <h2 class="ab-chead-title">Business Units</h2>
                        <p class="ab-chead-sub">Five strategic business units and the Group's social development arm.</p>
                    </div>
                    <div class="ab-units">
                        <div class="ab-unit">
                            <div class="ab-unit-icon"><i class="fas fa-city"></i></div>
                            <p class="ab-unit-tag">Property</p>
                            <h3 class="ab-unit-title">ACI, Inc.</h3>
                            <p class="ab-unit-text">ACI, Inc. is the owner, developer, and manager of Araneta City, a
                                mixed-use hub of retail, entertainment, residential, hospitality, and office
                                developments right at the heart of Metro Manila.</p>
                            <!-- <a href="#" class="ab-unit-link">Learn More &rarr;</a> -->
                        </div>
                        <div class="ab-unit">
                            <div class="ab-unit-icon"><i class="fas fa-utensils"></i></div>
                            <p class="ab-unit-tag">Food Service</p>
                            <h3 class="ab-unit-title">PPI Holdings, Inc.</h3>
                            <p class="ab-unit-text">PPI Holdings, Inc. is the franchisee of Pizza Hut, Taco Bell, and
                                Dairy Queen in the Philippines, with over 250 company-owned food service outlets.</p>
                            <!-- <a href="#" class="ab-unit-link">Learn More &rarr;</a> -->
                        </div>
                        <div class="ab-unit">
                            <div class="ab-unit-icon"><i class="fas fa-ticket-alt"></i></div>
                            <p class="ab-unit-tag">Leisure &amp; Entertainment</p>
                            <h3 class="ab-unit-title">Uniprom, Inc.</h3>
                            <p class="ab-unit-text">Uniprom Inc. (UI) manages the leisure and entertainment ventures of
                                Araneta City: Smart Araneta Coliseum, New Frontier Theater, TicketNet, Gateway Cineplex,
                                and Ali Mall Cinemas.</p>
                            <!-- <a href="#" class="ab-unit-link">Learn More &rarr;</a> -->
                        </div>
                        <div class="ab-unit">
                            <div class="ab-unit-icon"><i class="fas fa-hotel"></i></div>
                            <p class="ab-unit-tag">Hospitality</p>
                            <h3 class="ab-unit-title">Araneta Hotels, Inc.</h3>
                            <p class="ab-unit-text">Araneta Hotels Inc. (AHI) is the owner and developer of the deluxe
                                businessman's hotel Novotel Manila Araneta City, the first Novotel brand of Accor Hotels
                                in the country.</p>
                            <!-- <a href="#" class="ab-unit-link">Learn More &rarr;</a> -->
                        </div>
                        <div class="ab-unit">
                            <div class="ab-unit-icon"><i class="fas fa-project-diagram"></i></div>
                            <p class="ab-unit-tag">Investments</p>
                            <h3 class="ab-unit-title">Progressive Development Corporation</h3>
                            <p class="ab-unit-text">Progressive Development Corporation (PDC) manages investments and
                                property assets of the Araneta Group outside ACI, PPI, UI, and AHI. It serves as
                                incubator and nurturing entity for new businesses.</p>
                            <!-- <a href="#" class="ab-unit-link">Learn More &rarr;</a> -->
                        </div>
                        <div class="ab-unit">
                            <div class="ab-unit-icon"><i class="fas fa-hand-holding-heart"></i></div>
                            <p class="ab-unit-tag">Social Development</p>
                            <h3 class="ab-unit-title">J. Amado Araneta Foundation, Inc.</h3>
                            <p class="ab-unit-text">J. Amado Araneta Foundation (JAAF) is the social development arm of
                                the Araneta Group, dedicated to the vision and legacy of the company's founder.</p>
                            <!-- <a href="#" class="ab-unit-link">Learn More &rarr;</a> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management CTA -->
            <div class="ab-mgmt">
                <div class="ab-wrap">
                    <h2 class="ab-mgmt-title">The Araneta Group Management</h2>
                    <p class="ab-mgmt-text">Led by Chairman, President and CEO Jorge L. Araneta, the Group's management
                        teams steward ACI, Uniprom, Progressive Development Corporation, Araneta Hotels, and Philippine
                        Pizza Holdings.</p>
                    <a href="/about/management" class="ab-mgmt-btn">Meet the Management &rarr;</a>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>
