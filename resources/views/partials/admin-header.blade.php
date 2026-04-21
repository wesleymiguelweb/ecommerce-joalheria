<header class="admin-header">
    <div class="admin-header-content container">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button onclick="window.history.back()" style="background: none; border: none; cursor: pointer; color: #666; font-size: 0.95em; display: flex; align-items: center; gap: 6px; padding: 8px 0; transition: color 0.2s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#666'">
                <i class="fas fa-arrow-left"></i>
                <span>Voltar</span>
            </button>
            <nav aria-label="breadcrumb" class="admin-breadcrumbs">
                <ol>
                    <li><a href="{{ route('adm.dashboard') }}">Painel</a></li>
                    <li aria-current="page">@yield('breadcrumb', 'Dashboard')</li>
                </ol>
            </nav>
        </div>
        <div class="admin-header-right">
            {{-- <form action="{{ route('adm.search') }}" method="GET" class="admin-search">
                <i class="fas fa-search"></i>
                <input type="search" name="q" placeholder="Pesquisar produtos, usuários..." value="{{ request('q') }}">
            </form> --}}
            <div class="user-profile">
                <span class="user-initial">{{ substr(Auth::user()->name, 0, 1) }}</span>
                <span class="user-name">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; cursor: pointer; color: #999; margin-left: 10px;">Sair</button>
                </form>
            </div>
        </div>
    </div>
</header>
