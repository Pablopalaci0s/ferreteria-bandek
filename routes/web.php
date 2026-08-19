<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImportarProductosController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\ProductoImagenController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\SolicitudPrecioController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogoController::class, 'inicio'])
    ->name('inicio');

Route::get('/catalogo', [CatalogoController::class, 'index'])
    ->name('catalogo.index');

Route::get('/catalogo/buscar', [CatalogoController::class, 'buscar'])
    ->middleware('throttle:60,1')
    ->name('catalogo.buscar');

Route::get('/producto/{producto}', [CatalogoController::class, 'show'])
    ->name('catalogo.show');

Route::view('/nosotros', 'nosotros')
    ->name('nosotros');

Route::view('/privacidad', 'privacidad')
    ->name('privacidad');

Route::view('/terminos', 'terminos')
    ->name('terminos');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'obligar.password'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [
            DashboardController::class,
            'index',
        ])->name('dashboard');

        Route::resource(
            'categorias',
            CategoriaController::class
        );

        Route::resource(
            'marcas',
            MarcaController::class
        );

        Route::resource(
            'proveedores',
            ProveedorController::class
        )->parameters([
            'proveedores' => 'proveedor',
        ]);

        Route::get('productos/importar', [
            ImportarProductosController::class,
            'formulario',
        ])->name('productos.importar');

        Route::post('productos/importar', [
            ImportarProductosController::class,
            'procesar',
        ])->name('productos.importar.procesar');

        Route::get('productos/plantilla', [
            ImportarProductosController::class,
            'plantilla',
        ])->name('productos.plantilla');

        Route::resource(
            'productos',
            ProductoController::class
        );

        Route::post('productos/{producto}/imagenes', [
            ProductoImagenController::class,
            'store',
        ])->name('productos.imagenes.store');

        Route::delete('productos/{producto}/imagenes/{imagen}', [
            ProductoImagenController::class,
            'destroy',
        ])->name('productos.imagenes.destroy');

        Route::resource(
            'banners',
            BannerController::class
        );

        Route::get('ventas', [
            VentaController::class,
            'index',
        ])->name('ventas.index');

        Route::get('ventas/crear', [
            VentaController::class,
            'create',
        ])->name('ventas.create');

        Route::get('ventas/buscar-productos', [
            VentaController::class,
            'buscarProductos',
        ])->name('ventas.buscar-productos');

        Route::get('ventas/reportes', [
            VentaController::class,
            'reportes',
        ])->name('ventas.reportes');

        Route::post('ventas', [
            VentaController::class,
            'store',
        ])->name('ventas.store');

        Route::get('ventas/{venta}', [
            VentaController::class,
            'show',
        ])->name('ventas.show');

        Route::get('ventas/{venta}/ticket', [
            VentaController::class,
            'ticket',
        ])->name('ventas.ticket');

        Route::put('ventas/{venta}/confirmar', [
            VentaController::class,
            'confirmar',
        ])->name('ventas.confirmar');

        Route::put('ventas/{venta}/cancelar', [
            VentaController::class,
            'cancelar',
        ])->name('ventas.cancelar');

        Route::get('inventario', [
            InventarioController::class,
            'index',
        ])->name('inventario.index');

        Route::get('inventario/nuevo', [
            InventarioController::class,
            'create',
        ])->name('inventario.create');

        Route::post('inventario', [
            InventarioController::class,
            'store',
        ])->name('inventario.store');

        Route::middleware('admin')->group(function () {

            Route::get('configuracion', [
                ConfiguracionController::class,
                'edit',
            ])->name('configuracion.edit');

            Route::put('configuracion', [
                ConfiguracionController::class,
                'update',
            ])->name('configuracion.update');

            Route::get('solicitudes-precio', [
                SolicitudPrecioController::class,
                'index',
            ])->name('solicitudes-precio.index');

            Route::put('solicitudes-precio/{solicitud}/aprobar', [
                SolicitudPrecioController::class,
                'aprobar',
            ])->name('solicitudes-precio.aprobar');

            Route::put('solicitudes-precio/{solicitud}/rechazar', [
                SolicitudPrecioController::class,
                'rechazar',
            ])->name('solicitudes-precio.rechazar');

            Route::get('usuarios', [
                UsuarioController::class,
                'index',
            ])->name('usuarios.index');

            Route::get('usuarios/create', [
                UsuarioController::class,
                'create',
            ])->name('usuarios.create');

            Route::post('usuarios', [
                UsuarioController::class,
                'store',
            ])->name('usuarios.store');

            Route::get('usuarios/{usuario}/edit', [
                UsuarioController::class,
                'edit',
            ])->name('usuarios.edit');

            Route::put('usuarios/{usuario}', [
                UsuarioController::class,
                'update',
            ])->name('usuarios.update');

            Route::delete('usuarios/{usuario}', [
                UsuarioController::class,
                'destroy',
            ])->name('usuarios.destroy');

            Route::post('usuarios/{usuario}/reset-password', [
                UsuarioController::class,
                'resetPassword',
            ])->name('usuarios.reset-password');
        });
    });
});

require __DIR__.'/auth.php';
