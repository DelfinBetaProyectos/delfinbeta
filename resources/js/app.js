/**
 * Close open <details> dropdowns (nav menu, Download CV) when clicking outside them or pressing Escape.
 */
const openDropdowns = () => document.querySelectorAll('details[data-dropdown][open]');

document.addEventListener('click', (event) => {
    openDropdowns().forEach((dropdown) => {
        if (!dropdown.contains(event.target)) {
            dropdown.removeAttribute('open');
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    openDropdowns().forEach((dropdown) => {
        dropdown.removeAttribute('open');
        dropdown.querySelector('summary')?.focus();
    });
});

/**
 * Count resume downloads (/cv/es, /cv/en) as a GA4 event ("resume_download"); GA4 adds the visitor's
 * country, which the dashboard groups by. GA4 is the only source of download counts.
 */
document.addEventListener('click', (event) => {
    const link = event.target.closest?.('a[href]');
    const resumeLocale = link ? new URL(link.href, window.location.href).pathname.match(/^\/cv\/(es|en)$/)?.[1] : null;

    if (!resumeLocale || typeof window.gtag !== 'function') {
        return;
    }

    window.gtag('event', 'resume_download', {
        resume_locale: resumeLocale,
        site_locale: document.documentElement.lang,
        transport_type: 'beacon',
    });
});

/**
 * Count clicks on links to AraguaneyCode as a GA4 event ("araguaneycode_click") for the dashboard.
 * gtag only exists when Google Analytics is loaded (public pages, visitors only).
 */
document.addEventListener('click', (event) => {
    const link = event.target.closest?.('a[href*="araguaneycode.com"]');

    if (!link || typeof window.gtag !== 'function') {
        return;
    }

    window.gtag('event', 'araguaneycode_click', {
        link_url: link.href,
        site_locale: document.documentElement.lang,
        transport_type: 'beacon',
    });
});

/**
 * Count clicks on the WhatsApp contact link as a GA4 event ("whatsapp_click").
 */
document.addEventListener('click', (event) => {
    const link = event.target.closest?.('a[href^="https://wa.me/"]');

    if (!link || typeof window.gtag !== 'function') {
        return;
    }

    window.gtag('event', 'whatsapp_click', {
        site_locale: document.documentElement.lang,
        transport_type: 'beacon',
    });
});
