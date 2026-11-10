<?php
/**
 * CareerAI - OTP (One-Time Password) Helpers
 *
 * Generates, stores, and emails 6-digit verification codes.
 * Used by registration, resend, and password reset.
 *
 * Security features:
 *  - Cryptographically secure random generation (random_int)
 *  - Attempt limiting to prevent brute-force guessing
 *  - Short-lived codes (10 minutes by default)
 */

require_once __DIR__ . '/../config/mailer.php';

// How long an OTP stays valid (in minutes)
define('OTP_VALID_MINUTES', 10);

// Maximum number of incorrect attempts before the code is invalidated
define('OTP_MAX_ATTEMPTS', 5);

/**
 * Generate a cryptographically secure 6-digit OTP.
 *
 * @return string  Example: "042917"
 */
function generate_otp()
{
    // random_int is cryptographically secure (unlike rand / mt_rand)
    $number = random_int(0, 999999);

    // Pad with leading zeros so it is always 6 digits
    return str_pad((string)$number, 6, '0', STR_PAD_LEFT);
}

/**
 * Save the OTP and its expiry for a given user.
 * Resets the attempt counter (so a fresh code starts at 0 attempts).
 *
 * @param mysqli $conn
 * @param int    $userId
 * @param string $otp
 * @return bool
 */
function save_otp($conn, $userId, $otp)
{
    $expiresAt = date('Y-m-d H:i:s', time() + (OTP_VALID_MINUTES * 60));

    // Reset attempts whenever a new code is issued
    $stmt = $conn->prepare(
        'UPDATE users
         SET otp_code = ?, otp_expires_at = ?, otp_attempts = 0
         WHERE id = ? LIMIT 1'
    );
    $stmt->bind_param('ssi', $otp, $expiresAt, $userId);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

/**
 * Send the OTP email to a user.
 *
 * @param string $toEmail
 * @param string $toName
 * @param string $otp
 * @return true|string  true on success, error message on failure
 */
function send_otp_email($toEmail, $toName, $otp)
{
    $subject = 'Your CareerAI verification code';

    $safeName = htmlspecialchars($toName ?: 'there', ENT_QUOTES, 'UTF-8');

    $body = '
        <div style="font-family:Arial,Helvetica,sans-serif;color:#263B53;max-width:520px;margin:0 auto;padding:24px;border:1px solid #DCE7F2;border-radius:10px;">
            <h2 style="color:#0B2545;margin-top:0;">Verify your CareerAI account</h2>
            <p>Hi ' . $safeName . ',</p>
            <p>Use the code below to verify your email address. It expires in '
            . OTP_VALID_MINUTES . ' minutes.</p>

            <p style="font-size:32px;font-weight:bold;letter-spacing:6px;color:#1769E0;text-align:center;margin:24px 0;">'
            . $otp . '</p>

            <p>If you did not create a CareerAI account, you can safely ignore this email.</p>
            <p style="color:#6C7D90;font-size:13px;margin-bottom:0;">&copy; ' . date('Y') . ' CareerAI</p>
        </div>
    ';

    return send_mail($toEmail, $toName, $subject, $body);
}