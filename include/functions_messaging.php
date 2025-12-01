<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// SMTP Variables
function smtp_host($dbh) {
	return $data = 'mail.siitpartners.com';
}

function smtp_username($dbh) {
	return $data = "noreply@siitpartners.com";
}

function smtp_password($dbh) {
	return $data = "noreply@siit";
}

// Reset Password Email
function passwordResetEmail($dbh, $name, $username, $password) {
	$epage = file_get_contents("../template/emails/reset_password_admin.html", FILE_USE_INCLUDE_PATH);
	$epage1 = str_replace('$name', $name, $epage);
	$epage2 = str_replace('$username', $username, $epage1);
	$epage3 = str_replace('$password', $password, $epage2);
	return $epage3;
}

// Enrolment Email
function enrolmentEmail($dbh, $name) {
	$epage = file_get_contents("template/emails/enrolment.html", FILE_USE_INCLUDE_PATH);
	$epage1 = str_replace('$name', $name, $epage);
	return $epage1;
}

// Enrolment Email
function newUserEmail($dbh, $name, $username, $password) {
	$epage = file_get_contents("template/emails/new_user.html", FILE_USE_INCLUDE_PATH);
	$epage1 = str_replace('$name', $name, $epage);
	$epage2 = str_replace('$username', $username, $epage1);
	$epage3 = str_replace('$password', $password, $epage2);
	return $epage3;
}

// Send Mail function (Root folder= staff)
function sendMail($dbh, $email, $subject, $html_file, $linkAttachments= [], $stringAttachements= []) {
    require_once("template/plugins/PHPMailer/Exception.php");
    require_once("template/plugins/PHPMailer/PHPMailer.php");
    require_once("template/plugins/PHPMailer/SMTP.php");

    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    // Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                   // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = smtp_host($dbh);                        // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = smtp_username($dbh);                    // SMTP username
    $mail->Password   = smtp_password($dbh);                    // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom(smtp_username($dbh), profile_name($dbh));
    $mail->addAddress($email);     // Add a recipient
    // $mail->addAddress('info@example.com');                   // Optional
    // $mail->addReplyTo('info@example.com', 'Information');	// Optional
    // $mail->addCC('cc@example.com');							// Optional
    // $mail->addBCC('bcc@example.com');						// Optional

    // Attachments
	if($linkAttachments) {
		foreach($attachments as $attachment) {
			$mail->addAttachment($attachment);
		}
	}

    if($stringAttachements) {
		foreach($stringAttachements as $string) {
			$mail->addStringAttachment($string, "Attachement.pdf", $encoding= "base64", $type= "application/pdf");
		}
	}

    // $mail->addAttachment('/var/tmp/file.tar.gz');            // Add attachment (Optional)
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');       // Optional

    // Content
    $mail->isHTML(true);                                        // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $html_file;
    $mail->AltBody = $html_file;
    $mail->send();
}

// Send Mail function (Second level folder e.g staff/include)
function sendMail2($dbh, $email, $subject, $html_file, $linkAttachments= [], $stringAttachements= []) {
    require_once("../template/plugins/PHPMailer/Exception.php");
    require_once("../template/plugins/PHPMailer/PHPMailer.php");
    require_once("../template/plugins/PHPMailer/SMTP.php");

    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    // Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                   // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = smtp_host($dbh);                        // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = smtp_username($dbh);                    // SMTP username
    $mail->Password   = smtp_password($dbh);                    // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom(smtp_username($dbh), profile_name($dbh));
    $mail->addAddress($email);     // Add a recipient
    // $mail->addAddress('info@example.com');                   // Optional
    // $mail->addReplyTo('info@example.com', 'Information');	// Optional
    // $mail->addCC('cc@example.com');							// Optional
    // $mail->addBCC('bcc@example.com');						// Optional

    // Attachments
	if($linkAttachments) {
		foreach($attachments as $attachment) {
			$mail->addAttachment($attachment);
		}
	}

    if($stringAttachements) {
		foreach($stringAttachements as $string) {
			$mail->addStringAttachment($string, "Attachement.pdf", $encoding = 'base64', $type = 'application/pdf');
		}
	}

    // $mail->addAttachment('/var/tmp/file.tar.gz');            // Add attachment (Optional)
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');       // Optional

    // Content
    $mail->isHTML(true);                                        // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $html_file;
    $mail->AltBody = $html_file;
    $mail->send();
}
?>