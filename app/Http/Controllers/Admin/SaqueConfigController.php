<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\App;

class SaqueConfigController extends Controller
{
    public function index()
    {
        $config = App::first();
        return view('admin.saque-config', compact('config'));
    }

    public function update(Request $request)
    {
        // Converter formato brasileiro para formato numérico antes da validação
        $limiteFormatado = str_replace(['.', ','], ['', '.'], $request->limite_saque_automatico);
        
        // Validar se o valor convertido é numérico
        if (!is_numeric($limiteFormatado) || $limiteFormatado < 0) {
            return back()->with('error', 'Por favor, informe um limite válido maior ou igual a zero.');
        }
        
        $request->validate([
            'saque_automatico' => 'boolean',
            'limite_saque_automatico' => 'required|min:0'
        ]);

        $config = App::first();
        if (!$config) {
            return back()->with('error', 'Configurações não encontradas.');
        }

        $config->update([
            'saque_automatico' => $request->has('saque_automatico'),
            'limite_saque_automatico' => (float) $limiteFormatado
        ]);

        return back()->with('success', 'Configurações de saque atualizadas com sucesso!');
    }
}
