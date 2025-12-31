{{-- Componente de Búsqueda Global --}}
<div class="search-container mx-auto">
    <div class="position-relative">
        <input 
            type="text" 
            id="global-search" 
            class="form-control search-input"
            placeholder="Buscar productos, clientes, ventas..."
            autocomplete="off"
        >
        <i class="fas fa-search search-icon"></i>
    </div>
    
    {{-- Contenedor de resultados --}}
    <div id="search-results" class="shadow-sm"></div>
</div>

{{-- Estilos en línea para personalización específica --}}
<style>
.search-container {
    max-width: 500px;
}

.search-input {
    height: 45px;
    padding-left: 20px;
    padding-right: 45px;
    border: 2px solid #e3e6f0;
    border-radius: 25px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    outline: 0;
}

.search-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #858796;
    z-index: 10;
}

#search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e3e6f0;
    border-top: none;
    border-radius: 0 0 15px 15px;
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
    display: none;
}

#search-results.show {
    display: block;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.search-result-item {
    border-bottom: 1px solid #e3e6f0;
    transition: background-color 0.2s ease;
    cursor: pointer;
}

.search-result-item:hover,
.search-result-item.highlighted {
    background-color: #f8f9fc;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-link {
    color: #5a5c69;
    text-decoration: none;
    font-weight: 600;
}

.search-result-link:hover {
    color: #4e73df;
    text-decoration: none;
}

.search-header {
    background: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .search-container {
        max-width: 100%;
    }
    
    .search-input {
        font-size: 16px; /* Evita zoom en dispositivos móviles */
    }
}
</style>