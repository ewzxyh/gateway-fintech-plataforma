# 📱 COMANDOS PARA BUILDAR APK - GatewayApp

## 🚀 Método 1: EAS Build (Recomendado)

### 1️⃣ Preparar o ambiente
```bash
cd /home/gateway-demo/htdocs/demo.gateway.shop/gateway-mobile-app/GatewayApp
```

### 2️⃣ Instalar dependências (se necessário)
```bash
npm install
```

### 3️⃣ Buildar APK de Preview (para testes)
```bash
eas build --platform android --profile preview
```

### 4️⃣ Buildar APK de Produção (para Play Store)
```bash
eas build --platform android --profile production
```

---

## 🔧 Método 2: Build Local (Alternativo)

### 1️⃣ Preparar o ambiente
```bash
cd /home/gateway-demo/htdocs/demo.gateway.shop/gateway-mobile-app/GatewayApp
```

### 2️⃣ Instalar dependências
```bash
npm install
```

### 3️⃣ Gerar APK localmente
```bash
npx expo run:android --variant release
```

---

## 📋 Configurações Atuais do Projeto

### EAS Build Profiles:
- **preview**: Gera APK para testes internos
- **production**: Gera AAB para Play Store

### Informações do App:
- **Nome**: Gateway
- **Package**: com.hkdev027.GatewayApp
- **Versão**: 1.0.3
- **Version Code**: 4

---

## 🎯 Comando Recomendado

Para gerar um APK de teste com as correções implementadas:

```bash
cd /home/gateway-demo/htdocs/demo.gateway.shop/gateway-mobile-app/GatewayApp && eas build --platform android --profile preview
```

---

## 📥 Onde encontrar o APK

Após o build, o APK será disponibilizado em:
- Link de download no terminal
- Dashboard do EAS Build
- Email de notificação

---

## ⚠️ Importante

1. **Primeira vez**: Pode pedir login no EAS
2. **Build time**: ~10-15 minutos
3. **Tamanho**: ~50-100MB
4. **Notificações**: Funcionarão apenas em builds de produção/development

---

## 🔍 Verificar se está tudo OK

```bash
# Verificar configuração
eas build:configure

# Ver status dos builds
eas build:list

# Ver logs do último build
eas build:view
```

