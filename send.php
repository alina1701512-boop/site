<?php
// Заголовки для CORS (чтобы форма отправлялась с GitHub Pages)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Если это preflight-запрос OPTIONS — сразу отвечаем
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Принимаем только POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Токен и Chat ID (теперь они спрятаны на сервере!)
$BOT_TOKEN = '8624063695:AAHF7dbrUssL66aCX6KXxSxgEhbDjtT2hNQ';
$CHAT_ID = '342298611';

// Получаем данные из формы
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'No data']);
    exit;
}

// Формируем сообщение
$message = "📝 Новая заявка\n\n";
$message .= "🎁 Форма: " . ($data['formName'] ?? 'Не указана') . "\n";
$message .= "🔘 Кнопка: " . ($data['buttonText'] ?? 'Не указана') . "\n\n";
if (!empty($data['name'])) $message .= "👤 Имя: " . $data['name'] . "\n";
if (!empty($data['email'])) $message .= "📧 Email: " . $data['email'] . "\n";
if (!empty($data['phone'])) $message .= "📞 Телефон: " . $data['phone'] . "\n";
if (!empty($data['telegram'])) $message .= "💬 Telegram: " . $data['telegram'] . "\n";
if (!empty($data['message'])) $message .= "📝 ТЗ/пожелания: " . $data['message'] . "\n";
$message .= "\n⏰ " . ($data['time'] ?? '') . "\n";
$message .= "🔗 Страница: " . ($data['pageUrl'] ?? '') . "\n";
$message .= "🖥️ " . ($data['deviceInfo'] ?? '') . "\n";
$message .= "⏱️ На сайте: " . ($data['timeOnSite'] ?? '') . "\n";

// Отправляем в Telegram
$url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
$postData = [
    'chat_id' => $CHAT_ID,
    'text' => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Telegram API error', 'details' => $response]);
}
?>
