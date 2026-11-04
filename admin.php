<?php
require 'config.php'; need_login();
if (me()['role'] != 'admin') die('Admins only.');
$note = '';
if (isset($_POST['add'])) {   // INSERT
  $price = (int)$_POST['price'];
  if ($price < 1) $note = 'Price must be above 0.';
  else {
    $st = $db->prepare('INSERT INTO options(event_type,category,name,details,price,image) VALUES(?,?,?,?,?,?)');
    $st->bind_param('ssssis', $_POST['event_type'], $_POST['category'], $_POST['name'], $_POST['details'], $price, $_POST['image']);
    $st->execute(); $note = 'Option added.';
  }
}
if (isset($_POST['setprice'])) {   // UPDATE
  $price = (int)$_POST['price']; $oid = (int)$_POST['id'];
  if ($price < 1) $note = 'Price must be above 0.';
  else { $st = $db->prepare('UPDATE options SET price=? WHERE id=?'); $st->bind_param('ii', $price, $oid); $st->execute(); $note = 'Price updated.'; }
}
if (isset($_POST['del'])) {   // DELETE
  $oid = (int)$_POST['del'];
  $st = $db->prepare('DELETE FROM options WHERE id=?'); $st->bind_param('i', $oid); $st->execute(); $note = 'Option deleted.';
}
if (isset($_POST['status'])) {   // UPDATE (cancelled bookings stay cancelled)
  $bid = (int)$_POST['id'];
  $st = $db->prepare("UPDATE bookings SET status=? WHERE id=? AND status<>'Cancelled'");
  $st->bind_param('si', $_POST['status'], $bid); $st->execute();
}
top('Admin');
$opt = $db->query('SELECT * FROM options ORDER BY event_type,category,id')->fetch_all(MYSQLI_ASSOC);
$rows = $db->query('SELECT b.*, u.name, (SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM options o WHERE FIND_IN_SET(o.id,b.option_ids)) AS items FROM bookings b JOIN users u ON u.id=b.user_id ORDER BY b.id DESC')->fetch_all(MYSQLI_ASSOC);
?>
<h1>Admin dashboard</h1>
<p class="msg"><?= h($note) ?></p>
<h2 id="bookings">Booking requests</h2>
<?php if (!$rows) echo '<p>No booking requests yet.</p>'; else { ?>
<table><tr><th>#</th><th>Customer</th><th>Event</th><th>Items</th><th>Date</th><th>Venue</th><th>Phone</th><th>Total</th><th>Status</th><th></th></tr>
<?php foreach ($rows as $r) { ?>
<tr><td><?= $r['id'] ?></td><td><?= h($r['name']) ?></td><td><?= h($r['event_type']) ?></td><td><?= h($r['items']) ?></td><td><?= h($r['event_date']) ?></td><td><?= h($r['venue']) ?></td><td><?= h($r['phone']) ?></td><td>₹<?= number_format($r['total']) ?></td><td class="s-<?= $r['status'] ?>"><?= $r['status'] ?></td>
<td><?php if ($r['status'] != 'Cancelled') { ?><form method="post" class="row"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="status" value="Approved">Approve</button><button class="ghost" name="status" value="Rejected">Reject</button></form><?php } ?></td></tr>
<?php } ?></table><?php } ?>
<h2>Add an option</h2>
<form method="post" class="row"><input type="hidden" name="add" value="1">
  <input name="event_type" list="evs" placeholder="Event (e.g. Birthday)" required><datalist id="evs"><option>Birthday</option><option>Wedding</option><option>Anniversary</option><option>Baby Shower</option></datalist>
  <select name="category"><option>Decoration</option><option>Cake</option><option>Host</option><option>Photography</option><option>Catering</option></select>
  <input name="name" placeholder="Name" required><input name="details" placeholder="Details" required>
  <input name="price" type="number" min="1" placeholder="Price" required><input name="image" placeholder="Image keyword (e.g. roses)" required>
  <button>Add option</button></form>
<h2 id="options">Manage options</h2>
<table><tr><th>Event</th><th>Category</th><th>Name</th><th>Price</th><th></th></tr>
<?php foreach ($opt as $o) { ?>
<tr><td><?= h($o['event_type']) ?></td><td><?= h($o['category']) ?></td><td><?= h($o['name']) ?></td>
<td><form method="post" class="row"><input type="hidden" name="id" value="<?= $o['id'] ?>"><input name="price" type="number" min="1" value="<?= $o['price'] ?>" style="width:110px"><button name="setprice" value="1" class="ghost">Update</button></form></td>
<td><form method="post" onsubmit="return confirm('Delete this option?')"><button name="del" value="<?= $o['id'] ?>" class="ghost">Delete</button></form></td></tr>
<?php } ?></table>
<?php bottom(); ?>
