<?php
require 'config.php'; customer_only(); top('Plan your event');
?>
<h1>Plan your event</h1>
<label>Event type
  <select id="type" onchange="sel.clear(); loadOptions()"><?php foreach ($db->query('SELECT DISTINCT event_type FROM options ORDER BY event_type') as $r) echo '<option>' . h($r['event_type']) . '</option>'; ?></select></label>
<div class="layout">
  <div id="options"></div>
  <aside>
    <h2>Your estimate</h2>
    <p><span id="count">0</span> items selected</p>
    <ul id="chosen"></ul>
    <p class="total">₹<span id="total">0</span></p>
    <button class="ghost" onclick="savePlan()">Save plan</button> <button class="ghost" onclick="clearPlan()">Clear plan</button>
    <h2>Booking details</h2>
    <input type="date" id="date">
    <input id="venue" placeholder="Venue">
    <input id="phone" placeholder="Phone (10 digits)">
    <input type="number" id="guests" placeholder="Number of guests" min="1">
    <button id="bookbtn" onclick="book()">Submit booking request</button>
    <p id="msg" class="msg"></p>
  </aside>
</div>
<div id="modal" onclick="this.style.display='none'"><div id="mbody"></div></div>
<?php bottom(true); ?>
