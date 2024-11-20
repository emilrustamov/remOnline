document.addEventListener('DOMContentLoaded', () => {
    let isFormChanged = false;

    const clientForm = document.querySelector('#form');
    const modalBackground = document.querySelector('#modalBackground');
    const confirmationModal = document.querySelector('#confirmationModal');
    const confirmButton = document.querySelector('#confirmClose');
    const cancelButton = document.querySelector('#cancelClose');

    // Делегирование события изменения для отслеживания изменений в полях формы
    clientForm.addEventListener('change', (event) => {
        if (event.target.matches('input, textarea')) {
            isFormChanged = true; // Отмечаем, что форма была изменена
        }
    });

    function showModal() {
        clientForm.style.transform = 'translateX(0)'; // Показываем модальное окно
        modalBackground.style.display = 'block'; // Показываем фон
    }

    function hideModal() {
        if (isFormChanged) {
            confirmationModal.style.display = 'flex'; // Показываем окно подтверждения
        } else {
            closeModal();
        }
    }

    function closeModal() {
        clientForm.style.transform = 'translateX(100%)'; // Прячем модальное окно
        setTimeout(() => {
            modalBackground.style.display = 'none'; // Скрываем фон после анимации
        }, 500); // Длительность анимации (500ms)
        isFormChanged = false;
        resetForm(); // Сбрасываем флаг изменений
    }

    confirmButton.addEventListener('click', () => {
        closeModal();
        confirmationModal.style.display = 'none';
    });

    cancelButton.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
        isFormChanged = false; // Сбрасываем флаг изменений при отмене закрытия
    });

    modalBackground.addEventListener('click', (event) => {
        if (event.target === modalBackground) {
            hideModal();
        }
    });

});