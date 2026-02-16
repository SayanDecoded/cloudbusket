<?php
date_default_timezone_set('Asia/Kolkata'); 
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact-us.html");
    exit;
}

/* ======================
   HONEYPOT
====================== */
if (!empty($_POST["cb_token"] ?? "")) {
    exit;
}

/* ======================
   SANITIZE
====================== */
$first_name = trim($_POST["first_name"] ?? "");
$last_name  = trim($_POST["last_name"] ?? "");
$email      = trim($_POST["email"] ?? "");
$phone      = trim($_POST["phone"] ?? "");
$company    = trim($_POST["company"] ?? "");
$message    = trim($_POST["message"] ?? "");

/* ======================
   VALIDATION
====================== */
if (
    $first_name === "" ||
    $last_name === "" ||
    $email === "" ||
    $phone === "" ||
    $message === ""
) {
    die("Please complete all required fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

/* ======================
   SAVE TO CSV
====================== */
$csvDir  = dirname(__DIR__) . '/data';
$csvFile = $csvDir . '/contact.csv';

// Create directory if missing
if (!is_dir($csvDir)) {
    mkdir($csvDir, 0755, true);
}

// Check if file exists
$fileExists = file_exists($csvFile);

// Open file
$fp = fopen($csvFile, 'a');

if (!$fp) {
    die("Unable to save data.");
}

flock($fp, LOCK_EX);

//add header if new file
if (!$fileExists) {
    fputcsv($fp, [
        'Date',
        'First Name',
        'Last Name',
        'Email',
        'Phone',
        'Company',
        'Message'
    ]);
}

//add data row
fputcsv($fp, [
    date('Y-m-d H:i:s'),
    $first_name,
    $last_name,
    $email,
    $phone,
    $company,
    $message
]);

flock($fp, LOCK_UN);
fclose($fp);

/* ======================
   SUCCESS
====================== */
header("Location: ../pages/thank-you.html");
exit;
