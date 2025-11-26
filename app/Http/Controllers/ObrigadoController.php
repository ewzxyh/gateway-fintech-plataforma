<?php

namespace App\Http\Controllers;

use App\Models\CheckoutOrders;
use App\Models\CheckoutBuild;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CheckoutConfirmation;

class ObrigadoController extends Controller
{
    /**
     * Exibe a página de obrigado
     */
    public function index(Request $request)
    {
        $orderId = $request->get('order_id');
        $transactionId = $request->get('transaction_id') ?? $request->get('transaction');
        
        // Verificar se é preview
        if ($orderId === 'preview') {
            return $this->showPreview();
        }
        
        // Verificar se é ORDER_ID literal (template não substituído)
        if ($orderId === 'ORDER_ID') {
            return view('obrigado', [
                'order' => null,
                'checkout' => null,
                'user' => null,
                'route' => 'Pagamento Confirmado'
            ]);
        }
        
        // Buscar pedido por ID ou ID da transação
        $order = null;
        if ($orderId) {
            $order = CheckoutOrders::find($orderId);
        } elseif ($transactionId) {
            // Buscar por idTransaction (funciona para todas as adquirentes)
            Log::info('ObrigadoController - Buscando pedido:', ['transaction_id' => $transactionId]);
            $order = CheckoutOrders::where('idTransaction', $transactionId)->first();
            Log::info('ObrigadoController - Resultado da busca:', ['found' => $order ? 'Sim' : 'Não', 'order_id' => $order ? $order->id : null, 'status' => $order ? $order->status : null]);
        }
        
        if (!$order) {
            return view('obrigado', [
                'order' => null,
                'checkout' => null,
                'user' => null,
                'route' => 'Pagamento Confirmado'
            ]);
        }
        
        // Buscar checkout e usuário
        $checkout = CheckoutBuild::find($order->checkout_id);
        $user = null;
        
        if ($checkout) {
            $user = User::find($checkout->user_id);
        }
        
        // Log de acesso à página de obrigado
        Log::info('Acesso à página de obrigado:', [
            'order_id' => $order->id,
            'transaction_id' => $order->idTransaction,
            'status' => $order->status,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return view('obrigado', compact('order', 'checkout', 'user'))
            ->with('route', 'Pagamento Confirmado');
    }
    
    /**
     * Exibe preview da página de obrigado
     */
    private function showPreview()
    {
        // Criar dados de exemplo para preview
        $order = (object) [
            'id' => 999,
            'idTransaction' => 'PREVIEW_' . time(),
            'name' => 'João da Silva',
            'email' => 'redacted@example.invalid',
            'cpf' => '123.456.789-00',
            'telefone' => '(11) 99999-9999',
            'valor_total' => 99.90,
            'status' => 'pago',
            'created_at' => now()
        ];
        
        $checkout = (object) [
            'produto_name' => 'Produto de Exemplo',
            'produto_descricao' => 'Este é um produto de exemplo para demonstração',
            'produto_tipo' => 'digital',
            'whatsapp_suporte' => '5511999999999',
            'email_suporte' => 'redacted@example.invalid',
            'periodo_garantia' => '30 dias'
        ];
        
        $user = (object) [
            'name' => 'Vendedor Exemplo',
            'email' => 'redacted@example.invalid',
            'telefone' => '(11) 88888-8888'
        ];
        
        return view('obrigado', compact('order', 'checkout', 'user'))
            ->with('route', 'Preview - Pagamento Confirmado');
    }
    
    /**
     * Envia comprovante por email
     */
    public function enviarComprovante(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $email = $request->input('email');
            
            $order = CheckoutOrders::find($orderId);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não encontrado'
                ], 404);
            }
            
            // Buscar checkout e usuário
            $checkout = CheckoutBuild::find($order->checkout_id);
            $user = $checkout ? User::find($checkout->user_id) : null;
            
            // Enviar email de confirmação
            Mail::to($email)->send(new CheckoutConfirmation($order, $checkout, $user));
            
            Log::info('Comprovante enviado por email:', [
                'order_id' => $orderId,
                'email' => $email
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Comprovante enviado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar comprovante:', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar comprovante'
            ], 500);
        }
    }
    
    /**
     * Envia email de confirmação automático
     */
    public function enviarConfirmacao(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            
            $order = CheckoutOrders::find($orderId);
            
            if (!$order || $order->status !== 'pago') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não encontrado ou não pago'
                ], 404);
            }
            
            // Buscar checkout e usuário
            $checkout = CheckoutBuild::find($order->checkout_id);
            $user = $checkout ? User::find($checkout->user_id) : null;
            
            // Enviar email de confirmação
            Mail::to($order->email)->send(new CheckoutConfirmation($order, $checkout, $user));
            
            Log::info('Email de confirmação enviado:', [
                'order_id' => $orderId,
                'email' => $order->email
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Email de confirmação enviado'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar confirmação:', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar confirmação'
            ], 500);
        }
    }
    
    /**
     * Gera conteúdo do comprovante
     */
    private function gerarComprovante($order, $checkout)
    {
        $content = "========================================\n";
        $content .= "COMPROVANTE DE PAGAMENTO\n";
        $content .= "========================================\n\n";
        
        $content .= "Produto: " . ($checkout->produto_name ?? 'N/A') . "\n";
        $content .= "Valor: R$ " . number_format($order->valor_total, 2, ',', '.') . "\n\n";
        
        $content .= "Cliente: " . $order->name . "\n";
        $content .= "Email: " . $order->email . "\n";
        $content .= "CPF: " . $order->cpf . "\n";
        $content .= "Telefone: " . $order->telefone . "\n\n";
        
        $content .= "ID Transação: " . $order->idTransaction . "\n";
        $content .= "Data: " . $order->created_at->format('d/m/Y H:i') . "\n";
        $content .= "Status: PAGO\n\n";
        
        if ($checkout) {
            $content .= "Tipo de Produto: " . ($checkout->produto_tipo === 'digital' ? 'Digital' : 'Físico') . "\n";
            if ($checkout->periodo_garantia) {
                $content .= "Garantia: " . $checkout->periodo_garantia . "\n";
            }
        }
        
        $content .= "\n========================================\n";
        $content .= "Obrigado pela sua compra!\n";
        $content .= "========================================\n";
        
        return $content;
    }
    
    /**
     * Método estático para enviar email de confirmação de checkout
     */
    public static function enviarEmailConfirmacao($orderId)
    {
        try {
            $order = CheckoutOrders::find($orderId);
            
            if (!$order || $order->status !== 'pago') {
                Log::warning('Email de confirmação não enviado - pedido não encontrado ou não pago:', ['order_id' => $orderId]);
                return false;
            }
            
            // Buscar checkout e usuário
            $checkout = CheckoutBuild::find($order->checkout_id);
            $user = $checkout ? User::find($checkout->user_id) : null;
            
            // Enviar email de confirmação
            Mail::to($order->email)->send(new CheckoutConfirmation($order, $checkout, $user));
            
            Log::info('Email de confirmação de checkout enviado:', [
                'order_id' => $orderId,
                'email' => $order->email,
                'transaction_id' => $order->idTransaction
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de confirmação de checkout:', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            
            return false;
        }
    }
    
    /**
     * Envia email de checkout via API
     */
    public function enviarEmailCheckout(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $email = $request->input('email');
            
            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID do pedido é obrigatório'
                ], 400);
            }
            
            $order = CheckoutOrders::find($orderId);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não encontrado'
                ], 404);
            }
            
            // Se email específico foi fornecido, usar ele; senão usar o email do pedido
            $emailDestino = $email ?: $order->email;
            
            // Buscar checkout e usuário
            $checkout = CheckoutBuild::find($order->checkout_id);
            $user = $checkout ? User::find($checkout->user_id) : null;
            
            // Enviar email de confirmação
            Mail::to($emailDestino)->send(new CheckoutConfirmation($order, $checkout, $user));
            
            Log::info('Email de checkout enviado via API:', [
                'order_id' => $orderId,
                'email' => $emailDestino,
                'transaction_id' => $order->idTransaction
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Email de confirmação enviado com sucesso',
                'email' => $emailDestino
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de checkout via API:', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email de confirmação'
            ], 500);
        }
    }
}
