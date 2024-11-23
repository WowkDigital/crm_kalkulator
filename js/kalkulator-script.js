jQuery(document).ready(function($) {
    $.ajax({
        url: kalkulatorAjax.ajax_url,
        method: 'POST',
        data: {
            action: 'get_services',
            nonce: kalkulatorAjax.services_nonce
        },
        success: function(response) {
            if (response.success) {
                const services = response.data;
                const tableBody = document.getElementById('service-table-body');
                services.forEach(serviceData => {
                    const { service, cost, variable, price, crm } = serviceData;
                    let inputField = '';
                    let costField = '0';
                    if (variable !== 'none') {
                        inputField = `<input type="number" class="form-control service-quantity" id="${service.toLowerCase().replace(/\s/g, '_')}_${variable}" placeholder="Ilość ${variable}" min="0" value="0" data-cost="${cost}">`;
                        costField = '0'; // Domyślny koszt usługi
                    }
                    const imgSrc = crm.startsWith('http') ? crm : kalkulatorAjax.plugin_url + crm;
                    const newRow = `
                        <tr>
                            <td><input class="form-check-input service" type="checkbox" id="${service.toLowerCase().replace(/\s/g, '_')}" data-price="${price}" data-variable="${variable}"></td>
                            <td>${service}</td>
                            <td>${Math.round(cost)} zł</td>
                            <td>${inputField}</td>
                            <td class="service-cost">${costField}</td>
                            <td class="ok-icon"><img src="${imgSrc}" alt="OK"></td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', newRow);
                });

                // Dodajemy nasłuchiwanie zdarzeń dla dynamicznych elementów
                document.querySelectorAll('.service-quantity').forEach(input => {
                    input.addEventListener('input', handleQuantityChange);
                });

                // Dodajemy nasłuchiwanie zdarzeń dla checkboxów
                document.querySelectorAll('.service').forEach(checkbox => {
                    checkbox.addEventListener('change', handleCheckboxChange);
                });
            }
        }
    });

    function handleQuantityChange(event) {
        const input = event.target;
        const cost = parseFloat(input.dataset.cost);
        const quantity = parseInt(input.value) || 0;
        const serviceCost = cost * quantity;
        const costCell = input.closest('tr').querySelector('.service-cost');
        costCell.textContent = Math.round(serviceCost);

        // Zaznacz lub odznacz checkbox w zależności od ilości
        const checkbox = input.closest('tr').querySelector('.service');
        checkbox.checked = quantity > 0;
    }

    function handleCheckboxChange(event) {
        const checkbox = event.target;
        const row = checkbox.closest('tr');
        const variable = checkbox.dataset.variable;

        if (checkbox.checked && variable === 'none') {
            const costCell = row.querySelector('.service-cost');
            const cost = parseFloat(row.querySelector('td:nth-child(3)').textContent);
            costCell.textContent = Math.round(cost);
        } else if (!checkbox.checked && variable === 'none') {
            const costCell = row.querySelector('.service-cost');
            costCell.textContent = '0';
        }
    }
});

function calculateTotal() {
    document.getElementById('loading').style.display = 'block';
    setTimeout(() => {
        let total = 0;
        let savings = 0;
        const threshold = 500;

        document.querySelectorAll('.service').forEach(checkbox => {
            const row = checkbox.closest('tr');

            if (checkbox.checked) {
                const price = parseFloat(checkbox.dataset.price);
                const variable = checkbox.dataset.variable;
                let variableValue = 1;

                if (variable !== 'none') {
                    variableValue = parseInt(document.getElementById(`${checkbox.id}_${variable}`).value) || 1;
                }

                const serviceCost = price * variableValue;
                total += serviceCost;
                row.querySelector('.service-cost').textContent = Math.round(serviceCost);
            } else {
                row.querySelector('.service-cost').textContent = '0';
            }
        });

        let originalTotal = total;

        if (total > threshold) {
            savings = total - threshold;
            total = threshold;

            const confettiSettings = { target: 'confetti-canvas', clock: 30 };
            const confetti = new ConfettiGenerator(confettiSettings);
            confetti.render();
            setTimeout(() => {
                confetti.clear();
            }, 5000);
        }

        document.getElementById('original-total').textContent = Math.round(originalTotal);
        document.getElementById('total').textContent = Math.round(total);
        document.getElementById('savings').textContent = Math.round(savings);
        document.getElementById('loading').style.display = 'none';
    }, 1000);
}
