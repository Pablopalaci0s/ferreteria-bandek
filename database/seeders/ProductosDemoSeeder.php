<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductosDemoSeeder extends Seeder
{
    /**
     * Carga 10 productos de ejemplo (sin imagen, se agregan después desde el admin).
     * Crea las categorías, marcas y unidades de medida que hagan falta,
     * reutilizando las que ya existan (no duplica nada).
     */
    public function run(): void
    {
        $categorias = collect([
            'Herramientas',
            'Pintura',
            'Eléctrico',
            'Ferretería General',
        ])->mapWithKeys(function ($nombre) {
            $categoria = Categoria::firstOrCreate(
                ['nombre' => $nombre],
                ['slug' => Str::slug($nombre), 'activo' => true]
            );

            return [$nombre => $categoria->id];
        });

        $marcas = collect([
            'Truper',
            'Pretul',
            'Sherwin Williams',
            'Philips',
        ])->mapWithKeys(function ($nombre) {
            $marca = Marca::firstOrCreate(
                ['nombre' => $nombre],
                ['slug' => Str::slug($nombre), 'activo' => true]
            );

            return [$nombre => $marca->id];
        });

        $unidades = collect([
            'Unidad' => 'und',
            'Par' => 'par',
            'Galón' => 'gal',
            'Caja' => 'cja',
        ])->mapWithKeys(function ($abreviatura, $nombre) {
            $unidad = UnidadMedida::firstOrCreate(
                ['abreviatura' => $abreviatura],
                ['nombre' => $nombre]
            );

            return [$nombre => $unidad->id];
        });

        $productos = [
            [
                'sku' => 'HER-TAL-001',
                'nombre' => 'Taladro percutor 1/2" 550W',
                'descripcion' => 'Taladro percutor de uso doméstico y profesional, mango ergonómico.',
                'precio' => 45.99,
                'costo' => 30.00,
                'stock' => 8,
                'stock_minimo' => 2,
                'categoria' => 'Herramientas',
                'marca' => 'Truper',
                'unidad' => 'Unidad',
                'destacado' => true,
            ],
            [
                'sku' => 'HER-MAR-002',
                'nombre' => 'Martillo de uña 16oz mango fibra',
                'descripcion' => 'Martillo de uña con mango de fibra de vidrio, cabeza forjada.',
                'precio' => 8.50,
                'costo' => 5.00,
                'stock' => 20,
                'stock_minimo' => 5,
                'categoria' => 'Herramientas',
                'marca' => 'Truper',
                'unidad' => 'Unidad',
                'destacado' => false,
            ],
            [
                'sku' => 'HER-LLA-003',
                'nombre' => 'Juego de llaves mixtas 8-19mm (12 pzas)',
                'descripcion' => 'Set de 12 llaves mixtas en acero cromo vanadio, estuche incluido.',
                'precio' => 22.00,
                'costo' => 14.00,
                'stock' => 12,
                'stock_minimo' => 3,
                'categoria' => 'Herramientas',
                'marca' => 'Pretul',
                'unidad' => 'Unidad',
                'destacado' => true,
            ],
            [
                'sku' => 'HER-CIN-004',
                'nombre' => 'Cinta métrica 5m',
                'descripcion' => 'Cinta métrica con carcasa antideslizante y freno automático.',
                'precio' => 4.25,
                'costo' => 2.50,
                'stock' => 30,
                'stock_minimo' => 8,
                'categoria' => 'Herramientas',
                'marca' => 'Truper',
                'unidad' => 'Unidad',
                'destacado' => false,
            ],
            [
                'sku' => 'HER-GUA-005',
                'nombre' => 'Guantes de trabajo reforzados',
                'descripcion' => 'Par de guantes de trabajo con palma reforzada, talla única.',
                'precio' => 3.75,
                'costo' => 2.00,
                'stock' => 40,
                'stock_minimo' => 10,
                'categoria' => 'Herramientas',
                'marca' => 'Pretul',
                'unidad' => 'Par',
                'destacado' => false,
            ],
            [
                'sku' => 'PIN-LAT-006',
                'nombre' => 'Pintura látex blanca 1 galón',
                'descripcion' => 'Pintura látex interior/exterior, acabado mate, alto rendimiento.',
                'precio' => 18.99,
                'costo' => 12.50,
                'stock' => 15,
                'stock_minimo' => 4,
                'categoria' => 'Pintura',
                'marca' => 'Sherwin Williams',
                'unidad' => 'Galón',
                'destacado' => true,
            ],
            [
                'sku' => 'PIN-BRO-007',
                'nombre' => 'Brocha para pintar 3"',
                'descripcion' => 'Brocha de cerda sintética, mango de madera.',
                'precio' => 2.10,
                'costo' => 1.00,
                'stock' => 25,
                'stock_minimo' => 6,
                'categoria' => 'Pintura',
                'marca' => 'Pretul',
                'unidad' => 'Unidad',
                'destacado' => false,
            ],
            [
                'sku' => 'ELE-CAB-008',
                'nombre' => 'Cable eléctrico THHN #12 (rollo 100m)',
                'descripcion' => 'Cable THHN calibre 12 AWG, rollo de 100 metros, color negro.',
                'precio' => 65.00,
                'costo' => 48.00,
                'stock' => 6,
                'stock_minimo' => 2,
                'categoria' => 'Eléctrico',
                'marca' => 'Philips',
                'unidad' => 'Unidad',
                'destacado' => false,
            ],
            [
                'sku' => 'FER-CAN-009',
                'nombre' => 'Candado de seguridad 50mm',
                'descripcion' => 'Candado de acero con arco endurecido, incluye 2 llaves.',
                'precio' => 6.80,
                'costo' => 4.20,
                'stock' => 18,
                'stock_minimo' => 5,
                'categoria' => 'Ferretería General',
                'marca' => 'Truper',
                'unidad' => 'Unidad',
                'destacado' => false,
            ],
            [
                'sku' => 'FER-TOR-010',
                'nombre' => 'Caja de tornillos para madera 1" (100 unidades)',
                'descripcion' => 'Tornillos para madera punta fina, caja de 100 piezas.',
                'precio' => 5.50,
                'costo' => 3.00,
                'stock' => 22,
                'stock_minimo' => 6,
                'categoria' => 'Ferretería General',
                'marca' => 'Pretul',
                'unidad' => 'Caja',
                'destacado' => false,
            ],
        ];

        foreach ($productos as $datos) {

            if (Producto::where('sku', $datos['sku'])->exists()) {
                continue;
            }

            Producto::create([
                'sku' => $datos['sku'],
                'nombre' => $datos['nombre'],
                'slug' => Str::slug($datos['nombre']) . '-' . uniqid(),
                'descripcion' => $datos['descripcion'],
                'precio' => $datos['precio'],
                'costo' => $datos['costo'],
                'stock' => $datos['stock'],
                'stock_minimo' => $datos['stock_minimo'],
                'categoria_id' => $categorias[$datos['categoria']],
                'marca_id' => $marcas[$datos['marca']],
                'unidad_medida_id' => $unidades[$datos['unidad']],
                'activo' => true,
                'destacado' => $datos['destacado'],
            ]);
        }
    }
}
