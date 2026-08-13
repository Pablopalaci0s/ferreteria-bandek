<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProductos = Producto::where('activo', true)->count();

        // Productos por debajo (o en) su stock mínimo configurado.
        $productosStockBajo = Producto::where('activo', true)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->get();

        $movimientosDelMes = MovimientoInventario::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $movimientosRecientes = MovimientoInventario::with(['producto', 'usuario'])
            ->latest()
            ->take(5)
            ->get();

        $totalUsuarios = User::count();
        $totalAdmins = User::where('rol', 'admin')->count();
        $totalVendedores = User::where('rol', 'vendedor')->count();

        return view('admin.dashboard', compact(
            'totalProductos',
            'productosStockBajo',
            'movimientosDelMes',
            'movimientosRecientes',
            'totalUsuarios',
            'totalAdmins',
            'totalVendedores',
        ));
    }
}
