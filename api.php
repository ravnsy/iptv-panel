<?php
header('Content-Type: application/json; charset=utf-8');
define('SECRET_KEY', 'ChangeThisSecretKey2026_xyz');
$db = new SQLite3('iptv.db');
$db->exec("CREATE TABLE IF NOT EXISTS codes (code TEXT PRIMARY KEY, max_users INTEGER, current_users INTEGER, expiry_date TEXT, status TEXT)");
$db->exec("CREATE TABLE IF NOT EXISTS servers (id INTEGER PRIMARY KEY, name TEXT, url TEXT)");

// إضافة سيرفر افتراضي للتجربة
$db->exec("INSERT OR IGNORE INTO servers (id, name, url) VALUES (1, 'Default Server', 'http://marveltv.info:80/get.php?username=36889&password=147835&type=m3u_plus&output=ts')");

$input = json_decode(file_get_contents('php://input'), true);
$code = $input['code'] ?? '';
$stmt = $db->prepare("SELECT * FROM codes WHERE code = :code AND status = 'active'");
$stmt->bindValue(':code', $code);
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$result) die(json_encode(['status' => 'error']));
$servers = [];
$q = $db->query("SELECT name, url FROM servers");
while ($row = $q->fetchArray(SQLITE3_ASSOC)) $servers[] = $row;
echo json_encode(['status' => 'success', 'servers' => $servers]);
?>
