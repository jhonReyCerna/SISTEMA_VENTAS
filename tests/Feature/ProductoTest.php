<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_crear_editar_y_eliminar_producto()
    {
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create();

        // Crear producto
        $response = $this->actingAs($user)->post('/productos', [
            'id_categoria' => $categoria->id_categoria,
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio_compra' => 10.5,
            'precio_venta' => 15.0,
            'stock' => 100,
            'stock_minimo' => 10,
            'estado' => 'ACTIVO',
        ]);
        $response->assertRedirect('/productos');
        $this->assertDatabaseHas('productos', ['nombre' => 'Producto Test']);

        $producto = Producto::where('nombre', 'Producto Test')->first();

        // Editar producto
        $response = $this->actingAs($user)->put('/productos/' . $producto->id_producto, [
            'id_categoria' => $categoria->id_categoria,
            'nombre' => 'Producto Editado',
            'descripcion' => 'Descripción editada',
            'precio_compra' => 12.0,
            'precio_venta' => 18.0,
            'stock' => 90,
            'stock_minimo' => 5,
            'estado' => 'ACTIVO',
        ]);
        $response->assertRedirect('/productos');
        $this->assertDatabaseHas('productos', ['nombre' => 'Producto Editado']);

        // Eliminar producto
        $response = $this->actingAs($user)->delete('/productos/' . $producto->id_producto);
        $response->assertRedirect('/productos');
        $this->assertDatabaseMissing('productos', ['nombre' => 'Producto Editado']);
    }
}
