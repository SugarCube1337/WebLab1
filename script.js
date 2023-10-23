document.getElementById("checkButton").addEventListener("click", async function() {
// async-await

    const x = document.querySelector('input[name="x"]:checked');
    const y = document.getElementById('y').value;
    const r = document.getElementById('r').value;
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');

    // Проверяем, введены ли все данные
    if (!x || !y || !r) {
        errorMessage.textContent = 'Пожалуйста, введите все данные.';
        successMessage.textContent = '';
        return;
    }

    // Проверяем, являются ли данные числами
    if (isNaN(y) || isNaN(r)) {
        errorMessage.textContent = 'Координата Y и радиус R должны быть числами.';
        successMessage.textContent = '';
        return;
    }

    if (y < -5 || y > 5) {
        errorMessage.textContent = 'Координата Y должна быть в пределах от -5 до 5.';
        successMessage.textContent = '';
        return;
    }

    // Округляем координату Y и радиус R до 2 знаков после запятой
    const yRounded = parseFloat(y).toFixed(2);
    const rRounded = parseFloat(r).toFixed(2);

    // Очищаем сообщение об ошибке, если данные введены правильно
    errorMessage.textContent = '';
    successMessage.textContent = 'Данные введены успешно!';

    try {
        const formData = new FormData();
        formData.append('x', x.value);
        formData.append('y', yRounded);
        formData.append('r', rRounded);

        const response = await fetch('process.php', {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            const data = await response.text();
            const resultTable = document.getElementById('resultTable');
            resultTable.innerHTML = data;
        } else if (response.status === 405) {
            alert("Ошибка: метод не разрешён (405).");
        } else if (response.status === 414) {
            alert("Ошибка: uri слишком длинный (414).");
        } else {
            console.error("Ошибка сервера:", response.status);
        }
    } catch (error) {
        console.error("Ошибка", error);
    }
});


////414: uri (округление)
////get (405 method )