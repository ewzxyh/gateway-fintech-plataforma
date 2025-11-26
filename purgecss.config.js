module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './app/**/*.php',
    './public/**/*.js',
    './public/**/*.html',
  ],
  css: ['./public/css/tail.css'],
  output: './public/css/',
  safelist: {
    // Classes que sempre devem ser mantidas
    standard: [
      /^modal/,
      /^dropdown/,
      /^tooltip/,
      /^popover/,
      /^alert/,
      /^toast/,
      /^collapse/,
      /^accordion/,
      /^carousel/,
      /^offcanvas/,
      /^fade/,
      /^show/,
      /^active/,
      /^disabled/,
      /^swal/,
      /^fc-/, // FullCalendar
      /^datatable/, // DataTables
      /^dt-/, // DataTables
      /^lucide/,
      /^data-/,
      /^dark/,
      /^light/,
      /^theme/,
    ],
    deep: [
      /^btn-/,
      /^bg-/,
      /^text-/,
      /^border-/,
      /^hover:/,
      /^focus:/,
      /^dark:/,
      /^\[data-theme/,
      /^\[data-bs-theme/,
    ],
    greedy: [
      /^nav-/,
      /^navbar/,
      /^sidebar/,
      /^card/,
      /^table/,
      /^form/,
      /^input/,
      /^select/,
      /^badge/,
      /^pagination/,
      /^progress/,
      /^list-group/,
    ]
  },
  // Não remover variáveis CSS e @keyframes
  keyframes: true,
  variables: true,
  fontFace: true,
  rejected: true,
  rejectedCss: './public/css/tail-rejected.css', // CSS removido para análise
}

