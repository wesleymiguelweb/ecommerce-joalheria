/**
 * Sistema de Autenticação com AJAX
 * Gerencia login, cadastro e logout
 */

document.addEventListener('DOMContentLoaded', () => {
    // ===== LOGIN =====
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;

            if (!email || !password) {
                showAlert('Por favor, preencha todos os campos', 'error');
                return;
            }

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    if (data.errors) {
                        Object.values(data.errors).forEach(error => {
                            showAlert(error[0], 'error');
                        });
                    } else {
                        showAlert(data.message || 'Erro ao fazer login', 'error');
                    }
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                showAlert('Erro ao conectar ao servidor', 'error');
            }
        });
    }

    // ===== CADASTRO =====
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = document.getElementById('register-name').value.trim();
            const email = document.getElementById('register-email').value.trim();
            const password = document.getElementById('register-password').value;
            const passwordConfirm = document.getElementById('register-password-confirm').value;

            if (!name || !email || !password || !passwordConfirm) {
                showAlert('Por favor, preencha todos os campos', 'error');
                return;
            }

            if (password !== passwordConfirm) {
                showAlert('As senhas não conferem', 'error');
                return;
            }

            try {
                const response = await fetch('/cadastro', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        password,
                        password_confirmation: passwordConfirm
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    if (data.errors) {
                        Object.values(data.errors).forEach(error => {
                            showAlert(error[0], 'error');
                        });
                    } else {
                        showAlert(data.message || 'Erro ao criar conta', 'error');
                    }
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                showAlert('Erro ao conectar ao servidor', 'error');
            }
        });
    }

    // ===== LOGOUT =====
    const logoutLinks = document.querySelectorAll('[data-action="logout"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();

            if (confirm('Deseja sair da sua conta?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';

                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (token) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_token';
                    input.value = token;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    /**
     * Mostrar alertas customizados
     */
    function showAlert(message, type = 'info') {
        // Remove alertas anteriores
        const existingAlert = document.querySelector('.auth-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Criar novo alerta
        const alert = document.createElement('div');
        alert.className = `auth-alert auth-alert-${type}`;
        alert.textContent = message;
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        `;

        if (type === 'success') {
            alert.style.backgroundColor = '#4CAF50';
        } else if (type === 'error') {
            alert.style.backgroundColor = '#f44336';
        } else {
            alert.style.backgroundColor = '#2196F3';
        }

        document.body.appendChild(alert);

        // Auto remover após 3 segundos
        setTimeout(() => {
            alert.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }
});

// Estilos para animações de alerta
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

/**
 * Função para alternar visibilidade da senha
 */
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Tornar a função global
window.togglePassword = togglePassword;
