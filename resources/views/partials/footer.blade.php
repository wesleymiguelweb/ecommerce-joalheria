<footer class="container main-footer">
    <div class="footer-grid">
        <div class="footer-about">
            <div class="logo">Elegance Joias</div>
            <p>Joias para todos os momentos.</p>
            <p style="margin-top: 10px; font-size: 0.9em;">
                <a href="mailto:contato@elegancejoias.com.br" style="color: inherit; text-decoration: none;">
                    📧 contato@elegancejoias.com.br
                </a>
            </p>
            <p style="font-size: 0.9em;">
                <a href="tel:+5511999999999" style="color: inherit; text-decoration: none;">
                    📞 (11) 99999-9999
                </a>
            </p>
            <div class="social-icons">
                <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" title="Siga-nos no Twitter" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7a10.66 10.66 0 01-10 5.5z"></path></svg></a>
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" title="Siga-nos no Instagram" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"></path><circle cx="17.5" cy="6.5" r="1.5"></circle></svg></a>
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" title="Curta nossa página no Facebook" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a6 6 0 00-6 6v3H2v4h7v8h4v-8h3l1-4h-4V8a2 2 0 012-2h3z"></path></svg></a>
                <a href="https://wa.me/5511999999999" target="_blank" rel="noopener noreferrer" title="Fale conosco no WhatsApp" aria-label="WhatsApp"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg></a>
            </div>
        </div>
        <div class="footer-links">
            <h3>SOBRE</h3>
            <ul>
                <li><a href="{{ route('sobre') }}">Sobre Nós</a></li>
                <li><a href="{{ route('index') }}">Catálogo</a></li>
                <li><a href="{{ route('suporte') }}">Contato</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h3>AJUDA</h3>
            <ul>
                <li><a href="{{ route('suporte') }}">Suporte</a></li>
                <li><a href="{{ route('carrinho') }}#shipping-calculator">Calcular Frete</a></li>
                <li><a href="{{ route('termos') }}">Termos e Condições</a></li>
                <li><a href="{{ route('privacidade') }}">Políticas e Privacidade</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h3>FAQ</h3>
            <ul>
                <li><a href="{{ route('login') }}">Minha Conta</a></li>
                <li><a href="{{ route('suporte') }}#reclamacoes">Reclamações</a></li>
                <li><a href="{{ route('checkout') }}#payment-method">Pagamento</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Elegance Joias © 2000-2025 - Todos direitos reservados</p>
        <div class="footer-payment-icons">
            <img src="{{ asset('img/bandeiras.jpg') }}" height="35" width="300" alt="Visa Electron" title="Visa Electron">
        </div>
    </div>
</footer>
