/**
 * Sistema de Búsqueda Instantánea con Caché
 * Implementa búsqueda global AJAX con debouncing y cache local
 */

class InstantSearch {
    constructor(options = {}) {
        this.options = {
            searchInputId: options.searchInputId || 'global-search',
            resultsContainerId: options.resultsContainerId || 'search-results',
            minLength: options.minLength || 2,
            debounceDelay: options.debounceDelay || 300,
            maxResults: options.maxResults || 10,
            searchUrl: options.searchUrl || '/search/global',
            cacheExpiration: options.cacheExpiration || 300000 // 5 minutos
        };
        
        this.cache = new Map();
        this.searchTimeout = null;
        this.currentRequest = null;
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        const searchInput = document.getElementById(this.options.searchInputId);
        const resultsContainer = document.getElementById(this.options.resultsContainerId);
        
        if (!searchInput || !resultsContainer) {
            console.warn('InstantSearch: elementos requeridos no encontrados');
            return;
        }
        
        this.searchInput = searchInput;
        this.resultsContainer = resultsContainer;
        
        this.attachEventListeners();
        this.createLoadingIndicator();
    }
    
    attachEventListeners() {
        // Búsqueda con debouncing
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });
        
        // Limpiar resultados al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!this.searchInput.contains(e.target) && 
                !this.resultsContainer.contains(e.target)) {
                this.hideResults();
            }
        });
        
        // Navegación con teclado
        this.searchInput.addEventListener('keydown', (e) => {
            this.handleKeyNavigation(e);
        });
        
        // Mostrar resultados al enfocar si hay búsqueda previa
        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value.length >= this.options.minLength && 
                this.resultsContainer.innerHTML) {
                this.showResults();
            }
        });
    }
    
    createLoadingIndicator() {
        this.loadingIndicator = document.createElement('div');
        this.loadingIndicator.className = 'search-loading';
        this.loadingIndicator.innerHTML = `
            <div class="d-flex align-items-center justify-content-center p-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Buscando...</span>
                </div>
                <span class="text-muted">Buscando...</span>
            </div>
        `;
    }
    
    handleSearch(query) {
        // Cancelar búsqueda anterior
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        // Cancelar petición en curso
        if (this.currentRequest) {
            this.currentRequest.abort();
            this.currentRequest = null;
        }
        
        query = query.trim();
        
        // Ocultar resultados si la consulta es muy corta
        if (query.length < this.options.minLength) {
            this.hideResults();
            return;
        }
        
        // Debouncing
        this.searchTimeout = setTimeout(() => {
            this.performSearch(query);
        }, this.options.debounceDelay);
    }
    
    performSearch(query) {
        const cacheKey = query.toLowerCase();
        const cachedResult = this.cache.get(cacheKey);
        
        // Verificar caché local
        if (cachedResult && (Date.now() - cachedResult.timestamp) < this.options.cacheExpiration) {
            this.displayResults(cachedResult.data, true);
            return;
        }
        
        this.showLoading();
        
        // Crear AbortController para cancelar peticiones
        const abortController = new AbortController();
        this.currentRequest = abortController;
        
        fetch(`${this.options.searchUrl}?q=${encodeURIComponent(query)}&limit=${this.options.maxResults}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            signal: abortController.signal
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            this.currentRequest = null;
            
            // Guardar en caché local
            this.cache.set(cacheKey, {
                data: data,
                timestamp: Date.now()
            });
            
            // Limpiar caché si crece mucho
            if (this.cache.size > 50) {
                const oldestKey = this.cache.keys().next().value;
                this.cache.delete(oldestKey);
            }
            
            this.displayResults(data, false);
        })
        .catch(error => {
            this.currentRequest = null;
            
            if (error.name === 'AbortError') {
                return; // Búsqueda cancelada, no mostrar error
            }
            
            console.error('Error en búsqueda:', error);
            this.showError('Error al realizar la búsqueda. Inténtelo de nuevo.');
        });
    }
    
    displayResults(data, fromCache = false) {
        this.isLoading = false;
        
        if (!data.results || data.results.length === 0) {
            this.showNoResults(data.query);
            return;
        }
        
        let html = `
            <div class="search-header p-2 bg-light border-bottom">
                <small class="text-muted">
                    ${data.total} resultado(s) para "${data.query}"
                    ${fromCache ? '<i class="fas fa-bolt text-warning ms-1" title="Desde caché"></i>' : ''}
                </small>
            </div>
        `;
        
        data.results.forEach((result, index) => {
            const badgeColor = this.getBadgeColor(result.type);
            const stockWarning = result.stock_low ? '<i class="fas fa-exclamation-triangle text-warning ms-1" title="Stock bajo"></i>' : '';
            
            html += `
                <div class="search-result-item p-3 border-bottom" data-index="${index}">
                    <div class="d-flex align-items-center">
                        <div class="search-icon me-3">
                            <i class="${result.icon} fa-lg text-${badgeColor}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="${result.url}" class="text-decoration-none search-result-link">
                                            ${result.title}
                                        </a>
                                        ${stockWarning}
                                    </h6>
                                    <p class="mb-1 text-muted small">${result.subtitle}</p>
                                    <small class="text-muted">${result.description}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-${badgeColor}">${result.badge}</span>
                                    ${result.date ? `<br><small class="text-muted">${result.date}</small>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        this.resultsContainer.innerHTML = html;
        this.showResults();
        
        // Agregar evento click a los resultados
        this.attachResultClickEvents();
    }
    
    getBadgeColor(type) {
        const colors = {
            'producto': 'primary',
            'cliente': 'success',
            'venta': 'info',
            'usuario': 'warning'
        };
        return colors[type] || 'secondary';
    }
    
    showLoading() {
        this.isLoading = true;
        this.resultsContainer.innerHTML = '';
        this.resultsContainer.appendChild(this.loadingIndicator);
        this.showResults();
    }
    
    showNoResults(query) {
        this.resultsContainer.innerHTML = `
            <div class="text-center p-4">
                <i class="fas fa-search fa-2x text-muted mb-3"></i>
                <h6 class="text-muted">No se encontraron resultados</h6>
                <small class="text-muted">No hay coincidencias para "${query}"</small>
            </div>
        `;
        this.showResults();
    }
    
    showError(message) {
        this.resultsContainer.innerHTML = `
            <div class="text-center p-4">
                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                <h6 class="text-warning">Error de búsqueda</h6>
                <small class="text-muted">${message}</small>
            </div>
        `;
        this.showResults();
    }
    
    showResults() {
        this.resultsContainer.style.display = 'block';
        this.resultsContainer.classList.add('show');
    }
    
    hideResults() {
        this.resultsContainer.style.display = 'none';
        this.resultsContainer.classList.remove('show');
    }
    
    attachResultClickEvents() {
        const resultItems = this.resultsContainer.querySelectorAll('.search-result-item');
        resultItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                this.highlightResult(item);
            });
            
            item.addEventListener('mouseleave', () => {
                this.unhighlightResult(item);
            });
        });
    }
    
    handleKeyNavigation(e) {
        const results = this.resultsContainer.querySelectorAll('.search-result-item');
        if (results.length === 0) return;
        
        let currentIndex = -1;
        const highlighted = this.resultsContainer.querySelector('.search-result-item.highlighted');
        if (highlighted) {
            currentIndex = parseInt(highlighted.dataset.index);
        }
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                const nextIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
                this.selectResult(nextIndex);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                const prevIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
                this.selectResult(prevIndex);
                break;
                
            case 'Enter':
                e.preventDefault();
                if (highlighted) {
                    const link = highlighted.querySelector('.search-result-link');
                    if (link) {
                        window.location.href = link.href;
                    }
                }
                break;
                
            case 'Escape':
                this.hideResults();
                break;
        }
    }
    
    selectResult(index) {
        const results = this.resultsContainer.querySelectorAll('.search-result-item');
        
        // Remover selección anterior
        results.forEach(item => this.unhighlightResult(item));
        
        // Seleccionar nuevo elemento
        if (results[index]) {
            this.highlightResult(results[index]);
        }
    }
    
    highlightResult(item) {
        item.classList.add('highlighted', 'bg-light');
    }
    
    unhighlightResult(item) {
        item.classList.remove('highlighted', 'bg-light');
    }
    
    clearCache() {
        this.cache.clear();
    }
    
    destroy() {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        if (this.currentRequest) {
            this.currentRequest.abort();
        }
        
        this.clearCache();
    }
}

// Autocomplete para productos
class ProductAutocomplete {
    constructor(inputElement, options = {}) {
        this.input = inputElement;
        this.options = {
            minLength: options.minLength || 2,
            maxResults: options.maxResults || 15,
            searchUrl: options.searchUrl || '/search/products',
            onSelect: options.onSelect || null,
            placeholder: options.placeholder || 'Buscar productos...',
            ...options
        };
        
        this.cache = new Map();
        this.dropdown = null;
        this.searchTimeout = null;
        
        this.init();
    }
    
    init() {
        if (!this.input) return;
        
        this.input.placeholder = this.options.placeholder;
        this.createDropdown();
        this.attachEvents();
    }
    
    createDropdown() {
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'product-autocomplete-dropdown position-absolute bg-white border border-top-0 w-100';
        this.dropdown.style.cssText = `
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            display: none;
        `;
        
        this.input.parentNode.style.position = 'relative';
        this.input.parentNode.appendChild(this.dropdown);
    }
    
    attachEvents() {
        this.input.addEventListener('input', (e) => {
            this.handleInput(e.target.value);
        });
        
        this.input.addEventListener('keydown', (e) => {
            this.handleKeydown(e);
        });
        
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.hideDropdown();
            }
        });
    }
    
    handleInput(query) {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        query = query.trim();
        
        if (query.length < this.options.minLength) {
            this.hideDropdown();
            return;
        }
        
        this.searchTimeout = setTimeout(() => {
            this.search(query);
        }, 300);
    }
    
    async search(query) {
        const cacheKey = query.toLowerCase();
        
        if (this.cache.has(cacheKey)) {
            this.displayResults(this.cache.get(cacheKey));
            return;
        }
        
        try {
            const response = await fetch(`${this.options.searchUrl}?q=${encodeURIComponent(query)}&limit=${this.options.maxResults}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const results = await response.json();
            this.cache.set(cacheKey, results);
            this.displayResults(results);
            
        } catch (error) {
            console.error('Error en autocomplete:', error);
        }
    }
    
    displayResults(results) {
        if (!results || results.length === 0) {
            this.hideDropdown();
            return;
        }
        
        let html = '';
        results.forEach((product, index) => {
            html += `
                <div class="autocomplete-item p-2 border-bottom" data-index="${index}" data-product='${JSON.stringify(product)}'>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${product.text}</div>
                            <small class="text-muted">${product.category} - Stock: ${product.stock}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">S/ ${parseFloat(product.price).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        this.dropdown.innerHTML = html;
        this.showDropdown();
        this.attachItemEvents();
    }
    
    attachItemEvents() {
        const items = this.dropdown.querySelectorAll('.autocomplete-item');
        items.forEach(item => {
            item.addEventListener('click', () => {
                const productData = JSON.parse(item.dataset.product);
                this.selectProduct(productData);
            });
            
            item.addEventListener('mouseenter', () => {
                this.highlightItem(item);
            });
        });
    }
    
    selectProduct(product) {
        this.input.value = product.text;
        this.hideDropdown();
        
        if (this.options.onSelect) {
            this.options.onSelect(product);
        }
    }
    
    handleKeydown(e) {
        const items = this.dropdown.querySelectorAll('.autocomplete-item');
        if (items.length === 0) return;
        
        const highlighted = this.dropdown.querySelector('.autocomplete-item.highlighted');
        let currentIndex = -1;
        
        if (highlighted) {
            currentIndex = parseInt(highlighted.dataset.index);
        }
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                const nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
                this.highlightItem(items[nextIndex]);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
                this.highlightItem(items[prevIndex]);
                break;
                
            case 'Enter':
                e.preventDefault();
                if (highlighted) {
                    const productData = JSON.parse(highlighted.dataset.product);
                    this.selectProduct(productData);
                }
                break;
                
            case 'Escape':
                this.hideDropdown();
                break;
        }
    }
    
    highlightItem(item) {
        // Remover highlight anterior
        this.dropdown.querySelectorAll('.autocomplete-item').forEach(i => {
            i.classList.remove('highlighted', 'bg-light');
        });
        
        // Highlight nuevo item
        item.classList.add('highlighted', 'bg-light');
    }
    
    showDropdown() {
        this.dropdown.style.display = 'block';
    }
    
    hideDropdown() {
        this.dropdown.style.display = 'none';
    }
}

// Inicialización global
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar búsqueda global si existe el elemento
    if (document.getElementById('global-search')) {
        window.globalSearch = new InstantSearch();
    }
    
    // Inicializar autocompletado de productos
    document.querySelectorAll('.product-autocomplete').forEach(input => {
        new ProductAutocomplete(input, {
            onSelect: (product) => {
                // Trigger custom event
                input.dispatchEvent(new CustomEvent('productSelected', {
                    detail: product
                }));
            }
        });
    });
});