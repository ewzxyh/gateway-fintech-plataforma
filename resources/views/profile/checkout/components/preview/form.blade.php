<!-- Componente Formulário -->
<div class="form-section">
  <h3>Dados para Pagamento</h3>
  
  <form>
    <div class="form-group">
      <label>Nome Completo</label>
      <input type="text" name="nome" placeholder="Digite seu nome completo" required>
    </div>
    
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" placeholder="Digite seu email" required>
    </div>
    
    <div class="form-group">
      <label>CPF</label>
      <input type="text" name="cpf" placeholder="000.000.000-00" required>
    </div>
    
    <div class="form-group">
      <label>Telefone</label>
      <input type="tel" name="telefone" placeholder="(11) 99999-9999" required>
    </div>
    
    @if($checkout->produto_tipo === 'fisico')
      <div class="form-group">
        <label>CEP</label>
        <input type="text" name="cep" placeholder="00000-000" required>
      </div>
      
      <div class="form-group">
        <label>Endereço</label>
        <input type="text" name="endereco" placeholder="Rua, número, bairro" required>
      </div>
      
      <div class="form-group">
        <label>Cidade</label>
        <input type="text" name="cidade" placeholder="Sua cidade" required>
      </div>
      
      <div class="form-group">
        <label>Estado</label>
        <select name="estado" required>
          <option value="">Selecione</option>
          <option value="SP">São Paulo</option>
          <option value="RJ">Rio de Janeiro</option>
          <option value="MG">Minas Gerais</option>
          <option value="RS">Rio Grande do Sul</option>
          <option value="PR">Paraná</option>
          <option value="SC">Santa Catarina</option>
          <option value="BA">Bahia</option>
          <option value="GO">Goiás</option>
          <option value="PE">Pernambuco</option>
          <option value="CE">Ceará</option>
        </select>
      </div>
    @endif
    
    <div class="form-group">
      <label>Método de Pagamento</label>
      <div class="payment-methods">
        <div class="payment-method">
          <input type="radio" name="payment_method" value="pix" id="pix" checked>
          <label for="pix">
            <i class="fa-solid fa-qrcode"></i>
            <span>PIX</span>
          </label>
        </div>
        <div class="payment-method">
          <input type="radio" name="payment_method" value="card" id="card">
          <label for="card">
            <i class="fa-solid fa-credit-card"></i>
            <span>Cartão</span>
          </label>
        </div>
        <div class="payment-method">
          <input type="radio" name="payment_method" value="boleto" id="boleto">
          <label for="boleto">
            <i class="fa-solid fa-barcode"></i>
            <span>Boleto</span>
          </label>
        </div>
      </div>
    </div>
    
    <button type="submit" class="buy-button">
      <i class="fa-solid fa-lock"></i>
      Finalizar Compra - R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}
    </button>
  </form>
</div>