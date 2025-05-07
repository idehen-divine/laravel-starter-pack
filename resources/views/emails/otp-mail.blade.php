<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarterPack - OTP Verification</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">
    <table cellpadding="0" cellspacing="0" width="100%" align="center" style="background-color: #fff; width: 100%;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; width: 100%; border-collapse: collapse;">
                    <!-- Header -->
                    <tr>
                        <td align="center" bgcolor="#2DBB54" style="padding: 20px 0;">
                            <h1 style="font-size: 24px; margin: 0; color: #FFFFFF;">StarterPack</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td bgcolor="#FFFFFF" style="padding: 40px 30px;">
                            <p style="font-size: 16px; margin-top: 20px; color: #333333;">
                                Dear {{ $user_name }},
                            </p>
                            <p style="font-size: 16px; color: #333333;">
                                Your verification code for StarterPack is:
                            </p>
                            <div style="font-size: 24px; font-weight: bold; text-align: center; padding: 10px 0; color: #2DBB54;">
                                {{ $otp }}
                            </div>
                            <p style="font-size: 16px; color: #333333;">
                                Please enter this code to complete your verification. This code is valid for a limited time and should not be shared with anyone.
                            </p>
                            <p style="font-size: 16px; color: #333333;">
                                If you did not request this verification, please ignore this email or contact our support team at 
                                <a href="mailto:support@example.com">support@example.com</a>.
                            </p>
                            <p style="font-size: 16px; margin-top: 20px; color: #333333;">
                                Best Regards,
                            </p>
                            <p style="font-size: 16px; color: #333333; margin-top: -13px;">
                                StarterPack Team
                            </p>
                            <p style="font-size: 16px; color: #333333; margin-top: -13px;">
                                <a href="mailto:support@example.com">support@example.com</a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td bgcolor="#2DBB54" align="center" style="padding: 20px 0;">
                            <p style="font-size: 14px; color: #FFFFFF;">
                                Need help? Contact our support team at 
                                <a href="mailto:support@example.com" style="color: #FFFFFF; text-decoration: none;">support@example.com</a>.
                            </p>
                            <p style="font-size: 14px; color: #FFFFFF;">&copy; {{ date('Y') }} StarterPack. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
