<x-app-layout :route="'[GERENTE] Relatório de comissões'">
  <style>
    .form-outlined-select {
      position: relative;
      margin-top: 1.5rem;
    }

    .form-outlined-select select {
      padding: 1.2rem 0.75rem 0.4rem 0.75rem;
    }

    .form-outlined-select label {
      position: absolute;
      top: 0.4rem;
      left: 0.75rem;
      background: #F4F6F8;
      padding: 0 0.25rem;
      font-size: 0.875rem;
      transition: all 0.2s ease;
      z-index: 1;
    }
  </style>
  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Start::page-header -->
      <div class="mb-3 md-mb-0 row">
        <div class="mb-3 md-mb-0 col col-12 col-lg-8 text-start" >
          <h1 class="display-5">Relatório de comissões</h1>
        </div>

        <div class="col col-12 col-lg-4 text-end">
          <form method="GET" action="{{ route('gerencia.relatorio') }}" id="filtroForm">
            <div class="row g-2">
              <div class="col col-6">
                <input type="search"
                    class="form-control"
                    id="buscar"
                    name="buscar"
                    placeholder="Buscar"
                    value="{{ request('buscar') }}"
                    autofocus>
              </div>
              <!-- Período removido - usando filtros globais -->
            </div>
          </form>
        </div>
      </div>

      <!-- Start:: row-1 -->
      <div class="row g-3">

        <div class="col-xxl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-sync"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Transações</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">{{ (clone $transactions)->count() }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xxl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-arrow-down-short-wide"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Comissão</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format((clone $transactions)->sum('comission_value'), '2',',','.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  @php
                    $totalLiquido = $transactions->sum(function ($t) {
                      return $t->solicitacao->deposito_liquido ?? 0;
                    });
                  @endphp
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Faturamento (clientes)</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($totalLiquido, '2',',','.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End:: row-1 -->



      <div class="row">
        <div class="col-xl-12">
          <div class="card card-raised">
            <div class="card-body">
              <div class="table-responsive">
                <table id="table-pix-entradas" class="table text-nowrap">
                  <thead>
                    <tr>
                      <th scope="col">Transação ID</th>
                      <th scope="col">Valor</th>
                      <th scope="col">Líquido</th>
                      <th scope="col">Comissão</th>
                      <th scope="col">Data</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($transactions as $transaction)
                    <tr>
                      <td>{{ $transaction->solicitacao->idTransaction }}</td>
                      <td>{{"R$".number_format($transaction->solicitacao->amount, '2',',','.') }}</td>
                      <td>{{"R$".number_format($transaction->solicitacao->deposito_liquido, '2',',','.') }}</td>
                      <td>{{ $transaction->comission_value }}</td>
                      <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y \à\s H:i:s') }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
<div class="modal fade" id="dateRangeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
   <div class="modal-content rounded-4">
    <div class="justify-center p-4 pl-5 modal-body align-center">
     <h5 class="mb-4 fw-semibold">Selecione o período</h5>

     <div class="row">
      <div class="mb-3 text-center col-md-6">
       <strong class="mb-2 d-block">Data de Início</strong>
       <div class="d-flex justify-content-center" id="calendarInicio"></div>
      </div>
      <div class="text-center col-md-6">
       <strong class="mb-2 d-block">Data de Fim</strong>
       <div class="d-flex justify-content-center" id="calendarFim"></div>
      </div>
     </div>
    </div>
    <div class="gap-2 mt-4 modal-footer d-flex justify-content-end">
      <button class="btn btn-outline-dark" data-bs-dismiss="modal">Cancelar</button>
      <button class="btn btn-success" id="btnAplicarDatas">Aplicar</button>
    </div>
   </div>
  </div>
 </div>


<script>
  document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("periodoSelect");
  const buscar = document.getElementById("buscar");
  const form = document.getElementById("filtroForm");
  const modalEl = document.getElementById('dateRangeModal');
  const btnAplicar = document.getElementById("btnAplicarDatas");

  let dataInicioSelecionada = null;
  let dataFimSelecionada = null;

  function formatarDataBr(data) {
   const meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
   const dia = String(data.getDate()).padStart(2, '0');
   const mes = meses[data.getMonth()];
   return `${dia} ${mes}`;
  }

  // Flatpickrs
  flatpickr("#calendarInicio", {
   inline: true,
   locale:"pt",
   dateFormat:"Y-m-d",
   onChange: function (selectedDates) {
    dataInicioSelecionada = selectedDates[0];
   }
  });

  flatpickr("#calendarFim", {
   inline: true,
   locale:"pt",
   dateFormat:"Y-m-d",
   onChange: function (selectedDates) {
    dataFimSelecionada = selectedDates[0];
   }
  });

  // Abrir modal
  select.addEventListener("change", function () {
   if (select.value ==="personalizado") {
    new bootstrap.Modal(modalEl).show();
   } else {
    form.submit();
   }
  });

  buscar.addEventListener('input', function () {
  setTimeout(() => {
    form.submit();
  }, 500);
  })

  // Aplicar datas
  btnAplicar.addEventListener("click", function () {
   if (dataInicioSelecionada && dataFimSelecionada) {
    const inicioStr = dataInicioSelecionada.toISOString().split("T")[0];
    const fimStr = dataFimSelecionada.toISOString().split("T")[0];
    const texto = `${formatarDataBr(dataInicioSelecionada)} – ${formatarDataBr(dataFimSelecionada)}`;
    const valor = `${inicioStr}:${fimStr}`;

    // Remover opção personalizada anterior, se existir
    let opExistente = select.querySelector('option[data-personalizado]');
    if (opExistente) select.removeChild(opExistente);

    // Criar nova opção personalizada
    let op = document.createElement("option");
    op.value = valor;
    op.textContent = texto;
    op.setAttribute("data-personalizado","1");
    select.appendChild(op);
    select.value = valor; // Define como selecionado

    // Fechar modal e submeter
    bootstrap.Modal.getInstance(modalEl).hide();
    form.submit();
   } else {
    alert("Selecione ambas as datas.");
   }
  });

  // Restaurar valor da URL, se existir
  const urlParams = new URLSearchParams(window.location.search);
  const periodo = urlParams.get('periodo');

  if (periodo && select) {
   if (periodo.includes(':')) {
    const [inicioStr, fimStr] = periodo.split(':');
    const inicioDate = new Date(inicioStr);
    const fimDate = new Date(fimStr);
    dataInicioSelecionada = inicioDate;
    dataFimSelecionada = fimDate;

    const texto = `${formatarDataBr(inicioDate)} – ${formatarDataBr(fimDate)}`;
    let op = document.createElement("option");
    op.value = periodo;
    op.textContent = texto;
    op.setAttribute("data-personalizado","1");

    select.appendChild(op);
    select.value = periodo; // Seleciona corretamente
   } else {
    const optionToSelect = Array.from(select.options).find(opt => opt.value === periodo);
    if (optionToSelect) {
     optionToSelect.selected = true;
    }
   }
  }
 });

  </script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    $("#table-pix-entradas").DataTable({
      responsive: true,
      info:false,
      ordering: false,
      searching: false,
      lengthChange: false,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
      },
      dom: '<"top"f>rt<"bottom"p><"clear">',
        initComplete: function() {
          // Muda o placeholder do input de busca
          $('#table-produtos_filter input[type="search"]').attr('placeholder', 'Pesquisar');
        }
    });
  });
</script>
</x-app-layout>
