<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CheckoutBuild;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutCustomizationController extends Controller
{
    /**
     * Salvar customizações do checkout
     */
    public function saveCustomization(Request $request)
    {
        try {
            $checkoutId = $request->input('checkout_id');
            $componentOrder = $request->input('component_order', []);
            $themeConfig = $request->input('theme_config', []);
            
            $checkout = CheckoutBuild::find($checkoutId);
            
            if (!$checkout) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout não encontrado'
                ], 404);
            }
            
            // Atualizar configurações
            $checkout->update([
                'component_order' => $componentOrder,
                'layout_config' => $themeConfig,
                'drag_drop_enabled' => true,
                
                // Configurações de tema
                'primary_color' => $themeConfig['primary_color'] ?? $checkout->primary_color,
                'secondary_color' => $themeConfig['secondary_color'] ?? $checkout->secondary_color,
                'accent_color' => $themeConfig['accent_color'] ?? $checkout->accent_color,
                'text_color' => $themeConfig['text_color'] ?? $checkout->text_color,
                'background_color' => $themeConfig['background_color'] ?? $checkout->background_color,
                
                // Configurações de tipografia
                'font_family' => $themeConfig['font_family'] ?? $checkout->font_family,
                'font_size_base' => $themeConfig['font_size_base'] ?? $checkout->font_size_base,
                
                // Configurações de espaçamento
                'border_radius' => $themeConfig['border_radius'] ?? $checkout->border_radius,
                'padding_small' => $themeConfig['padding_small'] ?? $checkout->padding_small,
                'padding_medium' => $themeConfig['padding_medium'] ?? $checkout->padding_medium,
                'padding_large' => $themeConfig['padding_large'] ?? $checkout->padding_large,
                
                // Configurações de layout
                'layout_type' => $themeConfig['layout_type'] ?? $checkout->layout_type,
                'sidebar_enabled' => $themeConfig['sidebar_enabled'] ?? $checkout->sidebar_enabled,
                'sidebar_position' => $themeConfig['sidebar_position'] ?? $checkout->sidebar_position,
                'sidebar_width' => $themeConfig['sidebar_width'] ?? $checkout->sidebar_width,
                
                // Configurações de animação
                'animations_enabled' => $themeConfig['animations_enabled'] ?? $checkout->animations_enabled,
                'animation_duration' => $themeConfig['animation_duration'] ?? $checkout->animation_duration,
                'animation_easing' => $themeConfig['animation_easing'] ?? $checkout->animation_easing,
            ]);
            
            Log::info('Checkout customizado salvo:', [
                'checkout_id' => $checkoutId,
                'component_order' => $componentOrder,
                'theme_config' => $themeConfig
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Customizações salvas com sucesso!',
                'checkout' => $checkout
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao salvar customizações do checkout:', [
                'error' => $e->getMessage(),
                'checkout_id' => $request->input('checkout_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar customizações: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Preview do checkout customizado
     */
    public function preview($checkoutId)
    {
        try {
            $checkout = CheckoutBuild::find($checkoutId);
            
            if (!$checkout) {
                abort(404, 'Checkout não encontrado');
            }
            
            return view('profile.checkout.preview', compact('checkout'));
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar preview do checkout:', [
                'error' => $e->getMessage(),
                'checkout_id' => $checkoutId
            ]);
            
            abort(500, 'Erro ao gerar preview');
        }
    }
    
    /**
     * Resetar customizações para padrão
     */
    public function resetCustomization(Request $request)
    {
        try {
            $checkoutId = $request->input('checkout_id');
            
            $checkout = CheckoutBuild::find($checkoutId);
            
            if (!$checkout) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout não encontrado'
                ], 404);
            }
            
            // Resetar para configurações padrão
            $checkout->update([
                'component_order' => null,
                'layout_config' => null,
                'drag_drop_enabled' => false,
                
                'primary_color' => '#007bff',
                'secondary_color' => '#ffffff',
                'accent_color' => '#ff6b35',
                'text_color' => '#333333',
                'background_color' => '#f5f5f5',
                
                'font_family' => 'Inter',
                'font_size_base' => '16px',
                
                'border_radius' => '8px',
                'padding_small' => '8px',
                'padding_medium' => '16px',
                'padding_large' => '24px',
                
                'layout_type' => 'single-column',
                'sidebar_enabled' => false,
                'sidebar_position' => 'right',
                'sidebar_width' => 300,
                
                'animations_enabled' => true,
                'animation_duration' => '0.3s',
                'animation_easing' => 'ease-in-out',
            ]);
            
            Log::info('Checkout resetado para padrão:', [
                'checkout_id' => $checkoutId
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Checkout resetado para configurações padrão!',
                'checkout' => $checkout
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao resetar checkout:', [
                'error' => $e->getMessage(),
                'checkout_id' => $request->input('checkout_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao resetar checkout: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Exportar configurações do checkout
     */
    public function exportConfig($checkoutId)
    {
        try {
            $checkout = CheckoutBuild::find($checkoutId);
            
            if (!$checkout) {
                abort(404, 'Checkout não encontrado');
            }
            
            $config = [
                'theme_name' => $checkout->theme_name,
                'primary_color' => $checkout->primary_color,
                'secondary_color' => $checkout->secondary_color,
                'accent_color' => $checkout->accent_color,
                'text_color' => $checkout->text_color,
                'background_color' => $checkout->background_color,
                'font_family' => $checkout->font_family,
                'font_size_base' => $checkout->font_size_base,
                'border_radius' => $checkout->border_radius,
                'padding_small' => $checkout->padding_small,
                'padding_medium' => $checkout->padding_medium,
                'padding_large' => $checkout->padding_large,
                'layout_type' => $checkout->layout_type,
                'sidebar_enabled' => $checkout->sidebar_enabled,
                'sidebar_position' => $checkout->sidebar_position,
                'sidebar_width' => $checkout->sidebar_width,
                'animations_enabled' => $checkout->animations_enabled,
                'animation_duration' => $checkout->animation_duration,
                'animation_easing' => $checkout->animation_easing,
                'component_order' => $checkout->component_order,
                'layout_config' => $checkout->layout_config,
            ];
            
            return response()->json([
                'success' => true,
                'config' => $config
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao exportar configurações:', [
                'error' => $e->getMessage(),
                'checkout_id' => $checkoutId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar configurações: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Importar configurações do checkout
     */
    public function importConfig(Request $request)
    {
        try {
            $checkoutId = $request->input('checkout_id');
            $config = $request->input('config', []);
            
            $checkout = CheckoutBuild::find($checkoutId);
            
            if (!$checkout) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout não encontrado'
                ], 404);
            }
            
            // Aplicar configurações importadas
            $checkout->update($config);
            
            Log::info('Configurações importadas para checkout:', [
                'checkout_id' => $checkoutId,
                'config' => $config
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Configurações importadas com sucesso!',
                'checkout' => $checkout
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao importar configurações:', [
                'error' => $e->getMessage(),
                'checkout_id' => $request->input('checkout_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar configurações: ' . $e->getMessage()
            ], 500);
        }
    }
    
}
