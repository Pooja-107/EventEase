<?php
require 'config.php';
header('Content-Type: application/json');
$a = $_GET['a'] ?? '';
function out($d) { echo json_encode($d); exit; }
function ids($s) { return array_values(array_filter(array_map('intval', explode(',', $s)))); }
function total($db, $list) {
  if (!$list) return 0;
  $r = $db->query('SELECT SUM(price) t FROM options WHERE id IN (' . implode(',', $list) . ')');
  return (int)$r->fetch_assoc()['t'];
}
if ($a == 'options') {
  $st = $db->prepare('SELECT * FROM options WHERE event_type=? ORDER BY category,id');
  $st->bind_param('s', $_GET['type']); $st->execute();
  out($st->get_result()->fetch_all(MYSQLI_ASSOC));
}
if ($a == 'bill') out(['total' => total($db, ids($_POST['ids'] ?? ''))]);
$u = me();
if (!$u) out(['error' => 'Please log in first.']);
if ($u['role'] == 'admin') out(['error' => 'Admins cannot book or save plans.']);
$uid = (int)$u['id'];
if ($a == 'save') {
  $list = implode(',', ids($_POST['ids'] ?? ''));
  $st = $db->prepare('REPLACE INTO plans VALUES(?,?,?)');
  $st->bind_param('iss', $uid, $_POST['type'], $list); $st->execute();
  out(['ok' => 'Plan saved.']);
}
if ($a == 'load') {
  $st = $db->prepare('SELECT event_type,option_ids FROM plans WHERE user_id=?');
  $st->bind_param('i', $uid); $st->execute();
  out($st->get_result()->fetch_assoc() ?: null);
}
if ($a == 'clear') {
  $st = $db->prepare('DELETE FROM plans WHERE user_id=?');
  $st->bind_param('i', $uid); $st->execute();
  out(['ok' => 'Plan cleared.']);
}
if ($a == 'book') {
  $list = ids($_POST['ids'] ?? '');
  $d = $_POST['date'] ?? ''; $v = trim($_POST['venue'] ?? ''); $p = $_POST['phone'] ?? ''; $g = (int)($_POST['guests'] ?? 0);
  if (!$list) out(['error' => 'Select at least one option.']);
  if (!$d || strtotime($d) < strtotime('today')) out(['error' => 'Pick a date from today onwards.']);
  if (strlen($v) < 3) out(['error' => 'Enter the venue.']);
  if (!preg_match('/^\d{10}$/', $p)) out(['error' => 'Phone must be 10 digits.']);
  if ($g < 1) out(['error' => 'Enter the number of guests.']);
  $t = total($db, $list); $s = implode(',', $list); $type = $_POST['type'];
  $st = $db->prepare('INSERT INTO bookings(user_id,event_type,event_date,venue,phone,guests,option_ids,total) VALUES(?,?,?,?,?,?,?,?)');
  $st->bind_param('issssisi', $uid, $type, $d, $v, $p, $g, $s, $t); $st->execute();
  out(['ok' => 'Booking request sent.']);
}
out(['error' => 'Unknown action']);
