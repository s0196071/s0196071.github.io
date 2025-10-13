document.addEventListener('DOMContentLoaded', () => {
    const quantityInput = document.getElementById('quantity');
    const productSelect = document.getElementById('product');
    const calculateButton = document.getElementById('calculate');
    const resultDiv = document.getElementById('result');
    
    const prices = {
        '1': 10000,
        '2': 30000,
        '3': 2500,   
        '4': 2500,   
        '5': 5000    
    };
    
    calculateButton.addEventListener('click', () => {
        resultDiv.innerHTML = '';
        
        const quantity = parseInt(quantityInput.value);
        const product = productSelect.value;
        
        const price = prices[product];
        const total = quantity * price;
        
        showResult(quantity, product, total);
    });
    
    function showResult(quantity, product, total) {
        const productName = productSelect.options[product].text.split(' ')[0];
        resultDiv.innerHTML = `
            <div class="alert alert-success">
                <strong>Результат расчета:</strong><br>
                Товар: ${productName}<br>
                Количество: ${quantity} шт.<br>
                Общая стоимость: ${total.toLocaleString()} ₽
            </div>
        `;
    }
});

