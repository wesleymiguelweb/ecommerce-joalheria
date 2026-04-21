<header class="container main-header">
    <a href="{{ route('index') }}" class="logo">Elegance Joias</a>
    <nav>
        <a href="{{ route('index') }}">Página Inicial</a>
        <a href="{{ route('feminino') }}">Feminino</a>
        <a href="{{ route('masculino') }}">Masculino</a>
    </nav>
    <div class="header-icons">
        <form action="{{ route('search') }}" method="GET" class="search-form">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" name="q" placeholder="Pesquisar produtos" class="search-input" value="{{ request('q') }}">
            <button type="submit" style="display: none;"></button>
        </form>
        <a href="{{ route('carrinho') }}" class="icon-link cart-icon-link" title="Carrinho">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            <span class="cart-item-count" id="cart-count">
                {{ array_sum(array_map(fn($i) => $i['quantity'] ?? 0, session('cart', []))) }}
            </span>
        </a>
        @auth
            <div class="user-menu">
                <button class="icon-link user-menu-toggle" type="button" title="Menu do usuário" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    @if(Auth::user()->is_admin)
                        <span class="admin-badge">ADM</span>
                    @endif
                </button>
                <div class="user-menu-dropdown" aria-hidden="true">
                    <p class="menu-user-welcome">Olá,</p>
                    <p class="menu-user-name">{{ Auth::user()->name }}</p>
                    <div class="menu-divider"></div>
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('adm.dashboard') }}" class="menu-link">Painel do Admin</a>
                    @endif
                    <a href="{{ route('profile') }}" class="menu-link">Detalhes da Conta</a>
                    <a href="{{ route('orders.index') }}" class="menu-link">Meus Pedidos</a>
                    <div class="menu-divider"></div>
                    <form action="{{ route('logout') }}" method="POST" class="menu-logout">
                        @csrf
                        <button type="submit" class="menu-logout-btn">Sair</button>
                    </form>
                </div>
            </div>
        @else
            <div class="user-menu">
                <button class="icon-link user-menu-toggle" type="button" title="Entrar ou criar conta" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </button>
                <div class="user-menu-dropdown" aria-hidden="true">
                    <p class="menu-user-welcome">Bem-vindo!</p>
                    <p class="menu-user-name">Acesse sua conta</p>
                    <div class="menu-divider"></div>
                    <a href="{{ route('login') }}" class="menu-link">Entrar</a>
                    <a href="{{ route('cadastro') }}" class="menu-link">Criar conta</a>
                </div>
            </div>
        @endauth
    </div>
</header>

<script>
// Menu do usuário - toggle com fallback
(function() {
    console.log('[USER-MENU] Script carregado');

    // Aguarda o DOM estar pronto se necessário
    if (document.readyState === 'loading') {
        console.log('[USER-MENU] Aguardando DOMContentLoaded...');
        document.addEventListener('DOMContentLoaded', init);
    } else {
        console.log('[USER-MENU] DOM já está pronto, inicializando...');
        init();
    }

    function init() {
        console.log('[USER-MENU] Inicializando...');
        const menus = document.querySelectorAll('.user-menu');
        console.log('[USER-MENU] Menus encontrados:', menus.length);

        if (menus.length === 0) {
            console.warn('[USER-MENU] Nenhum elemento .user-menu encontrado');
            return;
        }

        function closeAll() {
            menus.forEach(m => {
                m.classList.remove('open');
                const t = m.querySelector('.user-menu-toggle');
                const d = m.querySelector('.user-menu-dropdown');
                if (t) t.setAttribute('aria-expanded', 'false');
                if (d) d.setAttribute('aria-hidden', 'true');
            });
        }

        menus.forEach((menu, index) => {
            const toggle = menu.querySelector('.user-menu-toggle');
            const dropdown = menu.querySelector('.user-menu-dropdown');

            console.log(`[USER-MENU] Menu ${index}:`, {
                toggle: !!toggle,
                dropdown: !!dropdown
            });

            if (!toggle || !dropdown) {
                console.warn('[USER-MENU] Toggle ou dropdown não encontrado no menu', index, menu);
                return;
            }

            console.log(`[USER-MENU] Adicionando listener ao menu ${index}`);

            // Click no botão toggle
            toggle.addEventListener('click', function(e) {
                console.log('[USER-MENU] Toggle clicado!');
                e.preventDefault();
                e.stopPropagation();

                const isOpen = menu.classList.contains('open');
                console.log('[USER-MENU] Estado antes:', isOpen ? 'aberto' : 'fechado');

                closeAll();

                if (!isOpen) {
                    menu.classList.add('open');
                    toggle.setAttribute('aria-expanded', 'true');
                    dropdown.setAttribute('aria-hidden', 'false');
                    console.log('[USER-MENU] Menu aberto!');
                    console.log('[USER-MENU] Classes do menu:', menu.className);
                } else {
                    console.log('[USER-MENU] Menu fechado');
                }
            });

            // Evitar fechar ao clicar dentro do dropdown
            dropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });

        // Fechar ao clicar fora - apenas uma vez no documento
        document.addEventListener('click', (e) => {
            const target = e.target;
            const isMenu = target.closest('.user-menu');
            if (!isMenu) {
                closeAll();
            }
        });

        console.log('[USER-MENU] Inicialização completa!');
    }
})();
</script>
