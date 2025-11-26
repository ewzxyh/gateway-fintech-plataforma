<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GatewayWallet;
use App\Models\SolicitacoesCashOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GatewayWalletController extends Controller
{
    /**
     * Exibe a página de gerenciamento da carteira gateway
     */
    public function index(Request $request)
    {
        $periodo = $request->input('periodo', 'tudo');
        
        // Filtros de datas (mesma lógica do DashboardController)
        $dataInicio = null;
        $dataFim = null;

        switch ($periodo) {
            case 'hoje':
                $dataInicio = Carbon::today()->startOfDay();
                $dataFim = Carbon::today()->endOfDay();
                break;
            case 'ontem':
                $dataInicio = Carbon::yesterday()->startOfDay();
                $dataFim = Carbon::yesterday()->endOfDay();
                break;
            case '7dias':
                $dataInicio = Carbon::today()->subDays(6)->startOfDay();
                $dataFim = Carbon::today()->endOfDay();
                break;
            case '30dias':
                $dataInicio = Carbon::today()->subDays(29)->startOfDay();
                $dataFim = Carbon::today()->endOfDay();
                break;
            case 'tudo':
                $dataInicio = Carbon::today()->subYears(100)->startOfDay();
                $dataFim = Carbon::today()->endOfDay();
                break;
            default:
                if (Str::contains($periodo, ':')) {
                    [$start, $end] = explode(':', $periodo);
                    $dataInicio = Carbon::parse($start)->startOfDay();
                    $dataFim = Carbon::parse($end)->endOfDay();
                } else {
                    $dataInicio = Carbon::today()->startOfDay();
                    $dataFim = Carbon::today()->endOfDay();
                }
                break;
        }

        // === CARTEIRA GATEWAY ===
        // Buscar ou criar registro da carteira gateway para hoje
        $gatewayWallet = GatewayWallet::buscarOuCriar(
            Carbon::today()->format('Y-m-d'),
            Carbon::today()->format('Y-m-d')
        );
        
        // Recalcular valores atualizados
        $gatewayWalletData = GatewayWallet::calcularSaldoTotal();
        
        // Atualizar o objeto com os dados calculados
        $gatewayWallet->taxa_sistema_total = $gatewayWalletData['taxa_sistema_total'];
        $gatewayWallet->taxa_adquirente_total = $gatewayWalletData['taxa_adquirente_total'];
        $gatewayWallet->saldo_gateway = $gatewayWalletData['saldo_gateway_total'];
        $gatewayWallet->saldo_disponivel = $gatewayWalletData['saldo_disponivel'];
        $gatewayWallet->saldo_pendente = $gatewayWalletData['saldo_pendente'];
        $gatewayWallet->total_sacado = $gatewayWalletData['total_sacado'];
        $gatewayWallet->save(); // Salvar no banco de dados
        
        // === SINCRONIZAR SALDO DO USUÁRIO GATEWAY_WALLET ===
        // Garantir que o saldo do usuário esteja sempre sincronizado com a carteira gateway
        $gatewayWallet->sincronizarSaldoUsuario();

        // Buscar saques pendentes da carteira gateway
        $saquesPendentes = SolicitacoesCashOut::where('user_id', 'gateway_wallet')
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')
            ->get();

        // Buscar adquirentes disponíveis para saque PIX
        $adquirentes = \App\Models\Adquirente::whereNotIn('referencia', ['sixxpayments', 'mercadopago', 'efi', 'syscoop', 'asaas'])
            ->orderBy('adquirente')
            ->get();

        return view('admin.gateway-wallet.index', compact('gatewayWallet', 'saquesPendentes', 'periodo', 'adquirentes'));
    }

    /**
     * Processa um saque da carteira gateway
     */
    public function sacar(Request $request)
    {
        $request->validate([
            'valor' => 'required|numeric|min:0.01',
            'pix_key' => 'required|string',
            'pix_key_type' => 'required|string|in:cpf,cnpj,email,phone,random',
            'adquirente' => 'required|string',
            'observacoes' => 'nullable|string|max:500'
        ]);

        // === VALIDAÇÕES PRELIMINARES ===
        
        // 1. Verificar usuário gateway_wallet
        $gatewayUser = \App\Models\User::where('user_id', 'gateway_wallet')->first();
        if (!$gatewayUser) {
            return back()->with('error', 'Usuário gateway_wallet não encontrado no sistema.');
        }

        // 2. Buscar carteira gateway atual
        $gatewayWallet = GatewayWallet::buscarOuCriar(
            Carbon::today()->format('Y-m-d'),
            Carbon::today()->format('Y-m-d')
        );

        $valor = (float) $request->valor;

        // 3. Verificar se há saldo disponível
        if ($gatewayWallet->saldo_disponivel < $valor) {
            return back()->with('error', 
                'Saldo insuficiente na carteira gateway. ' .
                'Disponível: R$ ' . number_format($gatewayWallet->saldo_disponivel, 2, ',', '.') . ' ' .
                'Solicitado: R$ ' . number_format($valor, 2, ',', '.')
            );
        }

        // 4. Log da tentativa de saque
        \Log::info('Tentativa de saque da carteira gateway', [
            'valor' => $valor,
            'pix_key' => $request->pix_key,
            'pix_key_type' => $request->pix_key_type,
            'saldo_disponivel' => $gatewayWallet->saldo_disponivel,
            'user_id' => auth()->id()
        ]);

        try {
            // === SINCRONIZAR SALDO DO USUÁRIO GATEWAY_WALLET ===
            // Garantir que o saldo do usuário esteja sempre sincronizado com a carteira gateway
            $gatewayWallet->sincronizarSaldoUsuario();
            
            \Log::info('Saldo do usuário gateway_wallet sincronizado', [
                'saldo_carteira_gateway' => $gatewayWallet->saldo_disponivel,
                'saldo_usuario_sincronizado' => $gatewayUser->fresh()->saldo
            ]);

            // === USAR A MESMA LÓGICA DOS SAQUES PIX NORMAIS ===
            // Criar request para usar com os Traits existentes
            $saqueRequest = new \Illuminate\Http\Request();
            $saqueRequest->merge([
                'amount' => $valor,
                'pixKey' => $request->pix_key,
                'pixKeyType' => $request->pix_key_type,
                'baasPostbackUrl' => 'web',
                'saque_automatico' => true,
                'observacoes' => $request->observacoes ?? 'Saque da Carteira Gateway'
            ]);

            // Configurar usuário gateway_wallet no request (recarregar para ter o saldo atualizado)
            $gatewayUser = $gatewayUser->fresh();
            $saqueRequest->setUserResolver(function () use ($gatewayUser) {
                return $gatewayUser;
            });

            // Usar o adquirente selecionado pelo usuário
            $adquirenteSelecionado = $request->adquirente;
            
            \Log::info('Processando saque da carteira gateway', [
                'valor' => $valor,
                'pix_key' => $request->pix_key,
                'pix_key_type' => $request->pix_key_type,
                'adquirente_selecionado' => $adquirenteSelecionado,
                'user_id' => $gatewayUser->username
            ]);

            // Processar saque usando o trait correspondente ao adquirente selecionado
            $response = null;
            switch ($adquirenteSelecionado) {
                case 'woovi':
                    $response = \App\Traits\WooviTrait::requestSaqueWoovi($saqueRequest);
                    break;
                case 'pixup':
                    $response = \App\Traits\PixupTrait::requestPaymentPixup($saqueRequest);
                    break;
                case 'asaas':
                    $response = \App\Traits\AsaasTrait::requestPaymentAsaas($saqueRequest);
                    break;
                case 'pagarme':
                    $response = \App\Traits\PagarMeTrait::requestPaymentPagarme($saqueRequest);
                    break;
                case 'mercadopago':
                    $response = \App\Traits\MercadoPagoTrait::requestPaymentMercadoPago($saqueRequest);
                    break;
                case 'efi':
                    $response = \App\Traits\EfiTrait::requestPaymentEfi($saqueRequest);
                    break;
                case 'xgate':
                    $response = \App\Traits\XgateTrait::requestPaymentXgate($saqueRequest);
                    break;
                case 'witetec':
                    $response = \App\Traits\WitetecTrait::requestPaymentWitetec($saqueRequest);
                    break;
                case 'bsPay':
                    $response = \App\Traits\BSPayTrait::requestPaymentBSPay($saqueRequest);
                    break;
                case 'cashtime':
                    $response = \App\Traits\CashtimeTrait::requestPaymentCashtime($saqueRequest);
                    break;
                case 'primepay7':
                    $response = \App\Traits\PrimePay7Trait::requestPaymentPrimePay7($saqueRequest);
                    break;
                case 'trustypix':
                    $response = \App\Traits\TrustyPixTrait::requestPaymentTrustyPix($saqueRequest);
                    break;
                default:
                    return back()->with('error', 'Adquirente não suportado: ' . $adquirenteSelecionado);
            }

            \Log::info('Resposta do trait para saque da carteira gateway', [
                'status' => $response['status'],
                'response_data' => $response['data'] ?? null,
                'adquirente' => $adquirenteSelecionado
            ]);

            if ($response['status'] === 200) {
                // Sucesso - saque processado
                $transactionData = $response['data']['transaction'] ?? null;
                
                // === ATUALIZAR CARTEIRA GATEWAY ===
                // Descontar o valor do saque do saldo disponível da carteira gateway
                $gatewayWallet->saldo_disponivel -= $valor;
                $gatewayWallet->total_sacado += $valor;
                $gatewayWallet->save();
                
                // === SINCRONIZAR SALDO DO USUÁRIO APÓS O SAQUE ===
                $gatewayWallet->sincronizarSaldoUsuario();
                
                \Log::info('Saque da carteira gateway processado com sucesso', [
                    'valor' => $valor,
                    'transaction_id' => $transactionData['id'] ?? 'N/A',
                    'adquirente' => $adquirenteSelecionado,
                    'saldo_disponivel_antes' => $gatewayWallet->saldo_disponivel + $valor,
                    'saldo_disponivel_depois' => $gatewayWallet->saldo_disponivel,
                    'total_sacado' => $gatewayWallet->total_sacado,
                    'saldo_usuario_sincronizado' => $gatewayUser->fresh()->saldo
                ]);
                
                return back()->with('success', 
                    'Saque processado com sucesso! ' .
                    'Valor: R$ ' . number_format($valor, 2, ',', '.') . ' ' .
                    'Status: COMPLETED ' .
                    ($transactionData ? 'ID: ' . $transactionData['id'] : '') . ' ' .
                    'Adquirente: ' . strtoupper($adquirenteSelecionado) . ' ' .
                    'Saldo restante: R$ ' . number_format($gatewayWallet->saldo_disponivel, 2, ',', '.')
                );
            } else {
                // Erro no processamento
                $errorMessage = $response['data']['message'] ?? 'Erro desconhecido';
                
                \Log::error('Erro no saque da carteira gateway', [
                    'status' => $response['status'],
                    'error_message' => $errorMessage,
                    'valor' => $valor,
                    'pix_key' => $request->pix_key,
                    'adquirente' => $adquirenteSelecionado
                ]);
                
                return back()->with('error', 
                    'Erro ao processar saque: ' . $errorMessage . ' ' .
                    '(Adquirente: ' . strtoupper($adquirenteSelecionado) . ')'
                );
            }

        } catch (\Exception $e) {
            \Log::error('Exceção no saque da carteira gateway', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'valor' => $valor,
                'pix_key' => $request->pix_key
            ]);
            
            return back()->with('error', 
                'Erro interno ao processar saque. Tente novamente ou entre em contato com o suporte. ' .
                'Detalhes: ' . $e->getMessage()
            );
        }
    }

    /**
     * Confirma um saque pendente
     */
    public function confirmarSaque($id)
    {
        $solicitacao = SolicitacoesCashOut::where('id', $id)
            ->where('user_id', 'gateway_wallet')
            ->where('status', 'PENDING')
            ->first();

        if (!$solicitacao) {
            return back()->with('error', 'Solicitação não encontrada ou já processada.');
        }

        // Atualizar status da solicitação
        $solicitacao->update([
            'status' => 'COMPLETED',
            'updated_at' => Carbon::now()
        ]);

        // Confirmar saque na carteira gateway
        $gatewayWallet = GatewayWallet::buscarOuCriar(
            Carbon::today()->format('Y-m-d'),
            Carbon::today()->format('Y-m-d')
        );

        $gatewayWallet->confirmarSaque($solicitacao->amount);

        return back()->with('success', 'Saque confirmado com sucesso!');
    }

    /**
     * Cancela um saque pendente
     */
    public function cancelarSaque($id)
    {
        $solicitacao = SolicitacoesCashOut::where('id', $id)
            ->where('user_id', 'gateway_wallet')
            ->where('status', 'PENDING')
            ->first();

        if (!$solicitacao) {
            return back()->with('error', 'Solicitação não encontrada ou já processada.');
        }

        // Devolver valor para saldo disponível
        $gatewayWallet = GatewayWallet::buscarOuCriar(
            Carbon::today()->format('Y-m-d'),
            Carbon::today()->format('Y-m-d')
        );

        $gatewayWallet->saldo_pendente -= $solicitacao->amount;
        $gatewayWallet->saldo_disponivel += $solicitacao->amount;
        $gatewayWallet->save();

        // Atualizar status da solicitação
        $solicitacao->update([
            'status' => 'CANCELLED',
            'updated_at' => Carbon::now()
        ]);

        return back()->with('success', 'Saque cancelado e valor devolvido ao saldo disponível.');
    }

    /**
     * Atualiza o saldo da carteira gateway manualmente
     */
    public function atualizarSaldo(Request $request)
    {
        $request->validate([
            'valor' => 'required|numeric'
        ]);

        $gatewayWallet = GatewayWallet::buscarOuCriar(
            Carbon::today()->format('Y-m-d'),
            Carbon::today()->format('Y-m-d')
        );

        $gatewayWallet->adicionarSaldo((float) $request->valor);

        return back()->with('success', 'Saldo atualizado com sucesso!');
    }
}