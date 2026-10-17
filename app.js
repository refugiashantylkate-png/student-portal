// Client-side validation + delete confirmation + password toggle
document.querySelectorAll('form.needs-validation').forEach(f => {
  f.addEventListener('submit', ev => {
    const pw = f.querySelector('[name=password]'), pc = f.querySelector('[name=confirm]');
    if (pw && pc) pc.setCustomValidity(pw.value !== pc.value ? 'Passwords do not match' : '');
    if (!f.checkValidity()) { ev.preventDefault(); ev.stopPropagation(); }
    f.classList.add('was-validated');
  });
});
document.querySelectorAll('form[data-confirm]').forEach(f =>
  f.addEventListener('submit', ev => { if (!confirm(f.dataset.confirm)) ev.preventDefault(); }));
document.querySelectorAll('[data-toggle-pw]').forEach(b =>
  b.addEventListener('click', () => {
    const i = document.querySelector(b.dataset.togglePw);
    i.type = i.type === 'password' ? 'text' : 'password';
  }));
