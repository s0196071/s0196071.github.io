document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('my-sweet-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Очистка предыдущих ошибок
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        const alertBlock = document.getElementById('form-alert');
        if (alertBlock) alertBlock.innerHTML = '';

        const formData = new FormData(form);
        const data = {
            name: formData.get('name') || '',
            email: formData.get('email') || '',
            phone: formData.get('phone') || '',
            comment: formData.get('comment') || '',
            agreement: formData.has('agreement')
        };

        fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            const messagesContainer = document.querySelector('.main-form .form-messages');
            if (!messagesContainer) return;
    
            // Очищаем предыдущие сообщения
            messagesContainer.innerHTML = '';
    
            if (result.success) {
                let message = 'Заявка успешно отправлена!';
                if (result.login) {
                    message += `<br><br>Ваш логин: <strong>${result.login}</strong><br>Пароль: <strong>${result.password}</strong><br><a href="${result.profile_url}">Перейти в профиль</a>`;
                }
                messagesContainer.innerHTML = `<div class="alert alert-success">${message}</div>`;
                form.reset();
            } else if (result.errors) {
                for (const [field, msg] of Object.entries(result.errors)) {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message text-danger';
                        errorDiv.textContent = msg;
                        input.parentNode.appendChild(errorDiv);
                    }
                }
            } else {
                messagesContainer.innerHTML = `<div class="alert alert-danger">${result.message || 'Произошла ошибка'}</div>`;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            const messagesContainer = document.querySelector('.main-form .form-messages');
            if (messagesContainer) {
                messagesContainer.innerHTML = `<div class="alert alert-danger">Ошибка соединения с сервером</div>`;
            }
        });
    });
});
