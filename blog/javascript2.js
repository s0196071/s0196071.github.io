document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('my-sweet-form');
    
    function getFormData() {
        return {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            tel: document.getElementById('tel').value,
            message: document.getElementById('message').value,
            check: document.getElementById('checkbox').checked
        };
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const checkbox = document.getElementById('checkbox');
        if (!checkbox.checked) {
            alert('Для отправки формы необходимо принять условия конфиденциальности');
            return;
        }
        
        const formData = getFormData();

        fetch('https://formcarry.com/s/2Uzb6krMzij', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                alert('Сообщение успешно отправлено');
                localStorage.removeItem('formData');
                closePopup();

                document.getElementById('name').value = '';
                document.getElementById('email').value = '';
                document.getElementById('tel').value = '';
                document.getElementById('organization').value = '';
                document.getElementById('message').value = '';
                document.getElementById('checkbox').checked = false;
            } else {
                alert('Ошибка отправки');
            }
        })
        .catch(error => {
            alert('Ошибка сети');
        });
    });
});


