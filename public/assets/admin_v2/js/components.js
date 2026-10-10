/**
 * Admin v2 UI Components Engine
 * - Custom Professional Table Engine (V2Table) - Zero DataTables Dependency
 * - High-Precision Select2 Initializer with Responsive Sizing & Contained Width
 * - Modern Flatpickr Datepicker & DateRange Initializer
 */
(function (window, $) {
    'use strict';

    function getJQuery() {
        return window.jQuery || window.$;
    }

    /* =========================================================================
       1. SELECT2 INITIALIZATION & SIZING ENGINE
       ========================================================================= */
    window.initSelect2 = function (context) {
        const jQueryInstance = getJQuery();
        if (!jQueryInstance || !jQueryInstance.fn || !jQueryInstance.fn.select2) return;

        const isRtl = jQueryInstance('html').attr('dir') === 'rtl';
        const $target = context 
            ? jQueryInstance(context).find('select:not(.no-select2):not(.swal2-select)') 
            : jQueryInstance('select:not(.no-select2):not(.swal2-select)');

        $target.each(function () {
            const $select = jQueryInstance(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                return;
            }

            const placeholder = $select.attr('placeholder') || $select.data('placeholder') || ($select.find('option[value=""]').first().text().trim() || 'اختر من القائمة...');
            const allowClear = $select.data('allow-clear') !== false && ($select.find('option[value=""]').length > 0 || !$select.prop('required'));

            $select.select2({
                dir: isRtl ? 'rtl' : 'ltr',
                placeholder: placeholder,
                allowClear: allowClear,
                width: '100%',
                dropdownAutoWidth: false, // Prevents 100vw full screen blowout
                dropdownParent: $select.closest('.modal').length ? $select.closest('.modal') : jQueryInstance(document.body)
            });
        });
    };

    /* =========================================================================
       2. FLATPICKR DATEPICKER ENGINE
       ========================================================================= */
    window.initDatePickers = function (context) {
        if (typeof flatpickr === 'undefined') return;

        const jQueryInstance = getJQuery();
        const root = context ? (jQueryInstance ? jQueryInstance(context) : (typeof context === 'string' ? document.querySelector(context) : context)) : document;
        if (!root) return;

        const isArabic = document.documentElement.getAttribute('lang') === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';
        
        let arabicLocale = undefined;
        if (isArabic && typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.ar) {
            arabicLocale = flatpickr.l10ns.ar;
        }

        // 1. Date Range Pickers (.daterange, .date-range, .flatpickr-range)
        const rangeInputs = root.querySelectorAll ? root.querySelectorAll('.daterange, .date-range, .flatpickr-range, input[name*="date_range"], input[name*="range"]') : [];
        rangeInputs.forEach(function (el) {
            if (el._flatpickr) return;
            try {
                if (el.type === 'date') el.type = 'text';
                flatpickr(el, {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    locale: arabicLocale,
                    disableMobile: true,
                    monthSelectorType: 'static'
                });
            } catch (e) { console.warn('Flatpickr range error:', e); }
        });

        // 2. DateTime Pickers (.datetimepicker, .flatpickr-datetime, input[type="datetime-local"])
        const dateTimeInputs = root.querySelectorAll ? root.querySelectorAll('.datetimepicker, .flatpickr-datetime, input[type="datetime-local"]') : [];
        dateTimeInputs.forEach(function (el) {
            if (el._flatpickr) return;
            try {
                if (el.type === 'datetime-local') el.type = 'text';
                flatpickr(el, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    time_24hr: true,
                    allowInput: true,
                    locale: arabicLocale,
                    disableMobile: true,
                    monthSelectorType: 'static'
                });
            } catch (e) { console.warn('Flatpickr datetime error:', e); }
        });

        // 3. Single Date Pickers (.datepicker, .flatpickr-date, input[type="date"], input[name="date_from"], input[name="date_to"], etc.)
        const dateInputs = root.querySelectorAll ? root.querySelectorAll('.datepicker, .flatpickr-date, input[data-provider="flatpickr"], input[type="date"], input[name="date_from"], input[name="date_to"], input[name="date"]') : [];
        dateInputs.forEach(function (el) {
            if (el._flatpickr || el.classList.contains('daterange') || el.classList.contains('date-range')) return;
            try {
                if (el.type === 'date') el.type = 'text';
                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    locale: arabicLocale,
                    altInput: false,
                    disableMobile: true,
                    monthSelectorType: 'static'
                });
            } catch (e) { console.warn('Flatpickr date error:', e); }
        });
    };

    /* =========================================================================
       3. CUSTOM PROFESSIONAL TABLE ENGINE (V2Table) - Zero DataTables Dependency
       ========================================================================= */
    class V2Table {
        constructor(selector, options = {}) {
            this.selector = selector;
            this.tableEl = typeof selector === 'string' ? document.querySelector(selector) : selector;
            if (!this.tableEl) return;

            this.options = Object.assign({
                ajax: null, // { url, data: fn, type: 'GET' }
                data: [], // Static array of objects
                columns: [], // [{ key/data, title, sortable, render: fn, className, orderable, searchable }]
                perPage: 15,
                perPageOptions: [10, 25, 50, 100],
                defaultSort: null, // { key, order: 'asc'|'desc' }
                searchInput: null, // Selector for custom search input
                filterForm: null,
                emptyText: document.documentElement.lang === 'ar' ? 'لا توجد بيانات متاحة حالياً' : 'No data available',
                emptySubtext: document.documentElement.lang === 'ar' ? 'جرب تغيير شروط الفلترة أو البحث' : 'Try adjusting search or filters',
                onLoaded: null
            }, options);

            this.currentPage = 1;
            this.perPage = parseInt(this.options.perPage) || 15;
            this.searchTerm = '';
            this.sortKey = this.options.defaultSort ? this.options.defaultSort.key : null;
            this.sortOrder = this.options.defaultSort ? (this.options.defaultSort.order || 'desc') : 'desc';
            this.rawData = [];
            this.filteredData = [];
            this.isLoading = false;

            this.init();
        }

        init() {
            this.buildWrapperStructure();
            this.bindEvents();

            if (this.options.ajax) {
                this.fetchData();
            } else if (this.options.data && this.options.data.length > 0) {
                this.rawData = [...this.options.data];
                this.applyFilterAndRender();
            } else {
                // Parse existing HTML table rows if present
                this.parseDOMRows();
                this.applyFilterAndRender();
            }

            // Save instance to table element
            this.tableEl._v2Table = this;
        }

        buildWrapperStructure() {
            let cardWrapper = this.tableEl.closest('.v2-table-wrapper') || this.tableEl.closest('.admin-card') || this.tableEl.parentElement;
            const isAr = document.documentElement.lang === 'ar';

            // 1. TOP TOOLBAR: Add or integrate Rows Per Page above the table
            let topHeader = cardWrapper.querySelector('.card-header-flex') || cardWrapper.querySelector('.v2-table-header');
            if (topHeader) {
                if (!topHeader.querySelector('.v2-per-page')) {
                    const perPageDiv = document.createElement('div');
                    perPageDiv.className = 'v2-per-page';
                    perPageDiv.innerHTML = `
                        <span>${isAr ? 'عرض' : 'Show'}</span>
                        <select class="v2-per-page-select no-select2 form-select" style="width: auto !important; height: 32px !important; min-height: 32px !important; padding: 0.15rem 1.75rem 0.15rem 0.6rem !important; font-size: 0.8rem !important; font-weight: 700 !important; display: inline-block !important;">
                            ${this.options.perPageOptions.map(opt => `<option value="${opt}" ${opt === this.perPage ? 'selected' : ''}>${opt}</option>`).join('')}
                        </select>
                        <span>${isAr ? 'سجل' : 'entries'}</span>
                    `;
                    // Tools group lives at the END of the header (opposite the title)
                    let tools = topHeader.querySelector('.v2-header-tools');
                    if (!tools) {
                        const kids = Array.from(topHeader.children);
                        if (kids.length > 1) {
                            tools = kids[kids.length - 1];
                        } else {
                            tools = document.createElement('div');
                            topHeader.appendChild(tools);
                        }
                        tools.classList.add('v2-header-tools');
                    }
                    tools.insertBefore(perPageDiv, tools.firstChild);
                }
            } else {
                // Create a top toolbar before the table
                const topBar = document.createElement('div');
                topBar.className = 'v2-table-top-bar';
                topBar.style.cssText = 'display: flex; align-items: center; justify-content: flex-end; padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border-color); background: var(--bg-card);';
                topBar.innerHTML = `
                    <div class="v2-per-page">
                        <span>${isAr ? 'عرض' : 'Show'}</span>
                        <select class="v2-per-page-select no-select2 form-select" style="width: auto !important; height: 32px !important; min-height: 32px !important; padding: 0.15rem 1.75rem 0.15rem 0.6rem !important; font-size: 0.8rem !important; font-weight: 700 !important; display: inline-block !important;">
                            ${this.options.perPageOptions.map(opt => `<option value="${opt}" ${opt === this.perPage ? 'selected' : ''}>${opt}</option>`).join('')}
                        </select>
                        <span>${isAr ? 'سجل لكل صفحة' : 'entries per page'}</span>
                    </div>
                `;
                this.tableEl.parentElement.insertAdjacentElement('beforebegin', topBar);
            }

            // 2. BOTTOM FOOTER: Records info and pagination navigation
            let footer = cardWrapper.querySelector('.v2-table-footer');
            if (!footer) {
                footer = document.createElement('div');
                footer.className = 'v2-table-footer';
                footer.innerHTML = `
                    <div class="v2-pagination-info" style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);"></div>
                    <div class="v2-pagination-nav"></div>
                `;
                this.tableEl.parentElement.insertAdjacentElement('afterend', footer);
            }

            this.footerEl = footer;
            this.perPageSelect = cardWrapper.querySelector('.v2-per-page-select');
            this.infoEl = footer.querySelector('.v2-pagination-info');
            this.navEl = footer.querySelector('.v2-pagination-nav');

            // 3. Setup sortable column headers
            const thList = this.tableEl.querySelectorAll('thead th');
            thList.forEach((th, index) => {
                const colDef = this.options.columns[index] || {};
                const isSortable = colDef.sortable !== false && colDef.orderable !== false && (colDef.key || colDef.data || th.dataset.sort);
                
                if (isSortable) {
                    th.style.cursor = 'pointer';
                    th.classList.add('v2-sortable-th');
                    const key = colDef.key || colDef.data || th.dataset.sort || index;
                    th.dataset.sortKey = key;
                    
                    if (!th.querySelector('.v2-sort-icon')) {
                        const icon = document.createElement('i');
                        icon.className = 'fa-solid fa-sort v2-sort-icon ms-1 me-1 text-muted';
                        icon.style.fontSize = '0.75rem';
                        th.appendChild(icon);
                    }

                    th.addEventListener('click', () => {
                        this.handleSort(key, th);
                    });
                }
            });
        }

        bindEvents() {
            // Per page change listener
            if (this.perPageSelect) {
                this.perPageSelect.addEventListener('change', (e) => {
                    this.perPage = parseInt(e.target.value) || 15;
                    this.currentPage = 1;
                    this.applyFilterAndRender();
                });
            }

            // Search input binding
            if (this.options.searchInput) {
                const searchEl = typeof this.options.searchInput === 'string' 
                    ? document.querySelector(this.options.searchInput) 
                    : this.options.searchInput;
                if (searchEl) {
                    searchEl.addEventListener('input', (e) => {
                        this.searchTerm = e.target.value.trim().toLowerCase();
                        this.currentPage = 1;
                        this.applyFilterAndRender();
                    });
                }
            }

            // Filter form binding
            if (this.options.filterForm) {
                const formEl = typeof this.options.filterForm === 'string' 
                    ? document.querySelector(this.options.filterForm) 
                    : this.options.filterForm;
                if (formEl) {
                    formEl.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.reload(true);
                    });
                }
            }
        }

        parseDOMRows() {
            const tbody = this.tableEl.querySelector('tbody');
            if (!tbody) return;
            const rows = tbody.querySelectorAll('tr');
            this.domRows = Array.from(rows);
            this.rawData = this.domRows.map((row, rIdx) => {
                const cells = Array.from(row.querySelectorAll('td'));
                const rowObj = { _domRow: row, _index: rIdx };
                cells.forEach((td, cIdx) => {
                    const colKey = (this.options.columns[cIdx] && (this.options.columns[cIdx].key || this.options.columns[cIdx].data)) || `col_${cIdx}`;
                    rowObj[colKey] = td.innerText.trim();
                    rowObj[`_html_${cIdx}`] = td.innerHTML;
                });
                return rowObj;
            });
        }

        fetchData() {
            if (!this.options.ajax || !this.options.ajax.url) return;
            this.isLoading = true;
            this.renderLoadingState();

            let requestData = {};
            if (typeof this.options.ajax.data === 'function') {
                requestData = this.options.ajax.data({}) || {};
            } else if (typeof this.options.ajax.data === 'object') {
                requestData = { ...this.options.ajax.data };
            }

            const jQueryInstance = getJQuery();
            const url = this.options.ajax.url;
            const type = this.options.ajax.type || 'GET';

            if (jQueryInstance) {
                jQueryInstance.ajax({
                    url: url,
                    type: type,
                    data: requestData,
                    dataType: 'json',
                    success: (response) => {
                        this.isLoading = false;
                        let records = [];
                        if (Array.isArray(response)) {
                            records = response;
                        } else if (response && Array.isArray(response.data)) {
                            records = response.data;
                        } else if (response && response.records) {
                            records = response.records;
                        }
                        this.rawData = records;
                        this.applyFilterAndRender();
                        if (typeof this.options.onLoaded === 'function') {
                            this.options.onLoaded(this.rawData);
                        }
                    },
                    error: (xhr) => {
                        this.isLoading = false;
                        this.renderErrorState(xhr.statusText || 'Error loading data');
                    }
                });
            } else {
                fetch(url)
                    .then(res => res.json())
                    .then(response => {
                        this.isLoading = false;
                        this.rawData = Array.isArray(response) ? response : (response.data || []);
                        this.applyFilterAndRender();
                    })
                    .catch(err => {
                        this.isLoading = false;
                        this.renderErrorState(err.message);
                    });
            }
        }

        handleSort(key, thEl) {
            if (this.sortKey === key) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortOrder = 'asc';
            }

            // Update UI icons
            this.tableEl.querySelectorAll('.v2-sortable-th .v2-sort-icon').forEach(icon => {
                icon.className = 'fa-solid fa-sort v2-sort-icon ms-1 me-1 text-muted';
            });
            const activeIcon = thEl.querySelector('.v2-sort-icon');
            if (activeIcon) {
                activeIcon.className = `fa-solid fa-sort-${this.sortOrder === 'asc' ? 'up' : 'down'} v2-sort-icon ms-1 me-1 text-primary`;
            }

            this.applyFilterAndRender();
        }

        applyFilterAndRender() {
            let data = [...this.rawData];

            // 1. Search filter
            if (this.searchTerm) {
                const term = this.searchTerm;
                data = data.filter(item => {
                    return Object.values(item).some(val => {
                        if (val === null || val === undefined) return false;
                        if (typeof val === 'object' && val._domRow) return false;
                        return String(val).toLowerCase().includes(term);
                    });
                });
            }

            // 2. Sorting
            if (this.sortKey) {
                data.sort((a, b) => {
                    let valA = a[this.sortKey] ?? '';
                    let valB = b[this.sortKey] ?? '';

                    // Try numeric comparison
                    let numA = parseFloat(String(valA).replace(/[^0-9.-]+/g, ''));
                    let numB = parseFloat(String(valB).replace(/[^0-9.-]+/g, ''));

                    if (!isNaN(numA) && !isNaN(numB) && String(valA).length < 15 && String(valB).length < 15) {
                        return this.sortOrder === 'asc' ? numA - numB : numB - numA;
                    }

                    valA = String(valA).toLowerCase();
                    valB = String(valB).toLowerCase();
                    if (valA < valB) return this.sortOrder === 'asc' ? -1 : 1;
                    if (valA > valB) return this.sortOrder === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            this.filteredData = data;
            const total = this.filteredData.length;
            const maxPage = Math.max(1, Math.ceil(total / this.perPage));
            if (this.currentPage > maxPage) {
                this.currentPage = maxPage;
            }

            const startIndex = (this.currentPage - 1) * this.perPage;
            const endIndex = Math.min(startIndex + this.perPage, total);
            const pageData = this.filteredData.slice(startIndex, endIndex);

            this.renderTableBody(pageData);
            this.renderPagination(total, startIndex, endIndex, maxPage);
            if (window.v2CompactActions) window.v2CompactActions(this.tableEl);
        }

        renderTableBody(pageData) {
            const tbody = this.tableEl.querySelector('tbody');
            if (!tbody) return;

            if (pageData.length === 0) {
                const colCount = this.tableEl.querySelectorAll('thead th').length || 6;
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${colCount}" class="p-0">
                            <div class="v2-empty-state">
                                <div class="v2-empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                                <div class="v2-empty-title">${this.options.emptyText}</div>
                                <div class="v2-empty-text">${this.options.emptySubtext}</div>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            // If DOM rows parsed
            if (this.domRows && this.domRows.length > 0 && (!this.options.columns || this.options.columns.length === 0)) {
                tbody.innerHTML = '';
                pageData.forEach(item => {
                    tbody.appendChild(item._domRow);
                });
                return;
            }

            // Render from JSON columns definition
            let html = '';
            pageData.forEach((row, rowIndex) => {
                html += '<tr>';
                this.options.columns.forEach((col, colIndex) => {
                    const key = col.key || col.data || '';
                    const rawVal = row[key];
                    let cellContent = rawVal !== undefined ? rawVal : '';
                    
                    if (typeof col.render === 'function') {
                        cellContent = col.render(rawVal, row, rowIndex);
                    } else if (col.defaultContent && (cellContent === '' || cellContent === null || cellContent === undefined)) {
                        cellContent = col.defaultContent;
                    }

                    const alignStyle = col.className ? ` class="${col.className}"` : '';
                    html += `<td${alignStyle}>${cellContent}</td>`;
                });
                html += '</tr>';
            });

            tbody.innerHTML = html;
        }

        renderPagination(total, start, end, maxPage) {
            const isAr = document.documentElement.lang === 'ar';

            // 1. Info Text
            if (this.infoEl) {
                if (total === 0) {
                    this.infoEl.textContent = isAr ? 'لا توجد سجلات' : 'No entries';
                } else {
                    const startLabel = start + 1;
                    this.infoEl.textContent = isAr 
                        ? `عرض ${startLabel} إلى ${end} من إجمالي ${total} سجل`
                        : `Showing ${startLabel} to ${end} of ${total} entries`;
                }
            }

            // 2. Pagination Nav Buttons
            if (!this.navEl) return;
            if (maxPage <= 1) {
                this.navEl.innerHTML = '';
                return;
            }

            let navHtml = '';

            // First + Previous buttons
            navHtml += `
                <button type="button" class="v2-page-btn" data-page="1" ${this.currentPage === 1 ? 'disabled' : ''} title="${isAr ? 'الأولى' : 'First'}">
                    <i class="fa-solid fa-angles-${isAr ? 'right' : 'left'}"></i>
                </button>
                <button type="button" class="v2-page-btn" data-page="${this.currentPage - 1}" ${this.currentPage === 1 ? 'disabled' : ''} title="${isAr ? 'السابق' : 'Previous'}">
                    <i class="fa-solid fa-chevron-${isAr ? 'right' : 'left'}"></i>
                </button>
            `;

            // Page numbers algorithm with smart ellipsis
            const delta = 1;
            const range = [];
            for (let i = Math.max(2, this.currentPage - delta); i <= Math.min(maxPage - 1, this.currentPage + delta); i++) {
                range.push(i);
            }

            if (this.currentPage - delta > 2) {
                range.unshift('...');
            }
            if (this.currentPage + delta < maxPage - 1) {
                range.push('...');
            }

            range.unshift(1);
            if (maxPage > 1) {
                range.push(maxPage);
            }

            range.forEach(p => {
                if (p === '...') {
                    navHtml += `<span class="v2-page-btn" style="border: none; background: transparent; cursor: default;">...</span>`;
                } else {
                    navHtml += `
                        <button type="button" class="v2-page-btn ${p === this.currentPage ? 'active' : ''}" data-page="${p}">
                            ${p}
                        </button>
                    `;
                }
            });

            // Next + Last buttons
            navHtml += `
                <button type="button" class="v2-page-btn" data-page="${this.currentPage + 1}" ${this.currentPage === maxPage ? 'disabled' : ''} title="${isAr ? 'التالي' : 'Next'}">
                    <i class="fa-solid fa-chevron-${isAr ? 'left' : 'right'}"></i>
                </button>
                <button type="button" class="v2-page-btn" data-page="${maxPage}" ${this.currentPage === maxPage ? 'disabled' : ''} title="${isAr ? 'الأخيرة' : 'Last'}">
                    <i class="fa-solid fa-angles-${isAr ? 'left' : 'right'}"></i>
                </button>
            `;

            this.navEl.innerHTML = navHtml;

            // Bind click to page buttons
            this.navEl.querySelectorAll('button[data-page]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const page = parseInt(btn.dataset.page);
                    if (!isNaN(page) && page >= 1 && page <= maxPage && page !== this.currentPage) {
                        this.currentPage = page;
                        this.applyFilterAndRender();
                    }
                });
            });
        }

        renderLoadingState() {
            const tbody = this.tableEl.querySelector('tbody');
            if (!tbody) return;
            const colCount = this.tableEl.querySelectorAll('thead th').length || 6;
            const isAr = document.documentElement.lang === 'ar';
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colCount}" style="text-align: center; padding: 3rem 1rem;">
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem;">
                            <div class="spinner-border text-primary" style="width: 2.25rem; height: 2.25rem;" role="status"></div>
                            <span style="font-size: 0.875rem; font-weight: 700; color: var(--text-muted);">${isAr ? 'جاري تحميل البيانات...' : 'Loading data...'}</span>
                        </div>
                    </td>
                </tr>
            `;
        }

        renderErrorState(errMsg) {
            const tbody = this.tableEl.querySelector('tbody');
            if (!tbody) return;
            const colCount = this.tableEl.querySelectorAll('thead th').length || 6;
            const isAr = document.documentElement.lang === 'ar';
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colCount}" class="p-0">
                        <div class="v2-empty-state">
                            <div class="v2-empty-icon text-danger"><i class="fa-solid fa-circle-exclamation"></i></div>
                            <div class="v2-empty-title text-danger">${isAr ? 'فشل تحميل البيانات' : 'Failed to load data'}</div>
                            <div class="v2-empty-text">${errMsg}</div>
                        </div>
                    </td>
                </tr>
            `;
        }

        reload(resetPage = false) {
            if (resetPage) this.currentPage = 1;
            if (this.options.ajax) {
                this.fetchData();
            } else {
                this.applyFilterAndRender();
            }
        }

        search(term) {
            this.searchTerm = (term || '').trim().toLowerCase();
            this.currentPage = 1;
            this.applyFilterAndRender();
        }
    }

    /* =========================================================================
       3b. ACTIONS COLUMN COMPACTOR
       Up to MAX_INLINE actions are shown inline; extra ones collapse into a
       kebab (⋮) popover menu. Elements are MOVED (not cloned) so inline
       onclick handlers and form submissions keep working.
       ========================================================================= */
    var MAX_INLINE = 3;
    var openMenu = null;

    function closeActionMenu() {
        if (openMenu) {
            openMenu.menu.classList.remove('open');
            openMenu.btn.classList.remove('active');
            openMenu = null;
        }
    }

    function positionMenu(btn, menu) {
        menu.style.visibility = 'hidden';
        menu.classList.add('open');
        var r = btn.getBoundingClientRect();
        var mw = menu.offsetWidth, mh = menu.offsetHeight;
        var left = document.documentElement.dir === 'rtl' ? r.left : r.right - mw;
        left = Math.min(Math.max(8, left), window.innerWidth - mw - 8);
        var top = r.bottom + 6;
        if (top + mh > window.innerHeight - 8) top = Math.max(8, r.top - mh - 6);
        menu.style.left = left + 'px';
        menu.style.top = top + 'px';
        menu.style.visibility = '';
    }

    function compactCell(td) {
        if (!td || td.dataset.v2Compact || td.querySelector('[data-v2-compact]')) return;
        var isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';

        // CASE 1: Cell contains a Bootstrap dropdown (.dropdown or .dropdown-menu)
        var dropdownMenu = td.querySelector('.dropdown-menu');
        if (dropdownMenu) {
            var rawItems = Array.prototype.slice.call(dropdownMenu.querySelectorAll('a, button')).filter(function(el) {
                return !el.classList.contains('dropdown-divider');
            });

            if (rawItems.length > 0) {
                var container = document.createElement('div');
                container.className = 'v2-actions-inline';

                var directItems = rawItems.slice(0, 2);
                var overflowItems = rawItems.slice(2);

                directItems.forEach(function(item) {
                    var btn = document.createElement(item.tagName.toLowerCase() === 'a' ? 'a' : 'button');
                    if (item.tagName.toLowerCase() === 'a') {
                        btn.href = item.getAttribute('href') || '#';
                        if (item.target) btn.target = item.target;
                    } else {
                        btn.type = 'button';
                    }
                    if (item.getAttribute('onclick')) btn.setAttribute('onclick', item.getAttribute('onclick'));

                    var icon = item.querySelector('i, svg');
                    var iconHtml = icon ? icon.outerHTML : '<i class="fa-solid fa-circle"></i>';
                    var label = (item.textContent || '').trim();
                    var isView = /view|عرض|تفاصيل/i.test(label) || /eye/i.test(iconHtml);
                    var isEdit = /edit|تعديل/i.test(label) || /pencil|edit/i.test(iconHtml);
                    var isDelete = /delete|حذف/i.test(label) || /trash/i.test(iconHtml);

                    btn.className = 'v2-action-btn' + (isView ? ' v2-action-btn-view' : (isEdit ? ' v2-action-btn-edit' : (isDelete ? ' v2-action-btn-delete' : '')));
                    btn.title = label;
                    btn.innerHTML = iconHtml;
                    container.appendChild(btn);
                });

                if (overflowItems.length > 0) {
                    var wrap = document.createElement('div');
                    wrap.className = 'v2-actions-more-wrap';
                    var moreBtn = document.createElement('button');
                    moreBtn.type = 'button';
                    moreBtn.className = 'v2-actions-more';
                    moreBtn.title = isAr ? 'المزيد من الإجراءات' : 'More actions';
                    moreBtn.innerHTML = '<i class="fa-solid fa-ellipsis-vertical"></i>';

                    var menu = document.createElement('div');
                    menu.className = 'v2-actions-menu';

                    overflowItems.forEach(function(item) {
                        var mItem = document.createElement(item.tagName.toLowerCase() === 'a' ? 'a' : 'button');
                        if (item.tagName.toLowerCase() === 'a') {
                            mItem.href = item.getAttribute('href') || '#';
                            if (item.target) mItem.target = item.target;
                        } else {
                            mItem.type = 'button';
                        }
                        if (item.getAttribute('onclick')) mItem.setAttribute('onclick', item.getAttribute('onclick'));

                        var icon = item.querySelector('i, svg');
                        var iconHtml = icon ? icon.outerHTML : '<i class="fa-solid fa-circle"></i>';
                        var label = (item.textContent || '').trim();
                        var isDelete = /delete|حذف/i.test(label) || /trash/i.test(iconHtml);

                        mItem.className = 'v2-menu-item' + (isDelete ? ' btn-action-danger' : '');
                        mItem.innerHTML = iconHtml + '<span class="v2-act-label">' + label + '</span>';
                        menu.appendChild(mItem);
                    });

                    wrap.appendChild(moreBtn);
                    wrap.appendChild(menu);
                    container.appendChild(wrap);

                    moreBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (openMenu && openMenu.menu === menu) { closeActionMenu(); return; }
                        closeActionMenu();
                        positionMenu(moreBtn, menu);
                        moreBtn.classList.add('active');
                        openMenu = { menu: menu, btn: moreBtn };
                    });
                    menu.addEventListener('click', function(e) {
                        if (e.target.closest('a,button')) setTimeout(closeActionMenu, 0);
                    });
                }

                td.innerHTML = '';
                td.appendChild(container);
                td.dataset.v2Compact = '1';
                return;
            }
        }

        // CASE 2: Cell contains inline buttons, forms, or act-action-btn links
        var container = td.querySelector('.btn-action-group, .v2-actions-inline, .d-flex') || td;
        while (container.children.length === 1 && /^(DIV|SPAN)$/.test(container.firstElementChild.tagName) &&
               !container.firstElementChild.matches('a,button,form')) {
            container = container.firstElementChild;
        }

        var units = Array.prototype.slice.call(container.children).filter(function(u) {
            return u.matches('a,button,form') || u.querySelector('a,button');
        });

        td.dataset.v2Compact = '1';
        if (units.length === 0) return;

        // Apply v2-action-btn class to all direct units
        units.forEach(function(u) {
            var targetBtn = u.matches('a,button') ? u : u.querySelector('a,button');
            if (targetBtn) {
                targetBtn.classList.add('v2-action-btn');
                var label = targetBtn.getAttribute('title') || targetBtn.getAttribute('data-title') || targetBtn.textContent.trim();
                if (/view|عرض|تفاصيل/i.test(label)) targetBtn.classList.add('v2-action-btn-view');
                if (/edit|تعديل/i.test(label)) targetBtn.classList.add('v2-action-btn-edit');
                if (/invoice|فاتورة/i.test(label)) targetBtn.classList.add('v2-action-btn-invoice');
                if (/delete|حذف/i.test(label)) targetBtn.classList.add('v2-action-btn-delete');
            }
        });

        if (units.length <= 2) {
            container.classList.add('v2-actions-inline');
            return;
        }

        // More than 2 buttons: Keep exactly the first 2 inline, put all rest in kebab menu
        var overflow = units.slice(2);
        var wrap = document.createElement('div');
        wrap.className = 'v2-actions-more-wrap';
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'v2-actions-more';
        btn.title = isAr ? 'المزيد من الإجراءات' : 'More actions';
        btn.innerHTML = '<i class="fa-solid fa-ellipsis-vertical"></i>';
        var menu = document.createElement('div');
        menu.className = 'v2-actions-menu';

        overflow.forEach(function(u) {
            var targetEl = u.matches('a,button') ? u : u.querySelector('a,button');
            if (targetEl) {
                targetEl.classList.remove('v2-action-btn', 'act-action-btn', 'act-action-btn--gold', 'btn', 'btn-sm');
                targetEl.classList.add('v2-menu-item');
                targetEl.removeAttribute('style'); // Clear legacy hardcoded style

                var label = targetEl.getAttribute('title') || targetEl.getAttribute('data-title') ||
                            targetEl.getAttribute('aria-label') || (targetEl.textContent || '').trim();
                
                if (!targetEl.querySelector('.v2-act-label') && label) {
                    var s = document.createElement('span');
                    s.className = 'v2-act-label';
                    s.textContent = label;
                    targetEl.appendChild(s);
                }

                if (/delete|حذف/i.test(label) || (targetEl.getAttribute('onclick') || '').indexOf('delete') !== -1) {
                    targetEl.classList.add('btn-action-danger');
                }
            }
            menu.appendChild(u);
        });

        wrap.appendChild(btn);
        wrap.appendChild(menu);
        container.appendChild(wrap);
        container.classList.add('v2-actions-inline');

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (openMenu && openMenu.menu === menu) { closeActionMenu(); return; }
            closeActionMenu();
            positionMenu(btn, menu);
            btn.classList.add('active');
            openMenu = { menu: menu, btn: btn };
        });
        menu.addEventListener('click', function(e) {
            if (e.target.closest('a,button')) setTimeout(closeActionMenu, 0);
        });
    }

    window.v2CompactActions = function(root) {
        var scope = root && root.querySelectorAll ? root : document;
        var tables = scope.matches && scope.matches('table') ? [scope] : scope.querySelectorAll('table.v2-table, table.table');
        Array.prototype.forEach.call(tables, function(table) {
            table.querySelectorAll('tbody tr').forEach(function(tr) {
                var tds = tr.querySelectorAll(':scope > td');
                if (tds.length < 2) return;
                compactCell(tds[tds.length - 1]);
            });
        });
    };

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.v2-actions-more');
        if (btn) {
            e.stopPropagation();
            var wrap = btn.closest('.v2-actions-more-wrap');
            var menu = wrap ? wrap.querySelector('.v2-actions-menu') : null;
            if (!menu) return;
            if (openMenu && openMenu.menu === menu) { closeActionMenu(); return; }
            closeActionMenu();
            positionMenu(btn, menu);
            btn.classList.add('active');
            openMenu = { menu: menu, btn: btn };
            return;
        }
        closeActionMenu();
    });
    window.addEventListener('resize', closeActionMenu);
    window.addEventListener('scroll', closeActionMenu, true);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeActionMenu(); });

    // Expose to window & jQuery
    window.V2Table = V2Table;
    if (getJQuery()) {
        getJQuery().fn.v2Table = function (options) {
            return this.each(function () {
                new V2Table(this, options);
            });
        };
    }

    /* =========================================================================
       4. AUTO INITIALIZATION ON READY
       ========================================================================= */
    function onReady() {
        // Initialize Select2
        window.initSelect2();

        // Initialize DatePickers
        window.initDatePickers();

        // Compact action columns of server-rendered tables
        window.v2CompactActions();

        // Observe DOM for dynamic elements (modals, ajax loads)
        if (window.MutationObserver) {
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function (node) {
                            if (node.nodeType === 1) {
                                if (node.classList && (node.classList.contains('swal2-container') || node.classList.contains('swal2-popup'))) return;
                                if (node.closest && (node.closest('.swal2-container') || node.closest('.swal2-popup'))) return;
                                if (node.tagName === 'SELECT' && !node.classList.contains('no-select2') && !node.classList.contains('swal2-select')) {
                                    window.initSelect2(node.parentElement);
                                } else if (node.querySelectorAll && node.querySelectorAll('select:not(.no-select2):not(.swal2-select)').length > 0) {
                                    window.initSelect2(node);
                                }
                                if (node.querySelectorAll && node.querySelectorAll('.datepicker, .flatpickr-date, .daterange').length > 0) {
                                    window.initDatePickers(node);
                                }
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, { childList: true, subtree: true });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onReady);
    } else {
        onReady();
    }

})(window, window.jQuery || window.$);
