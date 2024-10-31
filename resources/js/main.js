// document.getElementById('add-payment').addEventListener('click', function() {
//     let paymentsWrapper = document.querySelector('.payments-wrapper');
//     let paymentGroups = document.getElementsByName('payments[]').length;
//     console.log(paymentGroups);
    
//     if (paymentGroups < 5) {
//         let compiledInputGroup = document.createElement('div');
//         compiledInputGroup.innerHTML = `
//             <div>
//                 <label class="block text-sm font-medium leading-6 text-gray-900">Платеж ${paymentGroups + 1}</label>
//                 <div class="mt-1">
//                     <input type="number" name="payments[]" placeholder="Введите сумму"
//                            class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
//                 </div>
//             </div>
//         `;

//         paymentsWrapper.appendChild(compiledInputGroup);
//     } else {
//         alert('Можно добавить не более 5 платежей.');
//     }
// });