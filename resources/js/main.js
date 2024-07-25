document.getElementById('add-payment').addEventListener('click', function() {
    let paymentsWrapper = document.querySelector('.payments-wrapper');
    let paymentGroups = paymentsWrapper.querySelectorAll('.payment-group').length;
    if (paymentGroups < 5) {
        let newPaymentGroup = document.createElement('div');
        newPaymentGroup.className = 'input-group input-group-sm mb-3 payment-group';
        newPaymentGroup.innerHTML = `
            <span class="input-group-text" id="inputGroup-sizing-sm">Платеж ${paymentGroups + 1}</span>
            <input type="number" name="payments[]" class="form-control" aria-label="Платеж"
                aria-describedby="inputGroup-sizing-sm">
        `;
        paymentsWrapper.appendChild(newPaymentGroup);
    } else {
        alert('Можно добавить не более 5 платежей.');
    }
});