<?php
require 'config.php'; customer_only();
$uid = (int)me()['id'];
if (isset($_POST['cancel'])) {   // UPDATE: user cancels a booking
  $bid = (int)$_POST['cancel'];
  $st = $db->prepare("UPDATE bookings SET status='Cancelled' WHERE id=? AND user_id=? AND status IN('Pending','Approved')");
  $st->bind_param('ii', $bid, $uid); $st->execute();
}
top('My bookings');
$st = $db->prepare('SELECT b.*, (SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM options o WHERE FIND_IN_SET(o.id,b.option_ids)) AS items FROM bookings b WHERE user_id=? ORDER BY id DESC');
$st->bind_param('i', $uid); $st->execute();
$rows = $st->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<h1>My bookings</h1>
<?php if (!$rows) echo '<p>No bookings yet. <a href=index.php>Plan an event</a>.</p>'; else { ?>
<table><tr><th>#</th><th>Event</th><th>Items</th><th>Date</th><th>Venue</th><th>Guests</th><th>Total</th><th>Status</th><th></th></tr>
<?php foreach ($rows as $r) { ?>
<tr><td><?= $r['id'] ?></td><td><?= h($r['event_type']) ?></td><td><?= h($r['items']) ?></td><td><?= h($r['event_date']) ?></td><td><?= h($r['venue']) ?></td><td><?= $r['guests'] ?></td><td>₹<?= number_format($r['total']) ?></td><td class="s-<?= $r['status'] ?>"><?= $r['status'] ?></td>
<td><?php if (in_array($r['status'], ['Pending', 'Approved'])) { ?><form method="post" onsubmit="return confirm('Cancel this booking?')"><button class="ghost" name="cancel" value="<?= $r['id'] ?>">Cancel</button></form><?php } ?></td></tr>
<?php } ?></table><?php } bottom(); ?>
