(() => {
  const key = 'pitlabs-theme';
  const apply = (theme) => {
    const value = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.dataset.theme = value;
    document.body.dataset.theme = value;
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      const dark = value === 'dark';
      button.setAttribute('aria-label', dark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap');
      button.setAttribute('aria-pressed', dark ? 'true' : 'false');
      const icon = button.querySelector('.bi');
      if (icon) icon.className = dark ? 'bi bi-sun' : 'bi bi-moon-stars';
    });
  };
  let saved = null;
  try { saved = localStorage.getItem(key); } catch (_) {}
  const systemTheme = window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  apply(saved || systemTheme);
  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');
    if (!button) return;
    const next = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
    apply(next);
    try { localStorage.setItem(key, next); } catch (_) {}
  });
})();
