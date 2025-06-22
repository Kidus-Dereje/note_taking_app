<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$notesFile = __DIR__ . '/notes.json';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function getNotes($notesFile) {
    if (!file_exists($notesFile)) return [];
    $json = file_get_contents($notesFile);
    return json_decode($json, true) ?: [];
}

function saveNotes($notesFile, $notes) {
    file_put_contents($notesFile, json_encode($notes, JSON_PRETTY_PRINT));
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode(getNotes($notesFile));
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $notes = getNotes($notesFile);
        $note = [
            'id' => uniqid(),
            'text' => $data['text'] ?? ''
        ];
        $notes[] = $note;
        saveNotes($notesFile, $notes);
        echo json_encode($note);
        break;
    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $notes = getNotes($notesFile);
        $notes = array_filter($notes, function($n) use ($data) {
            return $n['id'] !== $data['id'];
        });
        saveNotes($notesFile, array_values($notes));
        echo json_encode(['success' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
