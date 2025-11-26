<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckoutBuild;
use App\Models\CheckoutOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpsellController extends Controller
{
    /**
     * Processar upsell
     */
    public function process(Request $request)
    {
        try {
            $produtoId = $request->input('produto_id');
            $redirectUrl = $request->input('redirect_url');
            
            if (!$produtoId) {
                return response()->json(['error' => 'ID do produto é obrigatório'], 400);
            }
            
            // Buscar produto upsell
            $produto = CheckoutBuild::where('id_unico', $produtoId)->first();
            
            if (!$produto) {
                return response()->json(['error' => 'Produto não encontrado'], 404);
            }
            
            // Aqui você pode implementar a lógica de processamento do upsell
            // Por exemplo: criar uma nova ordem, aplicar desconto, etc.
            
            Log::info('Upsell processado', [
                'produto_id' => $produtoId,
                'produto_nome' => $produto->produto_name,
                'produto_valor' => $produto->produto_valor,
                'redirect_url' => $redirectUrl,
                'user_id' => auth()->id()
            ]);
            
            // Redirecionar para URL especificada ou página de sucesso
            if ($redirectUrl && $redirectUrl !== '#') {
                return redirect($redirectUrl);
            }
            
            return redirect()->route('profile.checkout')->with('success', 'Upsell processado com sucesso!');
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar upsell: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Obter informações do produto upsell
     */
    public function getProduto($id)
    {
        try {
            $produto = CheckoutBuild::where('id_unico', $id)->first();
            
            if (!$produto) {
                return response()->json(['error' => 'Produto não encontrado'], 404);
            }
            
            return response()->json([
                'id' => $produto->id_unico,
                'nome' => $produto->produto_name,
                'valor' => $produto->produto_valor,
                'descricao' => $produto->produto_descricao
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar produto: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }
}

