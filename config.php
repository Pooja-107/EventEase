<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
$db = new mysqli('localhost', 'root', '', 'eventplanner');   // XAMPP default login
if ($db->connect_error) die('Database error: ' . $db->connect_error);
$db->set_charset('utf8mb4');
const TEAM = 'Made by:  Pooja (240911296) and B Rashmi Kini (240953568)';   // <-- edit this
const ADMIN_EMAIL = 'admin@events.com';   // signing up with this email makes an admin
function me() { return $_SESSION['user'] ?? null; }
function need_login() { if (!me()) { header('Location: login.php'); exit; } }
function customer_only() { need_login(); if (me()['role'] == 'admin') { header('Location: admin.php'); exit; } }
function h($s) { return htmlspecialchars((string)$s); }
function top($t) {
  $u = me();
  echo "<!DOCTYPE html><html><head><meta charset=utf-8><meta name=viewport content='width=device-width,initial-scale=1'><title>" . h($t) . " | EventEase</title><link rel=stylesheet href=style.css></head><body><nav><b>EventEase</b><span>";
  if ($u && $u['role'] == 'admin') echo "<a href=admin.php#bookings>Bookings</a><a href=admin.php#options>Manage options</a>";
  elseif ($u) echo "<a href=index.php>Plan event</a><a href=bookings.php>My bookings</a>";
  if ($u) echo "<a href=logout.php>Log out (" . h($u['name']) . ")</a>";
  echo "</span></nav><main>";
}
function bottom($js = false) {
  echo "</main><footer>" . h(TEAM) . "</footer>";
  if ($js) echo "<script src=app.js></script>";
  echo "</body></html>";
}
