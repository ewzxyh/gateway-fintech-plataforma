<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Configuração de Split Interno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-edit"></i> Editar Configuração de Split Interno</h5>
            <a href="{{ route('admin.splits-internos.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> Voltar
            </a>
          </div>
          <div class="card-body">
            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form action="{{ route('admin.splits-internos.update', $splitInterno->id) }}" method="POST">
              @csrf
              @method('PUT')
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="usuario_pagador_id" class="form-label">
                    <i class="fas fa-user-times"></i> Usuário Pagador <span class="text-danger">*</span>
                  </label>
                  <select class="form-select" id="usuario_pagador_id" name="usuario_pagador_id" required>
                    <option value="">Selecione o usuário pagador</option>
                    @foreach($usuarios as $usuario)
                      <option value="{{ $usuario->id }}" 
                          {{ old('usuario_pagador_id', $splitInterno->usuario_pagador_id) == $usuario->id ? 'selected' : '' }}>
                        {{ $usuario->name }} ({{ $usuario->email }}) - {{ $usuario->user_id }}
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">Este usuário terá suas taxas compartilhadas.</div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="usuario_beneficiario_id" class="form-label">
                    <i class="fas fa-user-check"></i> Usuário Beneficiário <span class="text-danger">*</span>
                  </label>
                  <select class="form-select" id="usuario_beneficiario_id" name="usuario_beneficiario_id" required>
                    <option value="">Selecione o usuário beneficiário</option>
                    @foreach($usuarios as $usuario)
                      <option value="{{ $usuario->id }}" 
                          {{ old('usuario_beneficiario_id', $splitInterno->usuario_beneficiario_id) == $usuario->id ? 'selected' : '' }}>
                        {{ $usuario->name }} ({{ $usuario->email }}) - {{ $usuario->user_id }}
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">Este usuário receberá o split das taxas.</div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="porcentagem_split" class="form-label">
                    <i class="fas fa-percentage"></i> Porcentagem do Split <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <input type="number" 
                        class="form-control" 
                        id="porcentagem_split" 
                        name="porcentagem_split" 
                        value="{{ old('porcentagem_split', $splitInterno->porcentagem_split) }}"
                        min="0.01" 
                        max="100" 
                        step="0.01" 
                        required>
                    <span class="input-group-text">%</span>
                  </div>
                  <div class="form-text">Porcentagem das taxas que será enviada como split (0.01% a 100%).</div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="tipo_taxa" class="form-label">
                    <i class="fas fa-tags"></i> Tipo de Taxa <span class="text-danger">*</span>
                  </label>
                  <select class="form-select" id="tipo_taxa" name="tipo_taxa" required>
                    <option value="">Selecione o tipo de taxa</option>
                    <option value="deposito" {{ old('tipo_taxa', $splitInterno->tipo_taxa) == 'deposito' ? 'selected' : '' }}>
                      Depósito
                    </option>
                    <option value="saque_pix" {{ old('tipo_taxa', $splitInterno->tipo_taxa) == 'saque_pix' ? 'selected' : '' }}>
                      Saque PIX
                    </option>
                    <option value="carteira_gateway" {{ old('tipo_taxa', $splitInterno->tipo_taxa) == 'carteira_gateway' ? 'selected' : '' }}>
                      Carteira Gateway
                    </option>
                  </select>
                  <div class="form-text">Tipo de transação que terá split aplicado.</div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="data_inicio" class="form-label">
                    <i class="fas fa-calendar-alt"></i> Data de Início
                  </label>
                  <input type="date" 
                      class="form-control" 
                      id="data_inicio" 
                      name="data_inicio" 
                      value="{{ old('data_inicio', $splitInterno->data_inicio ? $splitInterno->data_inicio->format('Y-m-d') : '') }}">
                  <div class="form-text">Data a partir da qual o split será aplicado (opcional).</div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="data_fim" class="form-label">
                    <i class="fas fa-calendar-times"></i> Data de Fim
                  </label>
                  <input type="date" 
                      class="form-control" 
                      id="data_fim" 
                      name="data_fim" 
                      value="{{ old('data_fim', $splitInterno->data_fim ? $splitInterno->data_fim->format('Y-m-d') : '') }}">
                  <div class="form-text">Data até a qual o split será aplicado (opcional).</div>
                </div>
              </div>

              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" 
                      type="checkbox" 
                      id="ativo" 
                      name="ativo" 
                      value="1"
                      {{ old('ativo', $splitInterno->ativo) ? 'checked' : '' }}>
                  <label class="form-check-label" for="ativo">
                    <i class="fas fa-toggle-on"></i> Configuração Ativa
                  </label>
                </div>
                <div class="form-text">Marque para ativar esta configuração de split.</div>
              </div>

              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('admin.splits-internos.index') }}" class="btn btn-secondary me-md-2">
                  <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> Atualizar Configuração
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  
  <script>
    $(document).ready(function() {
      // Inicializar Select2
      $('#usuario_pagador_id, #usuario_beneficiario_id').select2({
        placeholder: 'Selecione um usuário',
        allowClear: true,
        width: '100%'
      });

      // Validação de datas
      $('#data_inicio, #data_fim').on('change', function() {
        const dataInicio = $('#data_inicio').val();
        const dataFim = $('#data_fim').val();
        
        if (dataInicio && dataFim && dataInicio > dataFim) {
          alert('A data de fim deve ser posterior à data de início.');
          $('#data_fim').val('');
        }
      });

      // Validação de usuários diferentes
      $('#usuario_pagador_id, #usuario_beneficiario_id').on('change', function() {
        const pagador = $('#usuario_pagador_id').val();
        const beneficiario = $('#usuario_beneficiario_id').val();
        
        if (pagador && beneficiario && pagador === beneficiario) {
          alert('O usuário pagador e beneficiário devem ser diferentes.');
          $(this).val('').trigger('change');
        }
      });
    });
  </script>
</body>
</html>
