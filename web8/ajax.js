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
            comment: formData.get('message') || '',
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
            if (result.success) {
                let message = 'Заявка успешно отправлена!';
                if (result.login) {
                    message += `\nВаш логин: ${result.login}\nПароль: ${result.password}\nПрофиль: ${result.profile_url}`;
                }
                const div = document.createElement('div');
                div.className = 'alert alert-success';
                div.textContent = message;
                form.parentNode.insertBefore(div, form.nextSibling);
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
                alert(result.message || 'Произошла ошибка');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Ошибка соединения с сервером');
        });
    });
});
