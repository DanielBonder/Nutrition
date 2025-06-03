    const select = document.querySelector('.select-selected');
    const items = document.querySelector('.select-items');
    const result = document.getElementById('price-result');
    const button = document.getElementById('purchase-btn');

    let selectedValue = '';

    select.addEventListener('click', () => {
      items.style.display = items.style.display === 'block' ? 'none' : 'block';
    });

    document.querySelectorAll('.select-items div').forEach(option => {
      option.addEventListener('click', () => {
        select.textContent = option.textContent;
        selectedValue = option.getAttribute('data-value');
        items.style.display = 'none';
        result.textContent = 'המחיר: ₪' + selectedValue;
        button.style.display = 'inline-block';
      });
    });

    document.addEventListener('click', function (e) {
      if (!document.getElementById('plan-select').contains(e.target)) {
        items.style.display = 'none';
      }
    });

    function purchase() {
      if (!selectedValue) return;
      const message = `שלום, אני מעוניין לרכוש את התוכנית במחיר ₪${selectedValue}`;
      const url = `https://wa.me/9720546781613?text=${encodeURIComponent(message)}`;
      window.open(url, '_blank');
    }