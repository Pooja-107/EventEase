let sel = new Set(), opts = [];
const $ = id => document.getElementById(id);
const post = (a, d) => fetch('api.php?a=' + a, { method: 'POST', body: new URLSearchParams(d) }).then(r => r.json());
const img = o => 'images/' + encodeURIComponent(o.image) + '.jpg';
const EMO = { Decoration: '🎈', Cake: '🎂', Host: '🎤', Photography: '📷', Catering: '🍽️' };
const ph = c => 'data:image/svg+xml,' + encodeURIComponent(`<svg xmlns='http://www.w3.org/2000/svg' width='400' height='260'><rect width='400' height='260' fill='#e9dff5'/><text x='200' y='150' font-size='90' text-anchor='middle'>${EMO[c] || '🎉'}</text></svg>`);
const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const ids = () => [...sel].join(',');

async function loadOptions() {
  opts = await (await fetch('api.php?a=options&type=' + $('type').value)).json();
  [...sel].forEach(i => { if (!opts.some(o => +o.id == i)) sel.delete(i); });
  render();
}
function render() {
  if (!opts.length) { $('options').innerHTML = '<p>No options available for this event yet.</p>'; bill(); return; }
  let cat = '', html = '';
  opts.forEach(o => {
    if (o.category != cat) { cat = o.category; html += `<h2>${cat}</h2>`; }
    const on = sel.has(+o.id);
    html += `<div class="card ${on ? 'on' : ''}"><img src="${img(o)}" alt="" onerror="this.onerror=null;this.src=ph('${o.category}')"><div><b>${esc(o.name)}</b><br>₹${o.price}</div>
      <button class="ghost" onclick="preview(${o.id})">Preview</button>
      <button onclick="toggle(${o.id})">${on ? 'Remove' : 'Add'}</button></div>`;
  });
  $('options').innerHTML = html;
  bill();
}
function toggle(id) { sel.has(id) ? sel.delete(id) : sel.add(id); render(); }
function preview(id) {
  const o = opts.find(x => x.id == id);
  $('mbody').innerHTML = `<img src="${img(o)}" alt="" onerror="this.onerror=null;this.src=ph('${o.category}')"><h2>${esc(o.name)}</h2><p>${esc(o.details)}</p><p class="total">₹${o.price}</p><p>Click anywhere to close</p>`;
  $('modal').style.display = 'flex';
}
async function bill() {
  const r = await post('bill', { ids: ids() });
  $('total').textContent = r.total.toLocaleString('en-IN');
  $('count').textContent = sel.size;
  $('chosen').innerHTML = opts.filter(o => sel.has(+o.id)).map(o => `<li>${esc(o.name)} <span>₹${o.price}</span></li>`).join('');
}
async function savePlan() {
  const r = await post('save', { type: $('type').value, ids: ids() });
  $('msg').textContent = r.ok || r.error;
}
async function book() {
  const b = $('bookbtn'); b.disabled = true;
  const r = await post('book', { type: $('type').value, ids: ids(), date: $('date').value, venue: $('venue').value, phone: $('phone').value, guests: $('guests').value });
  $('msg').textContent = r.ok || r.error;
  if (r.ok) setTimeout(() => location = 'bookings.php', 1200); else b.disabled = false;
}
(async function init() {
  const p = await (await fetch('api.php?a=load')).json();
  if (p && p.option_ids) { $('type').value = p.event_type; p.option_ids.split(',').filter(Boolean).forEach(i => sel.add(+i)); }
  loadOptions();
})();

async function clearPlan() {
  sel.clear(); const r = await post('clear', {}); render(); $('msg').textContent = r.ok || r.error;
}
$('date').min = new Date().toISOString().split('T')[0];
