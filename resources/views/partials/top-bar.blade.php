<div class="top-bar">
    <span>Faça login e ganhe 20% em sua primeira compra. <a href="{{ route('cadastro') }}" class="top-bar-register">Registre-se</a></span>
    <button class="close-btn" title="Fechar">✕</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerLink = document.querySelector('.top-bar-register');
    if (!registerLink) return;

    // Se o usuário já estiver logado, impedir navegação e mostrar aviso
    registerLink.addEventListener('click', function(e) {
        if (document.body.dataset.userLogged === '1') {
            e.preventDefault();
            alert('Sua conta já está logada');
        }
    });
});
</script>
