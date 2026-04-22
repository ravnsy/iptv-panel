<?php
header('Content-Type: application/json; charset=utf-8');

// قاعدة البيانات في مجلد data لضمان صلاحيات الكتابة
$db = new SQLite3(__DIR__ . '/data/iptv.db');

$db->exec("CREATE TABLE IF NOT EXISTS codes (
    code TEXT PRIMARY KEY,
    max_users INTEGER DEFAULT 1,
    expiry_date TEXT,
    status TEXT DEFAULT 'active'
)");

$db->exec("CREATE TABLE IF NOT EXISTS servers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    url TEXT
)");

// سيرفرات تجريبية
$db->exec("INSERT OR IGNORE INTO servers (id, name, url) VALUES 
    (1, 'Marvel', 'http://marveltv.info:80/get.php?username=36889&password=147835&type=m3u_plus&output=ts'),
    (2, 'Arox', 'http://arox.cc:80/get.php?username=24335738729223&password=24335738729223&type=m3u_plus&output=ts')
");

// كود تجريبي
$db->exec("INSERT OR IGNORE INTO codes (code, max_users, expiry_date, status) VALUES 
    ('TEST123', 1, '2026-05-22', 'active')
");

$input = json_decode(file_get_contents('php://input'), true);
$code = $input['code'] ?? '';

$stmt = $db->prepare("SELECT * FROM codes WHERE code = :code AND status = 'active'");
$stmt->bindValue(':code', $code);
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$result) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid Code']));
}

if (strtotime($result['expiry_date']) < time()) {
    die(json_encode(['status' => 'error', 'message' => 'Code Expired']));
}

$servers = [];
$q = $db->query("SELECT name, url FROM servers");
while ($row = $q->fetchArray(SQLITE3_ASSOC)) {
    $servers[] = $row;
}

echo json_encode(['status' => 'success', 'servers' => $servers]);
?>
