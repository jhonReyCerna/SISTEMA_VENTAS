## 📋 Guía de Uso: Tooltips y Confirmaciones

### ✨ Tooltips Informativos

Los tooltips ya están implementados en el menú lateral. Para agregar tooltips en otras partes:

**Ejemplo básico:**
```html
<button class="btn btn-primary" data-toggle="tooltip" title="Descripción del botón">
    <i class="fas fa-save"></i> Guardar
</button>
```

**Ejemplo con posición personalizada:**
```html
<a href="#" data-toggle="tooltip" data-placement="top" title="Ver detalles">
    <i class="fas fa-eye"></i>
</a>

<!-- Posiciones disponibles: top, bottom, left, right -->
```

**Ejemplo en íconos de ayuda:**
```html
<label for="campo">
    Campo importante 
    <i class="fas fa-question-circle text-info" 
       data-toggle="tooltip" 
       title="Este campo es obligatorio para continuar"></i>
</label>
```

---

### 🗑️ Confirmaciones Modales (Eliminar)

Para agregar confirmación antes de eliminar un registro:

**Método 1: Con botón dentro de un formulario**
```html
<form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="button" 
            class="btn btn-danger btn-sm" 
            onclick="confirmDelete(this, '¿Eliminar al usuario {{ $usuario->nombre }}?')"
            data-toggle="tooltip" 
            title="Eliminar usuario">
        <i class="fas fa-trash"></i>
    </button>
</form>
```

**Método 2: Mensaje genérico**
```html
<form action="{{ route('productos.destroy', $producto) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="button" 
            class="btn btn-danger" 
            onclick="confirmDelete(this)">
        <i class="fas fa-trash"></i> Eliminar
    </button>
</form>
```

---

### 🎨 Tooltips en Botones de Acción

**Tabla con botones de acción:**
```html
<td class="text-right">
    <a href="{{ route('productos.show', $producto) }}" 
       class="btn btn-info btn-sm"
       data-toggle="tooltip" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    
    <a href="{{ route('productos.edit', $producto) }}" 
       class="btn btn-warning btn-sm"
       data-toggle="tooltip" 
       title="Editar producto">
        <i class="fas fa-edit"></i>
    </a>
    
    <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="button" 
                class="btn btn-danger btn-sm" 
                onclick="confirmDelete(this, '¿Eliminar {{ $producto->nombre }}?')"
                data-toggle="tooltip" 
                title="Eliminar producto">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</td>
```

---

### 💡 Casos de Uso Recomendados

**Tooltips:**
- ✅ Botones con solo íconos (sin texto)
- ✅ Íconos de ayuda junto a campos del formulario
- ✅ Enlaces o botones con funciones no obvias
- ✅ Menús laterales para dar contexto adicional

**Confirmaciones:**
- ✅ Eliminar registros (usuarios, productos, ventas, etc.)
- ✅ Acciones irreversibles
- ✅ Cambios de estado importantes (activar/desactivar)
- ✅ Cancelar procesos en progreso

---

### 🔧 Personalización Avanzada

**Tooltip con HTML:**
```html
<button data-toggle="tooltip" 
        data-html="true" 
        title="<strong>Importante:</strong><br>Este cambio afectará a todos los usuarios">
    Aplicar cambios
</button>
```

**Modal de confirmación personalizado:**
```javascript
// Desde cualquier vista, puedes llamar:
confirmDelete(this, '¿Eliminar este elemento permanentemente?');
```

**Cerrar modal con ESC o click fuera:**
El modal ya tiene esta funcionalidad por defecto de Bootstrap 4.
