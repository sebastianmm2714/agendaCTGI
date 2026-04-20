document.getElementById('btnPdf').addEventListener('click', () => {

  const elemento = document.querySelector('.hoja');
  
  const opciones = {
    margin: [0, 0, 10, 0], // Margen inferior aumentado para dar espacio al número de página
    filename: 'Agenda_Desplazamiento_SENA.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: {
      scale: 2,
      useCORS: true,
      letterRendering: true,
      scrollY: 0,
      scrollX: 0,
      x: 0,
      y: 0,
      windowWidth: 816
    },
    jsPDF: {
      unit: 'mm',
      format: 'letter',
      orientation: 'portrait'
    },
    pagebreak: { mode: ['css', 'legacy'] }
  };

  html2pdf()
    .set(opciones)
    .from(elemento)
    .toPdf()
    .get('pdf')
    .then(function (pdf) {
      const totalPages = pdf.internal.getNumberOfPages();
      for (let i = 1; i <= totalPages; i++) {
        pdf.setPage(i);
        pdf.setFontSize(10);
        pdf.setTextColor(100);
        // Escribe 'Página X de Y' en la esquina inferior derecha (aprox a 5mm del borde inferior)
        pdf.text('Página ' + i + ' de ' + totalPages, pdf.internal.pageSize.getWidth() - 25, pdf.internal.pageSize.getHeight() - 5);
      }
    })
    .save();
});
