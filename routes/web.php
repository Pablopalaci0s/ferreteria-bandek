<?php

use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', [CatalogoController::class, 'inicio'])
    ->name('inicio');


Route::get('/catalogo', [CatalogoController::class, 'index'])
    ->name('catalogo.index');


Route::get('/catalogo/buscar', [CatalogoController::class, 'buscar'])
    ->name('catalogo.buscar');


Route::get('/producto/{producto}', [CatalogoController::class, 'show'])
    ->name('catalogo.show');


Route::view('/nosotros', 'nosotros')
    ->name('nosotros');


Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])
    ->name('sitemap');


Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [
            \App\Http\Controllers\Admin\DashboardController::class,
            'index'
        ])->name('dashboard');


        Route::resource(
            'categorias',
            \App\Http\Controllers\Admin\CategoriaController::class
        );


        Route::resource(
            'marcas',
            \App\Http\Controllers\Admin\MarcaController::class
        );


        Route::resource(
            'proveedores',
            \App\Http\Controllers\Admin\ProveedorController::class
        )->parameters([
            'proveedores' => 'proveedor',
        ]);


        Route::get('productos/importar', [
            \App\Http\Controllers\Admin\ImportarProductosController::class,
            'formulario'
        ])->name('productos.importar');


        Route::post('productos/importar', [
            \App\Http\Controllers\Admin\ImportarProductosController::class,
            'procesar'
        ])->name('productos.importar.procesar');


        Route::get('productos/plantilla', [
            \App\Http\Controllers\Admin\ImportarProductosController::class,
            'plantilla'
        ])->name('productos.plantilla');


        Route::resource(
            'productos',
            \App\Http\Controllers\Admin\ProductoController::class
        );


        Route::post('productos/{producto}/imagenes', [
            \App\Http\Controllers\Admin\ProductoImagenController::class,
            'store'
        ])->name('productos.imagenes.store');


        Route::delete('productos/{producto}/imagenes/{imagen}', [
            \App\Http\Controllers\Admin\ProductoImagenController::class,
            'destroy'
        ])->name('productos.imagenes.destroy');


        Route::resource(
            'banners',
            \App\Http\Controllers\Admin\BannerController::class
        );


        Route::get('inventario', [
            \App\Http\Controllers\Admin\InventarioController::class,
            'index'
        ])->name('inventario.index');


        Route::get('inventario/nuevo', [
            \App\Http\Controllers\Admin\InventarioController::class,
            'create'
        ])->name('inventario.create');


        Route::post('inventario', [
            \App\Http\Controllers\Admin\InventarioController::class,
            'store'
        ])->name('inventario.store');


        Route::middleware('admin')->group(function () {

            Route::get('usuarios', [
                \App\Http\Controllers\Admin\UsuarioController::class,
                'index'
            ])->name('usuarios.index');


            Route::get('usuarios/create', [
                \App\Http\Controllers\Admin\UsuarioController::class,
                'create'
            ])->name('usuarios.create');


            Route::post('usuarios', [
                \App\Http\Controllers\Admin\UsuarioController::class,
                'store'
            ])->name('usuarios.store');


            Route::get('usuarios/{usuario}/edit', [
                \App\Http\Controllers\Admin\UsuarioController::class,
                'edit'
            ])->name('usuarios.edit');


            Route::put('usuarios/{usuario}', [
                \App\Http\Controllers\Admin\UsuarioController::class,
                'update'
            ])->name('usuarios.update');


            Route::delete('usuarios/{usuario}', [
                \App\Http\Controllers\Admin\UsuarioController::class,
                'destroy'
            ])->name('usuarios.destroy');
        });
    });
});


require __DIR__.'/auth.php';

