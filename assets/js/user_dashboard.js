const sectionIds = ['appointmentsSection', 'menuSection', 'paymentSection'];
const overlay = document.getElementById('pageOverlay');

// הסתרת כל הסקשנים והסרת active מהכפתורים
function hideAllSections() {
  sectionIds.forEach(id => {
    const section = document.getElementById(id);
    if (section) {
      section.style.display = 'none';
      const inner = section.querySelector('section');
      if (inner) inner.classList.remove('section-active');
    }
  });

  document.querySelectorAll('button[data-section]').forEach(btn => {
    btn.classList.remove('active-section-button');
  });
}

function showSection(sectionId) {
  hideAllSections();

  const selectedSection = document.getElementById(sectionId);
  if (selectedSection) {
    selectedSection.style.display = 'block';
    const inner = selectedSection.querySelector('section');
    if (inner) {
      inner.classList.add('section-active');
      inner.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'center' });
    }

    overlay.style.display = 'block';

    const activeButton = document.querySelector(`button[data-section="${sectionId}"]`);
    if (activeButton) activeButton.classList.add('active-section-button');

    localStorage.setItem('lastActiveSection', sectionId);
    sessionStorage.setItem('sectionOpenedFromUser', 'true');

    // ✅ רענון selectpicker אחרי שהקטע מוצג
    if (typeof $ !== 'undefined' && typeof $.fn.selectpicker === 'function') {
      $('.selectpicker').selectpicker('refresh');
    }
  }
}

// סגירת הכל
function closeOverlay() {
  hideAllSections();
  overlay.style.display = 'none';
  sessionStorage.removeItem('sectionOpenedFromUser');
}

// overlay ו־ESC
overlay.addEventListener('click', closeOverlay);
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeOverlay();
});

// עם הטעינה – רק אם המשתמש עבר מתוך האתר
window.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem('lastActiveSection');
  const openedFromUser = sessionStorage.getItem('sectionOpenedFromUser');

  if (sessionStorage.getItem('scrollToMenu') === 'yes') {
    showSection('menuSection');
    sessionStorage.removeItem('scrollToMenu');
  } else if (saved && openedFromUser === 'true') {
    showSection(saved);
  } else {
    closeOverlay(); // לא מציג כלום כברירת מחדל
  }

  // האזנה לכל כפתור
  document.querySelectorAll('button[data-section]').forEach(btn => {
    btn.addEventListener('click', () => {
      const section = btn.getAttribute('data-section');
      if (section) showSection(section);
    });
  });

  // ✅ הפעלת selectpicker רק לאחר שה־DOM נטען
  const selects = document.querySelectorAll('.selectpicker');
  selects.forEach(select => {
    if (typeof $ !== 'undefined' && typeof $(select).selectpicker === 'function') {
      $(select).selectpicker();
    }
  });
});

// פונקציה חיצונית אם תרצה לשלוח ל"תפריט"
function setMenuSection() {
  sessionStorage.setItem('scrollToMenu', 'yes');
}
