<?php
/**
 * Admin notification email — Contact Us form submission
 *
 * @var \App\View\AppView $this
 * @var string|null $firstName
 * @var string|null $middleName
 * @var string|null $lastName
 * @var string|null $mobileNumber
 * @var string|null $targetEmail
 * @var string|null $targetSubject
 * @var string|null $message
 * @var string $submittedAt
 */

$fullName = trim(($firstName ?? '') . ' ' . ($middleName ?? '') . ' ' . ($lastName ?? ''));
$fullName = preg_replace('/\s+/', ' ', $fullName);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
</head>
<body style="margin:0; padding:0; background:#f4f4f4; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4; padding:30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #eee;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#00afab; padding:24px 30px;">
                            <h1 style="margin:0; font-size:18px; color:#ffffff;">New Contact Us Inquiry</h1>
                            <p style="margin:4px 0 0; font-size:13px; color:#e0f7f6;">Araneta City Website</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#333;">
                                <tr>
                                    <td style="padding:8px 0; width:140px; color:#888; vertical-align:top;">Name</td>
                                    <td style="padding:8px 0; font-weight:bold;"><?= h($fullName ?: 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#888; vertical-align:top;">Mobile Number</td>
                                    <td style="padding:8px 0;"><?= h($mobileNumber ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#888; vertical-align:top;">Email</td>
                                    <td style="padding:8px 0;">
                                        <a href="mailto:<?= h($targetEmail ?? '') ?>" style="color:#00afab; text-decoration:none;">
                                            <?= h($targetEmail ?? 'N/A') ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#888; vertical-align:top;">Subject</td>
                                    <td style="padding:8px 0;"><?= h($targetSubject ?? 'N/A') ?></td>
                                </tr>
                            </table>

                            <div style="margin-top:20px; padding-top:16px; border-top:1px solid #eee;">
                                <p style="margin:0 0 8px; font-size:13px; font-weight:bold; color:#888; text-transform:uppercase; letter-spacing:0.5px;">Message</p>
                                <p style="margin:0; font-size:14px; color:#333; line-height:1.6; white-space:pre-wrap;"><?= h($message ?? '') ?></p>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:16px 30px; background:#fafafa; border-top:1px solid #eee;">
                            <p style="margin:0; font-size:11px; color:#999;">
                                Submitted on <?= h($submittedAt) ?>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>