<?php
/**
 * Password reset email.
 *
 * @var \App\View\AppView $this
 * @var string $firstName
 * @var string $resetUrl
 * @var string $expiresText
 */
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
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #eee;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#00afab; padding:24px 30px;">
                            <img src="<?= $this->Url->image('aci-white.png', ['fullBase' => true]) ?>"
                                alt="Araneta City" style="height:50px; width:auto; display:block;">
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px 30px; color:#333; font-size:15px; line-height:1.6;">
                            <h2 style="margin:0 0 16px; font-size:22px; color:#1a1a1a;">Reset your password</h2>

                            <p style="margin:0 0 16px;">Hi <?= h($firstName) ?>,</p>

                            <p style="margin:0 0 16px;">
                                We received a request to reset the password for your Araneta City CMS account.
                                Click the button below to choose a new password.
                            </p>

                            <p style="margin:0 0 28px;" align="center">
                                <a href="<?= h($resetUrl) ?>"
                                    style="display:inline-block; background:#00afab; color:#ffffff; text-decoration:none;
                                           font-weight:bold; font-size:15px; padding:14px 32px; border-radius:30px;">
                                    Reset Password
                                </a>
                            </p>

                            <p style="margin:0 0 16px; color:#777; font-size:13px;">
                                This link will expire in <?= h($expiresText) ?>. If the button doesn't work, copy and
                                paste this link into your browser:
                            </p>
                            <p style="margin:0 0 24px; word-break:break-all; font-size:13px;">
                                <a href="<?= h($resetUrl) ?>" style="color:#00afab;"><?= h($resetUrl) ?></a>
                            </p>

                            <p style="margin:0; color:#777; font-size:13px;">
                                If you didn't request a password reset, you can safely ignore this email &mdash; your
                                password will remain unchanged.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f7f7f7; padding:18px 30px; color:#999; font-size:12px; text-align:center;">
                            &copy; <?= date('Y') ?> Araneta City. This is an automated message, please do not reply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
