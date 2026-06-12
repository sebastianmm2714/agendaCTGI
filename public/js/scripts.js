document.getElementById('btnPdf').addEventListener('click', () => {

  const elemento = document.querySelector('.hoja');
  
  // 1. Quitar el padding superior y min-height temporalmente
  elemento.style.paddingTop = '0';
  elemento.style.minHeight = 'unset';

  const opciones = {
    margin: [10, 0, 20, 0], // Solo margen superior e inferior para evitar recortes laterales
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
      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();

      for (let i = 1; i <= totalPages; i++) {
        pdf.setPage(i);
        
        pdf.setDrawColor(0);
        pdf.setLineWidth(0.4);

        // 1. Línea superior de cierre (Solo a partir de la segunda hoja)
        if (i > 1) {
          pdf.line(10, 10, pageWidth - 10, 10);
        }

        // 2. Línea inferior de cierre (Solo si la tabla continúa en otra hoja)
        if (i < totalPages) {
          pdf.line(10, pageHeight - 20, pageWidth - 10, pageHeight - 20);
        }

        // 3. Pie de página
        pdf.setFontSize(10);
        pdf.setTextColor(100);
        // Escribe 'Página X de Y' en la esquina inferior derecha
        pdf.text('Página ' + i + ' de ' + totalPages, pageWidth - 25, pageHeight - 15);
        pdf.text('GCCON-F-095', pageWidth - 45, pageHeight - 10);
      }
    })
    .save().then(() => {
      // 2. Restaurar el padding visual y min-height después de generar el PDF
      elemento.style.paddingTop = '10mm';
      elemento.style.minHeight = '';
    });
});
