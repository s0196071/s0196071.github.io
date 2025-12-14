const dialog = document.getElementById('my-dialog');
const dialogOpener = document.querySelector('.open-dialog');
const dialogCloser = dialog.querySelector('.close-dialog');
const form = document.getElementById('my-sweet-form');

// Ключи для localStorage
const STORAGE_KEY = 'form-data-v1';

// Восстановление данных из localStorage при загрузке
function restoreFormData() {
  const savedData = localStorage.getItem(STORAGE_KEY);
  if (savedData) {
    const data = JSON.parse(savedData);
    Object.keys(data).forEach(key => {
      const input = form.querySelector(`[name="${key}"]`);
      if (input) {
        input.value = data[key];
      }
    });
  }
}

// Сохранение данных в localStorage
function saveFormData() {
  const data = {};
  new FormData(form).forEach((value, key) => {
    data[key] = value;
  });
  localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

// Очистка данных из localStorage
function clearFormData() {
  localStorage.removeItem(STORAGE_KEY);
}

// Отправка данных через fetch
async function submitForm(event) {
  event.preventDefault();

  const formData = new FormData(form);
  const jsonData = Object.fromEntries(formData);

  try {
    const response = await fetch('https://formcarry.com/s/itTQnN6wezq', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(jsonData),
    });

    if (!response.ok) {
      throw new Error(`Ошибка сервера: ${response.status}`);
    }

    // Успешная отправка: очищаем поля и localStorage
    form.reset();
    clearFormData();
    alert('Данные отправлены успешно!');

    close(); // Закрываем диалоговое окно

  } catch (error) {
    alert(`Ошибка отправки: ${error.message}`);
  }
}

// Обработчики событий
function closeOnBackDropClick({ currentTarget, target }) {
  if (target === currentTarget) {
    close();
  }
}

function openModalAndLockScroll() {
  dialog.showModal();
  document.body.classList.add('scroll-lock');
  restoreFormData(); // Восстанавливаем данные при открытии
}

function returnScroll() {
  document.body.classList.remove('scroll-lock');
}

function close() {
  dialog.close();
  returnScroll();
}

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
  restoreFormData(); // Сразу восстанавливаем данные при загрузке страницы


  dialog.addEventListener('click', closeOnBackDropClick);
  dialog.addEventListener('cancel', returnScroll);
  dialogOpener.addEventListener('click', openModalAndLockScroll);
  dialogCloser.addEventListener('click', (event) => {
    event.stopPropagation();
    close();
  });

  form.addEventListener('submit', submitForm);

  // Сохраняем данные при изменении полей
  form.querySelectorAll('input, textarea, select').forEach(input => {
    input.addEventListener('input', saveFormData);
  });
});
