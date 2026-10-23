<?php
// Xavfsizlik qoidalariga ko'ra, tokenlarni ochiq kodda qoldirmaslik va 
// .env faylida saqlash tavsiya etiladi. Hozircha oddiylik uchun shu yerda kiritildi.
define('API_TOKEN', '8772238227:AAHOtRwQ6BUovtZoyXkhkzPg9wOu2B8LB34');
define('API_URL', 'https://api.telegram.org/bot' . API_TOKEN . '/');

// Telegramdan kelgan JSON so'rovni qabul qilish
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Agar so'rov bo'sh bo'lsa, jarayonni to'xtatish
if (!$update) {
    exit;
}

// Xabarni va foydalanuvchi ma'lumotlarini ajratib olish
$message = $update['message'] ?? null;

if ($message) {
    $chatId = $message['chat']['id'];
    $text = $message['text'] ?? '';
    $firstName = $message['from']['first_name'] ?? 'Foydalanuvchi';

    // Faqat /start yoki salom so'zlariga javob qaytarish
    if ($text === '/start' || mb_strtolower($text) === 'salom') {
        $replyText = "Salom, <b>" . htmlspecialchars($firstName) . "</b>! Botimizga xush kelibsiz.";
        sendMessage($chatId, $replyText);
    }
}

/**
 * Telegram API orqali xabar yuborish funksiyasi
 * xavfsizlik va barqarorlik uchun cURL dan foydalanildi.
 */
function sendMessage($chatId, $text) {
    $url = API_URL . "sendMessage";
    $post_fields = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    // SSL xatoliklarni oldini olish uchun (serveringizga qarab o'zgartirishingiz mumkin)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
?>