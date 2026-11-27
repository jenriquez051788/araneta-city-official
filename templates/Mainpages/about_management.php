<?php
/**
 * About > Management
 * Business-unit management teams shown as tabs — matches aranetacity.com/our-team.
 *
 * Data comes from the database (team_members table) via
 * MainpagesController::aboutManagement(), which provides:
 *   - $chairman: ['name' => ..., 'title' => ...]
 *   - $units: array of ['tab', 'tag', 'name', 'groups' => [role => members[]]]
 *
 * Manage team members in the CMS at /team-members.
 *
 * @var \App\View\AppView $this
 * @var array $chairman
 * @var array $units
 */

/* Legacy hardcoded data — now sourced from the database (team_members table).
$_unusedOldArray = [
    [
        'tab' => 'ACI, Inc.',
        'tag' => 'Property',
        'name' => 'ACI, Inc. Management Team',
        'groups' => [
            'Senior Vice Presidents' => [
                ['name' => 'Noel B. Leonor', 'title' => 'Office of the Chairman, ACI Chief of Staff'],
                ['name' => 'Antonio T. Mardo, CSM', 'title' => 'Operations'],
                ['name' => 'John Peter Chick B. Castelo', 'title' => 'Treasurer/Business Development'],
                ['name' => 'Janet Ruth Solsoloy', 'title' => 'Human Resources'],
                ['name' => 'Lorna D. Fabian', 'title' => 'Leasing'],
            ],
            'Vice Presidents' => [
                ['name' => 'Morriel A. Abogado, CSM', 'title' => 'Operations'],
                ['name' => 'Atty. Philip U. Ang', 'title' => 'Legal Affairs'],
                ['name' => 'Erwin Dennis P. Lacson', 'title' => 'Supply Chain Management'],
                ['name' => 'Nelon N. Medina', 'title' => 'Controllership'],
                ['name' => 'Rolan D. Campos II', 'title' => 'Treasury'],
                ['name' => 'Marjorie C. Go', 'title' => 'Marketing'],
                ['name' => 'Jorge A. Fores', 'title' => ''],
                ['name' => 'Jose Amado A. Fores', 'title' => ''],
            ],
            'Assistant Vice Presidents' => [
                ['name' => 'Marie Anthonnette M. Bunag', 'title' => 'Leasing'],
                ['name' => 'Diane C. Romero', 'title' => 'JAAF'],
                ['name' => 'Madel Parocha Cervantes', 'title' => 'Digital Marketing'],
                ['name' => 'Kristine L. Reyes', 'title' => 'Construction'],
                ['name' => 'Erneil Nanola', 'title' => 'Business Development'],
                ['name' => 'Mike Canlas', 'title' => 'IT'],
                ['name' => 'AJ Dela Fuente', 'title' => 'Organization Development'],
            ],
            'Consultants' => [
                ['name' => 'Teofilo R. Asuncion Jr.', 'title' => 'Organization Development'],
                ['name' => 'J. Rowell L. Recinto', 'title' => 'General Management'],
                ['name' => 'Juliana I. Castro', 'title' => 'Design'],
                ['name' => 'Jose B. Rasalan', 'title' => 'Audit'],
                ['name' => 'Ma. Teresita Gonzaga', 'title' => 'Archives'],
                ['name' => 'Francis Mercado', 'title' => 'Architecture & Engineering'],
            ],
        ],
    ],
    [
        'tab' => 'Uniprom, Inc.',
        'tag' => 'Leisure &amp; Entertainment',
        'name' => 'Uniprom, Inc. Management Team',
        'groups' => [
            'Leadership' => [
                ['name' => 'Irene L. Jose', 'title' => 'COO'],
                ['name' => 'Veana A. Fores', 'title' => 'Vice President'],
            ],
        ],
    ],
    [
        'tab' => 'Progressive Dev. Corp.',
        'tag' => 'Investments &amp; Assets',
        'name' => 'Progressive Development Corporation',
        'groups' => [
            'Leadership' => [
                ['name' => 'Mar A. Roxas', 'title' => 'President'],
            ],
            'Officers' => [
                ['name' => 'Jay Enriquez', 'title' => 'AVP, Controllership'],
                ['name' => 'Desiderio A. Fuerte', 'title' => 'Luzon Operations'],
                ['name' => 'Cipriano E. Querol Jr.', 'title' => 'Security'],
                ['name' => 'Felix Manuel T. Trebol', 'title' => 'AG Visayas Operations'],
            ],
        ],
    ],
    [
        'tab' => 'Araneta Hotels',
        'tag' => 'Hospitality',
        'name' => 'Araneta Hotels, Inc. Management Team',
        'groups' => [
            'Leadership' => [
                ['name' => 'Maria Garcia', 'title' => 'General Manager'],
                ['name' => 'Joy De Mesa', 'title' => 'Hotel Manager, Novotel Manila Araneta City'],
                ['name' => 'Therese Galindo', 'title' => 'Cluster Head, Guest Experience, Sustainability & Safety'],
            ],
        ],
    ],
    [
        'tab' => 'Philippine Pizza',
        'tag' => 'Food Service',
        'name' => 'Philippine Pizza Holdings, Inc. Management Team',
        'groups' => [
            'Leadership' => [
                ['name' => 'Liza Leah Juinio', 'title' => 'COO'],
            ],
            'Senior Vice Presidents' => [
                ['name' => 'Maria Rosalita M. Herrera', 'title' => 'Human Resources'],
                ['name' => 'Rafael Alfonso P. Blardony', 'title' => 'Business Development'],
            ],
            'Vice Presidents' => [
                ['name' => 'Don Eric A. Romarate', 'title' => 'VP, Taco Bell & Dairy Queen'],
                ['name' => 'Frank F. Gonzales', 'title' => 'VP for Table Service, PPHI'],
                ['name' => 'Erryl John E. Ramos', 'title' => 'VP for Home Service, PPHI'],
                ['name' => 'Rommel R. Blanco', 'title' => 'Supply Chain Management'],
            ],
            'Assistant Vice Presidents' => [
                ['name' => 'Raymund R. Nobleza', 'title' => 'Marketing'],
                ['name' => 'Analiza Santiago', 'title' => 'Finance'],
                ['name' => 'Ralph Marasigan', 'title' => 'Corporate Planning'],
            ],
            'Consultants' => [
                ['name' => 'Grace Fornier Magno', 'title' => 'Marketing'],
                ['name' => 'Nick David', 'title' => 'Business Development'],
                ['name' => 'Dennis Hipolito', 'title' => 'Food Innovation'],
            ],
        ],
    ],
];
*/
?>

<style>
    .mg-page {
        background: #fff;
        text-align: center;
        padding: 46px 0 70px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .mg-page * {
        text-align: center;
    }

    .mg-wrap {
        width: 92%;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* Header */
    .mg-eyebrow {
        display: inline-block;
        color: #6b2d7b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .mg-title {
        font-size: 40px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.5px;
        line-height: 1.05;
    }

    .mg-subtitle {
        font-size: 16px;
        color: #777;
        margin: 0 auto 24px;
        max-width: 560px;
        line-height: 1.5;
    }

    /* Quick-nav pills */
    .mg-nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-bottom: 40px;
    }

    .mg-nav-pill {
        border: 1px solid #e2e2e2;
        background: #fff;
        color: #555;
        font-size: 14px;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 30px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .mg-nav-pill:hover {
        border-color: #00afab;
        color: #00afab;
    }

    .mg-unit {
        scroll-margin-top: 90px;
    }

    /* Chairman node (root of the tree) */
    .mg-chair {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        background: linear-gradient(135deg, #2b1a4d 0%, #3a2560 100%);
        color: #fff;
        border-radius: 14px;
        padding: 20px 30px;
        z-index: 2;
        margin: 0 auto;
    }

    .mg-chair-name {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 2px;
        letter-spacing: 0.3px;
    }

    .mg-chair-title {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.72);
        margin: 0;
    }

    /* ===== Hierarchy tree ===== */
    .mg-tree {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .mg-trunk {
        width: 2px;
        height: 40px;
        background: #d9d4e0;
    }

    .mg-units {
        width: 100%;
    }

    .mg-unit {
        position: relative;
        padding-top: 34px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .mg-unit::before {
        content: "";
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 34px;
        background: #d9d4e0;
    }

    .mg-unit-head {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        background: #fff;
        border: 2px solid #00afab;
        border-radius: 12px;
        padding: 14px 26px;
        text-align: center;
        position: relative;
        z-index: 2;
        box-shadow: 0 4px 14px rgba(0, 175, 171, 0.12);
    }

    .mg-unit-head * {
        text-align: center;
    }

    .mg-unit-tag {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #00afab;
        margin: 0 0 3px;
    }

    .mg-unit-name {
        font-size: 18px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: -0.2px;
    }

    .mg-unit-branch {
        width: 2px;
        height: 30px;
        background: #d9d4e0;
        margin: 0 auto;
    }

    .mg-levels {
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 24px 24px 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        align-self: stretch;
    }

    .mg-level {
        margin-bottom: 24px;
    }

    /* Section title — flanked-divider style instead of a solid mint bar */
    .mg-level-label {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 12.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.3px;
        color: #00afab;
        margin: 0 0 22px;
    }

    .mg-level-label::before,
    .mg-level-label::after {
        content: "";
        flex: 1 1 auto;
        height: 1px;
        background: linear-gradient(to right, transparent, #b9e6e4);
    }

    .mg-level-label::after {
        background: linear-gradient(to left, transparent, #b9e6e4);
    }

    .mg-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(0, calc(33.333% - 10px)));
        justify-content: center;
        gap: 14px;
    }

    /* Modern person card — soft shadow, monogram avatar, sentence case */
    .mg-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        background: #fff;
        border: 1px solid #f1efe9;
        border-top: 2px solid #00afab;
        border-radius: 4px 4px 16px 16px;
        padding: 26px 22px 24px;
        box-shadow: 0 1px 3px rgba(20, 15, 10, 0.05);
        transition: box-shadow 0.25s ease, transform 0.25s ease, border-color 0.25s ease;
    }

    .mg-card:hover {
        box-shadow: 0 16px 32px rgba(20, 15, 10, 0.09);
        transform: translateY(-3px);
        border-color: transparent;
    }

    .mg-card-name {
        font-size: 14.5px;
        font-weight: 700;
        letter-spacing: 0.1px;
        color: #1a1a1a;
        margin: 0 0 4px;
        line-height: 1.3;
    }

    .mg-card-title {
        font-size: 12.5px;
        color: #8a8290;
        margin: 0;
        line-height: 1.3;
    }

    /* CTA */
    .mg-cta {
        border-top: 1px solid #eee;
        margin-top: 44px;
        padding-top: 40px;
        text-align: center;
    }

    .mg-cta-title {
        font-size: 22px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
    }

    .mg-cta-text {
        font-size: 15px;
        line-height: 1.6;
        color: #888;
        margin: 0 auto 18px;
        max-width: 520px;
    }

    .mg-cta-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
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

    .mg-cta-btn:hover {
        background: #582465;
        color: #fff;
    }

    @media (max-width: 900px) {
        .mg-title {
            font-size: 32px;
        }

        .mg-cards {
            grid-template-columns: repeat(auto-fit, minmax(0, calc(50% - 7px)));
        }
    }

    @media (max-width: 600px) {
        .mg-cards {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .mg-tab {
            font-size: 13px;
            padding: 8px 16px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="mg-page">
            <div class="mg-wrap">
                <!-- Header -->
                <span class="mg-eyebrow">The Araneta Group</span>
                <h1 class="mg-title">Management</h1>
                <p class="mg-subtitle">The organizational structure of the Group's five strategic business units.</p>

                <!-- Quick nav: jump to a specific business unit -->
                <div class="mg-nav">
                    <?php foreach ($units as $i => $unit): ?>
                        <a href="#mg-unit-<?= $i ?>" class="mg-nav-pill"><?= $unit['tab'] ?></a>
                    <?php endforeach; ?>
                </div>

                <!-- Hierarchy tree -->
                <div class="mg-tree">
                    <!-- Chairman (root node) -->
                    <div class="mg-chair">
                        <div>
                            <p class="mg-chair-name"><?= $chairman['name'] ?></p>
                            <p class="mg-chair-title"><?= $chairman['title'] ?></p>
                        </div>
                    </div>

                    <!-- Trunk down to the units -->
                    <div class="mg-trunk"></div>

                    <!-- Business units branching off -->
                    <div class="mg-units">
                        <?php foreach ($units as $i => $unit): ?>
                            <div class="mg-unit" id="mg-unit-<?= $i ?>">
                                <!-- Unit head node -->
                                <div class="mg-unit-head">
                                    <p class="mg-unit-tag"><?= $unit['tag'] ?></p>
                                    <p class="mg-unit-name"><?= $unit['name'] ?></p>
                                </div>

                                <!-- Branch down into the levels -->
                                <div class="mg-unit-branch"></div>

                                <!-- Levels of the unit -->
                                <div class="mg-levels">
                                    <?php foreach ($unit['groups'] as $groupLabel => $members): ?>
                                        <div class="mg-level">
                                            <p class="mg-level-label"><?= h($groupLabel) ?></p>
                                            <div class="mg-cards">
                                                <?php foreach ($members as $member): ?>
                                                    <div class="mg-card">
                                                        <div>
                                                            <p class="mg-card-name"><?= $member['name'] ?></p>
                                                            <?php if (!empty($member['title'])): ?>
                                                                <p class="mg-card-title"><?= $member['title'] ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- CTA -->
                <div class="mg-cta">
                    <h2 class="mg-cta-title">About the Araneta Group</h2>
                    <p class="mg-cta-text">A progressive and diversified group of companies anchored on property, food
                        service, leisure, and hospitality.</p>
                    <a href="/about" class="mg-cta-btn">Learn More &rarr;</a>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>