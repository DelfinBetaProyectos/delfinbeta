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

/**
 * Two-digit country calling codes; other codes start with 1 or 7 (one digit) or are three digits.
 */
const TWO_DIGIT_COUNTRY_CODES = new Set([
    '20', '27', '30', '31', '32', '33', '34', '36', '39', '40', '41', '43', '44', '45', '46', '47', '48', '49',
    '51', '52', '53', '54', '55', '56', '57', '58', '60', '61', '62', '63', '64', '65', '66',
    '81', '82', '84', '86', '90', '91', '92', '93', '94', '95', '98',
]);

/**
 * Format phone digits as "+58 424 317 2126": country code, then groups of three (a lone last digit joins the group before it).
 */
const formatPhone = (digits) => {
    if (digits === '') {
        return '';
    }

    const countryCodeLength = /^[17]/.test(digits) ? 1 : TWO_DIGIT_COUNTRY_CODES.has(digits.slice(0, 2)) ? 2 : 3;
    const countryCode = digits.slice(0, countryCodeLength);
    const number = digits.slice(countryCodeLength);
    const groups = number.match(/.{1,3}/g) ?? [];

    if (groups.length > 2 && groups.at(-1).length === 1) {
        const lastDigit = groups.pop();
        groups.push(groups.pop() + lastDigit);
    }

    return ['+' + countryCode, ...groups].filter(Boolean).join(' ');
};

/**
 * Phone inputs ([data-phone-mask]) only take digits (15 at most, the E.164 limit) and show them formatted.
 * The server strips the mask again before saving.
 */
document.addEventListener('input', (event) => {
    const input = event.target.closest?.('input[data-phone-mask]');

    if (!input) {
        return;
    }

    const digitsBeforeCaret = input.value.slice(0, input.selectionStart ?? input.value.length).replace(/\D/g, '').length;
    input.value = formatPhone(input.value.replace(/\D/g, '').slice(0, 15));

    let caret = 0;
    for (let seenDigits = 0; caret < input.value.length && seenDigits < digitsBeforeCaret; caret++) {
        if (/\d/.test(input.value[caret])) {
            seenDigits++;
        }
    }
    input.setSelectionRange(caret, caret);
});
