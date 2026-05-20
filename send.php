<?php
header('Content-Type: application/json; charset=utf-8');

$to = "info@vitek3dprint.cz";
$subject = "Nová poptávka z webu Vitek3DPrint";

$name = trim($_POST["Jméno"] ?? "");
$email = trim($_POST["E-mail"] ?? "");
$type = trim($_POST["Typ zakázky"] ?? "");
$message = trim($_POST["Popis zakázky"] ?? "");

if ($email === "" || $message === "") {
    echo json_encode([
        "success" => false,
        "message" => "Vyplňte prosím e-mail a popis zakázky."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Zadejte prosím platný e-mail."
    ]);
    exit;
}

$body =
"Nová poptávka z webu Vitek3DPrint\n\n" .
"Jméno: " . $name . "\n" .
"E-mail: " . $email . "\n" .
"Typ zakázky: " . $type . "\n\n" .
"Popis zakázky:\n" . $message . "\n";

$headers = [];
$headers[] = "From: Vitek3DPrint <info@vitek3dprint.cz>";
$headers[] = "Reply-To: " . $email;
$headers[] = "Content-Type: text/plain; charset=UTF-8";

// Jednoduchá verze bez přílohy je nejspolehlivější.
// Pokud zákazník přiloží soubor, upozorníme v e-mailu, aby ho případně poslal následně.
if (isset($_FILES["Příloha"]) && $_FILES["Příloha"]["error"] === UPLOAD_ERR_OK) {
    $body .= "\nPoznámka: Zákazník vybral přílohu: " . $_FILES["Příloha"]["name"] . "\n";
    $body .= "Pokud příloha nedorazí, požádejte zákazníka o zaslání souboru odpovědí na e-mail.\n";
}

$sent = mail($to, $subject, $body, implode("\r\n", $headers));

if ($sent) {
    echo json_encode([
        "success" => true,
        "message" => "Poptávka byla odeslána."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Odeslání se nepovedlo. Hosting pravděpodobně blokuje funkci mail()."
    ]);
}
