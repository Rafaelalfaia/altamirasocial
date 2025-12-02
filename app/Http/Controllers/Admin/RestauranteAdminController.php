<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendaRestaurante;
use Illuminate\Http\Request;

class RestauranteAdminController extends Controller
{
    public function index()
    {
        $vendas = VendaRestaurante::with('cidadao')->latest()->paginate(15);
        return view('admin.restaurante.index', compact('vendas'));
    }

    public function entrar($id)
    {
        session(['impersonate_admin_id' => auth()->id()]);
        session(['impersonate_restaurante_id' => $id]);
        Auth::loginUsingId($id); // ou outro critério, se restaurante tiver user_id

        return redirect()->route('restaurante.dashboard')->with('success', 'Você entrou como restaurante.');
    }

}
