// Fix para problemas de links na área administrativa
document.addEventListener('DOMContentLoaded', function() {
    // Corrigir links que começam com // (protocolo relativo)
    const links = document.querySelectorAll('a[href^="//"]');
    links.forEach(function(link) {
        const href = link.getAttribute('href');
        if (href.startsWith('//')) {
            // Remover as barras duplas e adicionar a URL base
            const newHref = href.substring(2);
            link.setAttribute('href', newHref);
            console.log('Link corrigido:', href, '->', newHref);
        }
    });

    // Corrigir links de dashboard que podem estar sendo modificados pelo MDC
    const dashboardLinks = document.querySelectorAll('a[href*="dashboard"]');
    dashboardLinks.forEach(function(link) {
        const href = link.getAttribute('href');
        if (href.includes('//dashboard')) {
            const newHref = href.replace('//dashboard', '/dashboard');
            link.setAttribute('href', newHref);
            console.log('Dashboard link corrigido:', href, '->', newHref);
        }
    });

    // Verificar se o usuário tem permissão de admin
    const adminLinks = document.querySelectorAll('a[href*="/admin/"]');
    adminLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Verificar se o usuário está autenticado
            if (!window.user || !window.user.permission) {
                e.preventDefault();
                alert('Você precisa estar logado para acessar esta área.');
                return;
            }

            // Verificar se o usuário tem permissão de admin
            if (window.user.permission !== 3) {
                e.preventDefault();
                alert('Você não tem permissão para acessar a área administrativa.');
                window.location.href = '/dashboard';
                return;
            }
        });
    });

    // Log de debug para verificar links
    console.log('Admin fix aplicado. Links encontrados:', {
        total: document.querySelectorAll('a').length,
        admin: document.querySelectorAll('a[href*="/admin/"]').length,
        dashboard: document.querySelectorAll('a[href*="dashboard"]').length
    });
});
