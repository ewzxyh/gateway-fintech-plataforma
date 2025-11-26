<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Calcula a cor de contraste baseada na luminosidade da cor de fundo
     * 
     * @param string $backgroundColor Cor de fundo em formato hex (#ffffff)
     * @param float $opacity Opacidade da cor (0.0 a 1.0)
     * @return string Cor de contraste em formato hex
     */
    public static function getContrastColor($backgroundColor, $opacity = 1.0)
    {
        // Remove o # se presente
        $backgroundColor = ltrim($backgroundColor, '#');
        
        // Converte hex para RGB
        $r = hexdec(substr($backgroundColor, 0, 2));
        $g = hexdec(substr($backgroundColor, 2, 2));
        $b = hexdec(substr($backgroundColor, 4, 2));
        
        // Calcula a luminosidade usando a fórmula padrão
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        // Se a luminosidade for menor que 0.5, usa branco, senão usa preto
        $contrastColor = $luminance < 0.5 ? '#ffffff' : '#000000';
        
        // Aplica opacidade se necessário
        if ($opacity < 1.0) {
            $contrastColor = self::applyOpacity($contrastColor, $opacity);
        }
        
        return $contrastColor;
    }
    
    /**
     * Aplica opacidade a uma cor hex
     * 
     * @param string $hexColor Cor em formato hex
     * @param float $opacity Opacidade (0.0 a 1.0)
     * @return string Cor com opacidade aplicada
     */
    private static function applyOpacity($hexColor, $opacity)
    {
        // Remove o # se presente
        $hexColor = ltrim($hexColor, '#');
        
        // Converte hex para RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Aplica opacidade
        $r = round($r * $opacity);
        $g = round($g * $opacity);
        $b = round($b * $opacity);
        
        // Converte de volta para hex
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
    
    /**
     * Calcula uma cor de fundo mais clara ou escura baseada na cor original
     * 
     * @param string $backgroundColor Cor de fundo em formato hex
     * @param float $factor Fator de ajuste (-1.0 a 1.0, negativo escurece, positivo clareia)
     * @return string Cor ajustada em formato hex
     */
    public static function adjustBrightness($backgroundColor, $factor)
    {
        // Remove o # se presente
        $backgroundColor = ltrim($backgroundColor, '#');
        
        // Converte hex para RGB
        $r = hexdec(substr($backgroundColor, 0, 2));
        $g = hexdec(substr($backgroundColor, 2, 2));
        $b = hexdec(substr($backgroundColor, 4, 2));
        
        // Aplica o fator de ajuste
        $r = max(0, min(255, $r + ($factor * 255)));
        $g = max(0, min(255, $g + ($factor * 255)));
        $b = max(0, min(255, $b + ($factor * 255)));
        
        // Converte de volta para hex
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
    
    /**
     * Verifica se uma cor é clara ou escura
     * 
     * @param string $backgroundColor Cor de fundo em formato hex
     * @return bool true se for clara, false se for escura
     */
    public static function isLightColor($backgroundColor)
    {
        // Remove o # se presente
        $backgroundColor = ltrim($backgroundColor, '#');
        
        // Converte hex para RGB
        $r = hexdec(substr($backgroundColor, 0, 2));
        $g = hexdec(substr($backgroundColor, 2, 2));
        $b = hexdec(substr($backgroundColor, 4, 2));
        
        // Calcula a luminosidade
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance >= 0.5;
    }
}

