document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.like-btn');
  if (!btn) return;
  const id = btn.getAttribute('data-id');

  try {
    const res = await fetch('/api/like.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'id=' + encodeURIComponent(id)
    });
    const data = await res.json();
    if (!data.success) return;

    btn.classList.add('liked');
    if (btn.hasAttribute('data-detail')) {
      btn.textContent = '❤️ Disukai';
      const el = document.getElementById('detailLikes');
      if (el) el.textContent = data.likes;
    } else {
      btn.textContent = '❤️';
      const countEl = document.querySelector('.like-count[data-id="' + id + '"]');
      if (countEl) countEl.textContent = data.likes;
    }
  } catch (err) {
    console.error('Gagal like:', err);
  }
});
