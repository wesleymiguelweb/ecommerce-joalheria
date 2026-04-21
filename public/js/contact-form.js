/**
 * Contact Form Handler
 * Simula envio de mensagem com feedback visual
 */

document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contact-form');
    const successMessage = document.getElementById('success-message');
    const submitBtn = document.getElementById('submit-btn');

    if (!contactForm) return;

    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();

        // Capturar dados do formulário
        const email = contactForm.querySelector('input[name="email"]').value;
        const message = contactForm.querySelector('input[name="message"]').value;

        // Validar campos
        if (!email || !message) {
            alert('Por favor, preencha todos os campos.');
            return;
        }

        // Desabilitar botão durante o "envio"
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';
        submitBtn.style.opacity = '0.6';

        // Simular delay de envio (como se estivesse mandando para o servidor)
        setTimeout(() => {
            // Limpar formulário
            contactForm.reset();

            // Ocultar formulário
            contactForm.style.display = 'none';

            // Mostrar mensagem de sucesso
            successMessage.style.display = 'block';
            successMessage.classList.add('show');

            // Resetar o formulário após 5 segundos
            setTimeout(() => {
                contactForm.style.display = 'block';
                successMessage.style.display = 'none';
                successMessage.classList.remove('show');

                // Restaurar botão
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar';
                submitBtn.style.opacity = '1';
            }, 5000);
        }, 1500);
    });
});
