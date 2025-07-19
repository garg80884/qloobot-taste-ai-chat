<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'];

$gemini_key = 'Your Gemini API Key';
$qloo_key = 'YOUR_QLOO_API_KEY';

// Get Qloo Taste Data
function getTasteContext($query, $qloo_key) {
  $qlooUrl = "https://api.qloo.com/v1/search";
  $payload = json_encode(["query" => $query]);
  $headers = [
    "Authorization: Bearer $qloo_key",
    "Content-Type: application/json"
  ];

  $ch = curl_init($qlooUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  $response = curl_exec($ch);
  curl_close($ch);

  return json_decode($response, true);
}

// Ask Gemini API
function askGemini($userMessage, $tasteData, $gemini_key) {
  $tasteSummary = json_encode($tasteData, JSON_PRETTY_PRINT);

  $finalPrompt = "User Query: $userMessage\n\nQloo Taste Insights:\n$tasteSummary\n\nReply as Qloobot, a smart and fun cultural assistant who gives creative suggestions.";

  $postData = json_encode([
    "contents" => [
      ["parts" => [["text" => $finalPrompt]]]
    ]
  ]);

  $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$gemini_key");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  $json = json_decode($response, true);
  return $json['candidates'][0]['content']['parts'][0]['text'];
}

// Run logic
$tasteInfo = getTasteContext($userMessage, $qloo_key);
$geminiResponse = askGemini($userMessage, $tasteInfo, $gemini_key);

echo json_encode(["reply" => $geminiResponse]);
