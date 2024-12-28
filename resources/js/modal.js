document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#form');
    const modalBackground = document.querySelector('#modalBackground');
    const confirmationModal = document.querySelector('#confirmationModal');
    const confirmButton = document.querySelector('#confirmClose');
    const cancelButton = document.querySelector('#cancelClose');

    // Удаляем делегирование события изменения
    // form.addEventListener('change', (event) => {
    //     if (event.target.matches('input, textarea, select')) {
    //         isFormChanged = true; // Отмечаем, что форма была изменена
    //     }
    // });

    function closeModal() {
        form.style.transform = 'translateX(100%)'; // Прячем модальное окно
        setTimeout(() => {
            modalBackground.style.display = 'none'; // Скрываем фон после анимации
        }, 500); 
        // Удаляем переменную isFormChanged
        // isFormChanged = false;
    }

    confirmButton.addEventListener('click', () => {
        closeModal();
        confirmationModal.style.display = 'none';
    });

    cancelButton.addEventListener('click', () => {
        confirmationModal.style.display = 'none !important';
    });

    // modalBackground.addEventListener('click', (event) => {
    //     if (event.target === modalBackground) {
    //         hideModal();
    //     }
    // });

    // form.addEventListener('click', (event) => {
    //     event.stopPropagation(); // Предотвращаем всплытие события клика
    // });
});