(function () {
    const config = window.playggSearch;
    if (!config?.endpoint) {
        return;
    }

    const wrap = document.querySelector('[data-search-autocomplete]');
    const input = wrap?.querySelector('[data-search-input]');
    const panel = wrap?.querySelector('[data-search-suggestions]');
    const clearBtn = wrap?.querySelector('[data-search-clear]');

    if (!wrap || !input || !panel) {
        return;
    }

    let debounceTimer = null;
    let abortController = null;
    let activeIndex = -1;

    const formatPrice = (value) => {
        const n = Math.round(Number(value));
        return new Intl.NumberFormat('ru-RU').format(n) + ' ₽';
    };

    const hidePanel = () => {
        panel.classList.add('d-none');
        panel.innerHTML = '';
        activeIndex = -1;
    };

    const showPanel = () => {
        panel.classList.remove('d-none');
    };

    const toggleClear = () => {
        if (!clearBtn) {
            return;
        }
        const hasValue = input.value.trim() !== '';
        clearBtn.classList.toggle('d-none', !hasValue);
    };

    const renderItems = (data) => {
        const items = data.items || [];

        if (items.length === 0) {
            panel.innerHTML =
                '<div class="search-suggestions__empty">Ничего не найдено</div>';
            showPanel();
            return;
        }

        const rows = items
            .map((item, index) => {
                const discount =
                    item.discount_percent != null
                        ? `<span class="search-suggestions__discount">-${item.discount_percent}%</span>`
                        : '';
                const oldPrice =
                    item.old_price != null
                        ? `<span class="search-suggestions__old-price">${formatPrice(item.old_price)}</span>`
                        : '';

                return `
                    <a href="${item.url}" class="search-suggestions__item" role="option" data-index="${index}">
                        <img src="${item.image}" alt="" class="search-suggestions__thumb" width="56" height="32" loading="lazy">
                        <span class="search-suggestions__body">
                            <span class="search-suggestions__title">${escapeHtml(item.name)}</span>
                            <span class="search-suggestions__meta">
                                <span class="search-suggestions__platform">${escapeHtml(item.platform)}</span>
                            </span>
                        </span>
                        <span class="search-suggestions__price-wrap">
                            ${discount}
                            ${oldPrice}
                            <span class="search-suggestions__price">от ${formatPrice(item.price)}</span>
                        </span>
                    </a>
                `;
            })
            .join('');

        const catalogLink = data.catalog_url
            ? `<a href="${data.catalog_url}" class="search-suggestions__all">Все результаты в каталоге</a>`
            : '';

        panel.innerHTML = rows + catalogLink;
        showPanel();
    };

    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    const fetchSuggestions = (query) => {
        if (abortController) {
            abortController.abort();
        }

        abortController = new AbortController();

        const url = new URL(config.endpoint, window.location.origin);
        url.searchParams.set('q', query);

        fetch(url.toString(), {
            headers: { Accept: 'application/json' },
            signal: abortController.signal,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Search failed');
                }
                return response.json();
            })
            .then(renderItems)
            .catch((error) => {
                if (error.name !== 'AbortError') {
                    hidePanel();
                }
            });
    };

    const onInput = () => {
        toggleClear();
        const query = input.value.trim();

        clearTimeout(debounceTimer);

        if (query.length < config.minChars) {
            hidePanel();
            return;
        }

        debounceTimer = setTimeout(() => fetchSuggestions(query), 220);
    };

    input.addEventListener('input', onInput);
    input.addEventListener('focus', onInput);

    input.addEventListener('keydown', (event) => {
        const options = panel.querySelectorAll('.search-suggestions__item');

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            activeIndex = Math.min(activeIndex + 1, options.length - 1);
            options[activeIndex]?.focus();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            options[activeIndex]?.focus();
        } else if (event.key === 'Escape') {
            hidePanel();
        }
    });

    document.addEventListener('click', (event) => {
        if (!wrap.contains(event.target)) {
            hidePanel();
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', (event) => {
            if (clearBtn.tagName === 'BUTTON') {
                event.preventDefault();
                input.value = '';
                input.focus();
                toggleClear();
                hidePanel();
            }
        });
    }

    toggleClear();
})();
