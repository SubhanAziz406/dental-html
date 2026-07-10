// =========================================================
// Lahore Dental Clinic — shared front-end behaviour
// =========================================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- Footer year (works whether span has id="yr" or class="yr-js") ----
  var year = new Date().getFullYear();
  document.querySelectorAll('#yr, .yr-js').forEach(function (el) {
    el.textContent = year;
  });

  // ---- Services accordion (Services page) ----
  document.querySelectorAll('.acc-trigger').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.acc-item');
      var wasOpen = item.classList.contains('open');
      document.querySelectorAll('.acc-item.open').forEach(function (openItem) {
        openItem.classList.remove('open');
      });
      if (!wasOpen) item.classList.add('open');
    });
  });

  // ---- Appointment form (Contact page) ----
  var form = document.getElementById('appointmentForm');
  if (!form) return;

  var alertBox = document.getElementById('formAlert');
  var submitBtn = document.getElementById('submitBtn');
  var submitLabel = document.getElementById('submitLabel');

  function showAlert(type, message) {
    alertBox.className = type; // 'success' or 'error'
    alertBox.textContent = message;
    alertBox.style.display = 'block';
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  // Minimum date = today, so no one can pick a past date
  var dateInput = document.getElementById('preferredDate');
  if (dateInput) {
    var today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    var formData = new FormData(form);

    submitBtn.disabled = true;
    submitLabel.textContent = 'Sending...';
    alertBox.style.display = 'none';

    fetch('php/send-mail.php', {
      method: 'POST',
      body: formData
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          showAlert('success', data.message || 'Thanks! Your appointment request has been sent — we will contact you shortly to confirm.');
          form.reset();
        } else {
          showAlert('error', data.message || 'Something went wrong while sending your request. Please call us instead.');
        }
      })
      .catch(function () {
        showAlert('error', 'We could not reach the server. Please check your connection or call us directly.');
      })
      .finally(function () {
        submitBtn.disabled = false;
        submitLabel.textContent = 'Send Appointment Request';
      });
  });
});
