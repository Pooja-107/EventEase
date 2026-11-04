<?php
require 'config.php';
$msg = '';
if (me()) { header('Location: ' . (me()['role'] == 'admin' ? 'admin.php' : 'index.php')); exit; }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $e = strtolower(trim($_POST['email'])); $p = $_POST['password'];
  if (isset($_POST['signup'])) {
    $n = trim($_POST['name']);
    if (!$n || !filter_var($e, FILTER_VALIDATE_EMAIL) || strlen($p) < 6) $msg = 'Enter your name, a valid email and a password of 6+ characters.';
    else {
      $role = $e == ADMIN_EMAIL ? 'admin' : 'user';
      $hash = password_hash($p, PASSWORD_DEFAULT);
      $st = $db->prepare('INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)');
      $st->bind_param('ssss', $n, $e, $hash, $role);
      $msg = $st->execute() ? 'Account created. Log in below.' : 'This email is already registered.';
    }
  } else {
    $st = $db->prepare('SELECT * FROM users WHERE email=?');
    $st->bind_param('s', $e); $st->execute();
    $u = $st->get_result()->fetch_assoc();
    if ($u && password_verify($p, $u['password'])) {
      unset($u['password']); $_SESSION['user'] = $u;
      header('Location: ' . ($u['role'] == 'admin' ? 'admin.php' : 'index.php')); exit;
    }
    $msg = 'Wrong email or password.';
  }
}
top('Log in');
?>
<section class="hero"><h1>Make every celebration special</h1><p>Pick decor, cakes, hosts, photography and catering, and watch your estimate update live.</p></section>
<p class="msg"><?= h($msg) ?></p>
<div class="two">
  <form method="post"><h2>Log in</h2>
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password" required>
    <button>Log in</button></form>
  <form method="post"><h2>Sign up</h2><input type="hidden" name="signup" value="1">
    <input name="name" placeholder="Your name" required>
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password (6+ characters)" required>
    <button>Create account</button></form>
</div>
<?php bottom(); ?>
