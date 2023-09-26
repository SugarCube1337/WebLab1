document.getElementById("pointForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Предотвращаем отправку формы
//сверху
    const formData = new FormData(this);
// async-await
    fetch('process.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            // Обновляем таблицу результатов на странице
            var resultTable = document.getElementById('resultTable');
            resultTable.innerHTML = data;
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
});

