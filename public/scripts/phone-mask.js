(function () {
    function formatRuPhone(value) {
        let digits = String(value || '').replace(/\D/g, '');

        if (!digits.length) {
            return '';
        }

        if (digits.startsWith('8') && digits.length >= 11) {
            digits = '7' + digits.slice(1);
        }

        if (!digits.startsWith('7') && digits.length === 10) {
            digits = '7' + digits;
        }

        digits = digits.slice(0, 11);
        const local = digits.startsWith('7') ? digits.slice(1) : digits;

        let result = '+7';

        if (local.length > 0) {
            result += ' (' + local.slice(0, 3);
        }
        if (local.length >= 3) {
            result += ')';
        }
        if (local.length > 3) {
            result += ' ' + local.slice(3, 6);
        }
        if (local.length > 6) {
            result += '-' + local.slice(6, 8);
        }
        if (local.length > 8) {
            result += '-' + local.slice(8, 10);
        }

        return result;
    }

    function initPhoneMask(input) {
        if (!input || input.dataset.phoneMaskReady) {
            return;
        }

        input.dataset.phoneMaskReady = '1';
        input.setAttribute('inputmode', 'tel');
        input.setAttribute('autocomplete', 'tel');
        input.setAttribute('maxlength', '18');
        input.placeholder = input.placeholder || '+7 (999) 123-45-67';

        if (input.value) {
            input.value = formatRuPhone(input.value);
        }

        input.addEventListener('input', () => {
            const start = input.selectionStart;
            const before = input.value;
            input.value = formatRuPhone(input.value);

            if (document.activeElement === input && start !== null) {
                const delta = input.value.length - before.length;
                const pos = Math.max(0, Math.min(input.value.length, start + delta));
                input.setSelectionRange(pos, pos);
            }
        });

        input.addEventListener('focus', () => {
            if (!input.value.trim()) {
                input.value = '+7 (';
                input.setSelectionRange(input.value.length, input.value.length);
            }
        });

        input.addEventListener('blur', () => {
            const digits = input.value.replace(/\D/g, '');
            if (digits.length <= 1) {
                input.value = '';
            }
        });
    }

    function boot() {
        document.querySelectorAll('[data-phone-mask]').forEach(initPhoneMask);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    window.initPhoneMask = initPhoneMask;
    window.formatRuPhone = formatRuPhone;
})();
