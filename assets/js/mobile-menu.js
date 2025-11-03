// ============================================
// MENU MOBILE - HAMBÚRGUER
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mobileOverlay = document.querySelector('.mobile-overlay');

    // Criar toggle se não existir
    if (!mobileMenuToggle && sidebar) {
        const toggle = document.createElement('button');
        toggle.className = 'mobile-menu-toggle';
        toggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
        toggle.setAttribute('aria-label', 'Abrir menu');
        document.body.insertBefore(toggle, document.body.firstChild);
        
        toggle.addEventListener('click', toggleMobileMenu);
    } else if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
    }

    // Criar overlay se não existir
    if (!mobileOverlay && sidebar) {
        const overlay = document.createElement('div');
        overlay.className = 'mobile-overlay';
        document.body.appendChild(overlay);
        
        overlay.addEventListener('click', closeMobileMenu);
    } else if (mobileOverlay) {
        mobileOverlay.addEventListener('click', closeMobileMenu);
    }

    // Fechar menu ao clicar em link (mobile)
    if (sidebar) {
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeMobileMenu();
                }
            });
        });
    }

    // Fechar menu ao pressionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Ajustar menu ao redimensionar janela
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            if (sidebar) {
                sidebar.classList.remove('active');
            }
            if (mobileOverlay) {
                mobileOverlay.classList.remove('active');
            }
            if (mobileMenuToggle) {
                mobileMenuToggle.classList.remove('active');
            }
        }
    });
});

function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');

    if (!sidebar) return;

    sidebar.classList.toggle('active');
    
    if (mobileOverlay) {
        mobileOverlay.classList.toggle('active');
    }
    
    if (mobileMenuToggle) {
        mobileMenuToggle.classList.toggle('active');
        const icon = mobileMenuToggle.querySelector('i');
        if (icon) {
            if (sidebar.classList.contains('active')) {
                icon.className = 'fa-solid fa-times';
                mobileMenuToggle.setAttribute('aria-label', 'Fechar menu');
            } else {
                icon.className = 'fa-solid fa-bars';
                mobileMenuToggle.setAttribute('aria-label', 'Abrir menu');
            }
        }
    }

    // Previne scroll do body quando menu está aberto
    if (sidebar.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

function closeMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');

    if (sidebar) {
        sidebar.classList.remove('active');
    }
    
    if (mobileOverlay) {
        mobileOverlay.classList.remove('active');
    }
    
    if (mobileMenuToggle) {
        mobileMenuToggle.classList.remove('active');
        const icon = mobileMenuToggle.querySelector('i');
        if (icon) {
            icon.className = 'fa-solid fa-bars';
            mobileMenuToggle.setAttribute('aria-label', 'Abrir menu');
        }
    }

    document.body.style.overflow = '';
}

