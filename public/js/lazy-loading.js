/**
 * Sistema de Lazy Loading para Imágenes
 * Implementa carga diferida de imágenes para mejorar el rendimiento
 */

class LazyImageLoader {
    constructor(options = {}) {
        this.options = {
            threshold: options.threshold || 0.1,
            rootMargin: options.rootMargin || '50px',
            loadingClass: options.loadingClass || 'lazy-loading',
            loadedClass: options.loadedClass || 'lazy-loaded',
            errorClass: options.errorClass || 'lazy-error',
            retryAttempts: options.retryAttempts || 3,
            retryDelay: options.retryDelay || 1000,
            placeholder: options.placeholder || '/images/placeholder.svg',
            ...options
        };
        
        this.retryMap = new Map();
        this.observer = null;
        this.init();
    }
    
    init() {
        // Verificar soporte para Intersection Observer
        if (!('IntersectionObserver' in window)) {
            this.fallbackToImmediate();
            return;
        }
        
        this.setupObserver();
        this.observeImages();
        this.addErrorHandling();
    }
    
    setupObserver() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    this.observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: this.options.threshold,
            rootMargin: this.options.rootMargin
        });
    }
    
    observeImages() {
        const lazyImages = document.querySelectorAll('img[data-lazy-src], img[data-src]');
        
        lazyImages.forEach(img => {
            // Agregar placeholder si no tiene src
            if (!img.src || img.src === window.location.href) {
                img.src = this.options.placeholder;
            }
            
            // Agregar clase de carga
            img.classList.add(this.options.loadingClass);
            
            // Observar imagen
            this.observer.observe(img);
        });
    }
    
    loadImage(img) {
        const src = img.dataset.lazySrc || img.dataset.src;
        
        if (!src) {
            console.warn('LazyLoader: No hay URL de imagen para cargar');
            return;
        }
        
        // Crear nueva imagen para precargar
        const imageLoader = new Image();
        
        imageLoader.onload = () => {
            this.onImageLoad(img, src);
        };
        
        imageLoader.onerror = () => {
            this.onImageError(img, src);
        };
        
        // Iniciar carga
        imageLoader.src = src;
    }
    
    onImageLoad(img, src) {
        // Actualizar src de la imagen
        img.src = src;
        
        // Actualizar clases
        img.classList.remove(this.options.loadingClass);
        img.classList.add(this.options.loadedClass);
        
        // Limpiar data attributes
        delete img.dataset.lazySrc;
        delete img.dataset.src;
        
        // Eliminar de retry map
        this.retryMap.delete(img);
        
        // Trigger evento
        img.dispatchEvent(new CustomEvent('lazyLoaded', {
            detail: { src: src }
        }));
    }
    
    onImageError(img, src) {
        const retryCount = this.retryMap.get(img) || 0;
        
        if (retryCount < this.options.retryAttempts) {
            // Incrementar contador de reintentos
            this.retryMap.set(img, retryCount + 1);
            
            // Reintentar después del delay
            setTimeout(() => {
                this.loadImage(img);
            }, this.options.retryDelay * (retryCount + 1));
            
        } else {
            // Máximo de reintentos alcanzado
            img.classList.remove(this.options.loadingClass);
            img.classList.add(this.options.errorClass);
            
            // Usar imagen de error si está disponible
            if (this.options.errorImage) {
                img.src = this.options.errorImage;
            }
            
            // Trigger evento de error
            img.dispatchEvent(new CustomEvent('lazyError', {
                detail: { src: src, attempts: retryCount }
            }));
            
            console.warn(`LazyLoader: Error cargando imagen después de ${retryCount} intentos: ${src}`);
        }
    }
    
    fallbackToImmediate() {
        // Fallback para navegadores sin Intersection Observer
        console.warn('LazyLoader: Intersection Observer no soportado, cargando todas las imágenes inmediatamente');
        
        const lazyImages = document.querySelectorAll('img[data-lazy-src], img[data-src]');
        lazyImages.forEach(img => {
            this.loadImage(img);
        });
    }
    
    addErrorHandling() {
        // Manejo global de errores de imágenes
        document.addEventListener('error', (e) => {
            if (e.target.tagName === 'IMG' && e.target.classList.contains(this.options.loadingClass)) {
                const src = e.target.dataset.lazySrc || e.target.dataset.src || e.target.src;
                this.onImageError(e.target, src);
            }
        }, true);
    }
    
    // Método para agregar nuevas imágenes después de la inicialización
    observeNewImages(container = document) {
        const newImages = container.querySelectorAll('img[data-lazy-src]:not(.lazy-loading):not(.lazy-loaded), img[data-src]:not(.lazy-loading):not(.lazy-loaded)');
        
        newImages.forEach(img => {
            if (!img.src || img.src === window.location.href) {
                img.src = this.options.placeholder;
            }
            
            img.classList.add(this.options.loadingClass);
            this.observer.observe(img);
        });
        
        return newImages.length;
    }
    
    // Forzar carga de una imagen específica
    forceLoad(img) {
        if (this.observer && img.classList.contains(this.options.loadingClass)) {
            this.observer.unobserve(img);
            this.loadImage(img);
        }
    }
    
    // Obtener estadísticas
    getStats() {
        return {
            loading: document.querySelectorAll(`.${this.options.loadingClass}`).length,
            loaded: document.querySelectorAll(`.${this.options.loadedClass}`).length,
            error: document.querySelectorAll(`.${this.options.errorClass}`).length,
            retrying: this.retryMap.size
        };
    }
    
    // Destruir el loader
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        this.retryMap.clear();
    }
}

/**
 * Sistema de Scroll Infinito
 * Implementa paginación automática mediante scroll
 */
class InfiniteScroll {
    constructor(options = {}) {
        this.options = {
            container: options.container || window,
            contentSelector: options.contentSelector || '.infinite-content',
            loadTriggerOffset: options.loadTriggerOffset || 100,
            loadingIndicatorSelector: options.loadingIndicatorSelector || '.loading-indicator',
            url: options.url || null,
            pageParam: options.pageParam || 'page',
            initialPage: options.initialPage || 1,
            maxPages: options.maxPages || null,
            onLoad: options.onLoad || null,
            onComplete: options.onComplete || null,
            onError: options.onError || null,
            debounceDelay: options.debounceDelay || 100,
            ...options
        };
        
        this.currentPage = this.options.initialPage;
        this.isLoading = false;
        this.isComplete = false;
        this.debounceTimer = null;
        
        this.init();
    }
    
    init() {
        if (!this.options.url) {
            console.error('InfiniteScroll: URL es requerida');
            return;
        }
        
        this.container = this.options.container === window ? window : document.querySelector(this.options.container);
        this.contentContainer = document.querySelector(this.options.contentSelector);
        this.loadingIndicator = document.querySelector(this.options.loadingIndicatorSelector);
        
        if (!this.container || !this.contentContainer) {
            console.error('InfiniteScroll: Contenedores requeridos no encontrados');
            return;
        }
        
        this.attachScrollListener();
        this.hideLoadingIndicator();
    }
    
    attachScrollListener() {
        const scrollHandler = () => {
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }
            
            this.debounceTimer = setTimeout(() => {
                this.checkScrollPosition();
            }, this.options.debounceDelay);
        };
        
        this.container.addEventListener('scroll', scrollHandler);
        
        // También verificar en resize
        window.addEventListener('resize', scrollHandler);
    }
    
    checkScrollPosition() {
        if (this.isLoading || this.isComplete) {
            return;
        }
        
        let scrollTop, scrollHeight, clientHeight;
        
        if (this.container === window) {
            scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            scrollHeight = document.documentElement.scrollHeight;
            clientHeight = window.innerHeight;
        } else {
            scrollTop = this.container.scrollTop;
            scrollHeight = this.container.scrollHeight;
            clientHeight = this.container.clientHeight;
        }
        
        const scrollBottom = scrollTop + clientHeight;
        const shouldLoad = scrollBottom >= scrollHeight - this.options.loadTriggerOffset;
        
        if (shouldLoad) {
            this.loadMoreContent();
        }
    }
    
    async loadMoreContent() {
        if (this.isLoading || this.isComplete) {
            return;
        }
        
        // Verificar límite de páginas
        if (this.options.maxPages && this.currentPage >= this.options.maxPages) {
            this.markComplete();
            return;
        }
        
        this.isLoading = true;
        this.showLoadingIndicator();
        
        try {
            const nextPage = this.currentPage + 1;
            const url = new URL(this.options.url, window.location.origin);
            url.searchParams.set(this.options.pageParam, nextPage);
            
            // Agregar parámetros adicionales si existen
            if (this.options.extraParams) {
                Object.keys(this.options.extraParams).forEach(key => {
                    url.searchParams.set(key, this.options.extraParams[key]);
                });
            }
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.html && data.html.trim()) {
                // Agregar nuevo contenido
                this.appendContent(data.html);
                this.currentPage = nextPage;
                
                // Observar nuevas imágenes lazy
                if (window.lazyLoader) {
                    const newImagesCount = window.lazyLoader.observeNewImages(this.contentContainer);
                    console.log(`InfiniteScroll: ${newImagesCount} nuevas imágenes agregadas a lazy loading`);
                }
                
                // Callback de carga exitosa
                if (this.options.onLoad) {
                    this.options.onLoad(data, nextPage);
                }
                
            } else {
                // No hay más contenido
                this.markComplete();
            }
            
        } catch (error) {
            console.error('InfiniteScroll: Error cargando contenido:', error);
            
            if (this.options.onError) {
                this.options.onError(error);
            } else {
                this.showError('Error cargando más contenido. Inténtelo de nuevo.');
            }
        } finally {
            this.isLoading = false;
            this.hideLoadingIndicator();
        }
    }
    
    appendContent(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Mover todos los elementos al contenedor
        while (tempDiv.firstChild) {
            this.contentContainer.appendChild(tempDiv.firstChild);
        }
        
        // Trigger evento
        this.contentContainer.dispatchEvent(new CustomEvent('contentAppended', {
            detail: { page: this.currentPage + 1 }
        }));
    }
    
    markComplete() {
        this.isComplete = true;
        
        if (this.options.onComplete) {
            this.options.onComplete();
        }
        
        // Mostrar mensaje de completado
        this.showCompletedMessage();
    }
    
    showLoadingIndicator() {
        if (this.loadingIndicator) {
            this.loadingIndicator.style.display = 'block';
        }
    }
    
    hideLoadingIndicator() {
        if (this.loadingIndicator) {
            this.loadingIndicator.style.display = 'none';
        }
    }
    
    showError(message) {
        if (this.loadingIndicator) {
            this.loadingIndicator.innerHTML = `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    ${message}
                    <br>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="infiniteScroll.retry()">
                        Reintentar
                    </button>
                </div>
            `;
            this.loadingIndicator.style.display = 'block';
        }
    }
    
    showCompletedMessage() {
        if (this.loadingIndicator) {
            this.loadingIndicator.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p class="mb-0">Has visto todo el contenido disponible</p>
                </div>
            `;
            this.loadingIndicator.style.display = 'block';
        }
    }
    
    retry() {
        if (this.loadingIndicator) {
            this.loadingIndicator.innerHTML = `
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 mb-0 text-muted">Cargando más contenido...</p>
                </div>
            `;
        }
        
        this.loadMoreContent();
    }
    
    // Reiniciar el scroll infinito
    reset() {
        this.currentPage = this.options.initialPage;
        this.isLoading = false;
        this.isComplete = false;
        this.hideLoadingIndicator();
    }
    
    // Actualizar parámetros de URL
    updateParams(params) {
        this.options.extraParams = { ...this.options.extraParams, ...params };
        this.reset();
    }
}

// Inicialización automática
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Lazy Loading
    if (document.querySelectorAll('img[data-lazy-src], img[data-src]').length > 0) {
        window.lazyLoader = new LazyImageLoader({
            placeholder: '/images/placeholder.svg',
            errorImage: '/images/error.svg'
        });
        
        console.log('LazyLoader inicializado:', window.lazyLoader.getStats());
    }
    
    // Inicializar Infinite Scroll si hay contenedor
    const infiniteContainer = document.querySelector('.infinite-scroll-container');
    if (infiniteContainer) {
        const url = infiniteContainer.dataset.url;
        const pageParam = infiniteContainer.dataset.pageParam || 'page';
        
        if (url) {
            window.infiniteScroll = new InfiniteScroll({
                url: url,
                pageParam: pageParam,
                contentSelector: '.infinite-content',
                loadingIndicatorSelector: '.loading-indicator',
                onLoad: (data, page) => {
                    console.log(`Página ${page} cargada exitosamente`);
                },
                onComplete: () => {
                    console.log('Infinite scroll completado');
                }
            });
        }
    }
});