<?php
/**
 * Lahore Dental Clinic — Appointment Form Handler
 * Core PHP, no frameworks. Validates input, then sends an email
 * to the clinic using PHP's built-in mail() function.
 *
 * IMPORTANT SETUP NOTES (read before going live):
 * 1. Replace CLINIC_EMAIL below with the real inbox that should
 *    receive appointment requests.
 * 2. PHP's mail() depends on the server having a working mail
 *    transport (sendmail/postfix) configured. Most shared hosts
 *    (e.g. cPanel/Hostinger) support this out of the box. On
 *    localhost/XAMPP, mail() will NOT work without extra setup —
 *    test on your actual hosting server.
 * 3. If mail() proves unreliable on your host, swap it for
 *    PHPMailer with SMTP credentials — the validation and JSON
 *    response logic below can stay exactly the same.
 */

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------
// CONFIG — update this before deploying
// ---------------------------------------------------------------
const CLINIC_EMAIL   = 'subhanaziz406@gmail.com'; // <-- replace with real client email
const CLINIC_NAME     = 'Lahore Dental Clinic';
const SITE_NAME_FOR_SUBJECT = 'Lahore Dental Clinic Website';

// ---------------------------------------------------------------
// Helper: send a JSON response and stop execution
// ---------------------------------------------------------------
function respond(bool $success, string $message): void
{
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, 'Invalid request method.');
}

// ---------------------------------------------------------------
// Collect + sanitize input
// ---------------------------------------------------------------
function clean(string $value): string
{
    // Strip tags and trim; also strip line breaks to prevent header injection
    // if a field is ever reused in an email header.
    $value = trim(strip_tags($value));
    $value = str_replace(["\r", "\n"], ' ', $value);
    return $value;
}

$fullName       = clean($_POST['fullName'] ?? '');
$phone          = clean($_POST['phone'] ?? '');
$email          = clean($_POST['email'] ?? '');
$service        = clean($_POST['service'] ?? '');
$preferredDate  = clean($_POST['preferredDate'] ?? '');
$preferredTime  = clean($_POST['preferredTime'] ?? '');
$message        = trim(strip_tags($_POST['message'] ?? '')); // message may contain line breaks, that's fine in the body

// ---------------------------------------------------------------
// Validate
// ---------------------------------------------------------------
$errors = [];

if ($fullName === '' || mb_strlen($fullName) < 2) {
    $errors[] = 'Please enter your full name.';
}

if ($phone === '' || !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
    $errors[] = 'Please enter a valid phone number.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if ($service === '') {
    $errors[] = 'Please choose a service.';
}

if ($preferredDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $preferredDate)) {
    $errors[] = 'Please choose a preferred date.';
}

if ($preferredTime === '') {
    $errors[] = 'Please choose a preferred time slot.';
}

if (!empty($errors)) {
    respond(false, implode(' ', $errors));
}

// ---------------------------------------------------------------
// Build the email
// ---------------------------------------------------------------
$subject = "New Appointment Request — {$fullName}";

$body  = "You have a new appointment request from the {$_SERVER['HTTP_HOST']} website:\n\n";
$body .= "Name:            {$fullName}\n";
$body .= "Phone:           {$phone}\n";
$body .= "Email:           {$email}\n";
$body .= "Service:         {$service}\n";
$body .= "Preferred Date:  {$preferredDate}\n";
$body .= "Preferred Time:  {$preferredTime}\n";
$body .= "Message:\n" . ($message !== '' ? $message : '(none provided)') . "\n\n";
$body .= "---\n";
$body .= "Sent automatically from the " . SITE_NAME_FOR_SUBJECT . " appointment form.\n";

// Headers: From must be a domain address on most hosts (not the visitor's
// own address) to avoid being marked as spam. Reply-To is set to the
// visitor's email so you can hit "Reply" directly.
$fromAddress = 'noreply@' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'localhost');

$headers   = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=utf-8';
$headers[] = "From: {$fromAddress}";
$headers[] = "Reply-To: {$email}";
$headers[] = 'X-Mailer: PHP/' . phpversion();

$headersString = implode("\r\n", $headers);

// ---------------------------------------------------------------
// Send
// ---------------------------------------------------------------
$sent = @mail(CLINIC_EMAIL, $subject, $body, $headersString);

if ($sent) {
    respond(true, 'Thanks, ' . $fullName . '! Your appointment request has been sent — our front desk will contact you shortly to confirm.');
} else {
    respond(false, 'Sorry, your request could not be sent right now. Please call us directly at +92 309 2499737.');
}
