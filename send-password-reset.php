<?php

$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes

$mysqli = require __DIR__ . "/config.php";

$sql = "UPDATE customer
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);

$stmt->execute();

if ($mysqli->affected_rows) {

    $mail = require __DIR__ . "/mailer.php";

    if ($mail instanceof PHPMailer\PHPMailer\PHPMailer) {

        $mail->setFrom("Jackkee3123@gmail.com", 'Password Reset');
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";

        $resetLink = "http://localhost/FYP(11)/FYP/reset-password.php?token=" . $token;
        
        $mail->isHTML(true); // Ensure HTML format
        $mail->Body = 'Click <a href="' . $resetLink . '">here</a> to reset your password.';
        $mail->AltBody = 'Copy and paste this link to reset your password: ' . $resetLink;

        try {
            $mail->send();
            echo "If the email exists, a password reset link has been sent.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Mailer setup failed.";
    }

} else {
    echo "If the email exists, a password reset link has been sent.";
}
?>
