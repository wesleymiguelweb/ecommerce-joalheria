<section class="contact-section">
    <div class="content">
        <h2>Entre em contato conosco</h2>

        <form id="contact-form" class="contact-form">
            <div class="input-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="input-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <input type="email" name="email" placeholder="Seu email" class="input-field" required>
            </div>

            <div class="input-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="input-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <input type="text" name="message" placeholder="Sua mensagem" class="input-field" required>
            </div>
            <button type="submit" class="btn-submit" id="submit-btn">Enviar</button>
        </form>

        <div id="success-message" class="success-message" style="display: none;">
            <div class="success-icon">✓</div>
            <h3>Mensagem Enviada!</h3>
            <p>Obrigado por entrar em contato conosco. Responderemos em breve.</p>
        </div>
    </div>
</section>

<script>
// Simular envio de formulário de contato
(function() {
    const contactForm = document.getElementById('contact-form');
    const successMessage = document.getElementById('success-message');
    const submitBtn = document.getElementById('submit-btn');

    if (!contactForm) return;

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validar campos
        const email = contactForm.querySelector('input[name="email"]').value.trim();
        const message = contactForm.querySelector('input[name="message"]').value.trim();

        if (!email || !message) {
            alert('Por favor, preencha todos os campos.');
            return;
        }

        // Mostrar animação de carregamento
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';
        submitBtn.style.opacity = '0.6';

        // Simular envio (delay de 1.5 segundos)
        setTimeout(() => {
            // Limpar formulário
            contactForm.reset();
            contactForm.style.display = 'none';

            // Mostrar mensagem de sucesso com animação
            successMessage.style.display = 'block';
            successMessage.style.animation = 'fadeIn 0.6s ease-in-out';

            // Restaurar formulário após 4 segundos
            setTimeout(() => {
                successMessage.style.display = 'none';
                contactForm.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar';
                submitBtn.style.opacity = '1';
            }, 4000);
        }, 1500);
    });
})();
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
