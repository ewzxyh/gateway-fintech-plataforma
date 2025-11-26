# Sistema de Cores Dinâmico - Checkout Builder

## 🎨 Como Funciona

O sistema de cores dinâmico calcula automaticamente as cores de contraste baseado na cor de fundo escolhida no checkout builder.

### 📋 Regras Automáticas:

- **Fundo ESCURO** → Texto fica **BRANCO**
- **Fundo CLARO** → Texto fica **PRETO**

## 🔧 Implementação

### 1. Helper ColorHelper.php
```php
// Calcula cor de contraste baseada na luminosidade
ColorHelper::getContrastColor($backgroundColor, $opacity)

// Ajusta brilho de uma cor
ColorHelper::adjustBrightness($backgroundColor, $factor)

// Verifica se cor é clara ou escura
ColorHelper::isLightColor($backgroundColor)
```

### 2. Variáveis CSS Dinâmicas
```css
:root {
  /* Cores dinâmicas baseadas na luminosidade do fundo */
  --text-primary: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color) }};
  --text-secondary: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color, 0.8) }};
  --text-muted: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color, 0.6) }};
  --border-color: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color, 0.3) }};
  --border-light: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color, 0.2) }};
}
```

## 🎯 Variáveis Disponíveis

| Variável | Descrição | Opacidade |
|----------|-----------|-----------|
| `--text-primary` | Cor principal do texto | 100% |
| `--text-secondary` | Cor secundária | 80% |
| `--text-muted` | Cor suave | 60% |
| `--border-color` | Cor das bordas | 30% |
| `--border-light` | Cor das bordas claras | 20% |

## 📐 Fórmula de Luminosidade

```
Luminosidade = (0.299 × R + 0.587 × G + 0.114 × B) ÷ 255
```

- Se luminosidade < 0.5 → Usa texto branco
- Se luminosidade ≥ 0.5 → Usa texto preto

## 🚀 Como Usar

1. **No Checkout Builder**: Escolha qualquer cor de fundo
2. **Sistema Automático**: As cores de texto se ajustam automaticamente
3. **Resultado**: Sempre terá contraste perfeito para legibilidade

## ✅ Benefícios

- ✅ **Contraste Automático**: Sempre legível
- ✅ **Flexibilidade**: Funciona com qualquer cor
- ✅ **Consistência**: Mesmo padrão em todo o checkout
- ✅ **Acessibilidade**: Atende padrões de contraste
- ✅ **Manutenção**: Zero configuração manual

## 🔍 Exemplos

### Fundo Escuro (#000000)
- Texto: Branco (#ffffff)
- Bordas: Cinza claro (#4d4d4d)

### Fundo Claro (#ffffff)
- Texto: Preto (#000000)
- Bordas: Cinza escuro (#333333)

### Fundo Colorido (#ff6b6b)
- Texto: Branco (#ffffff)
- Bordas: Rosa claro (#ff9999)

