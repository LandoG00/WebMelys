const { PurgeCSS } = require('purgecss');
const fs = require('fs');
const path = require('path');
const concat = require('concat');

console.log('🚀 Starting CSS optimization...');

(async () => {
  try {
    // --- PASO 1: Combinar todos los archivos CSS en uno solo ---
    const cssFiles = [
        './assets/css/bootstrap.min.css',
        './assets/css/animate.min.css',
        './assets/css/font-awesome.min.css',
        './assets/css/lightcase.css',
        './assets/css/meanmenu.css',
        './assets/css/nice-select.css',
        './assets/css/owl.carousel.min.css',
        './assets/css/odometer.css',
        './assets/css/default.css',
        './assets/css/style.css',
        './assets/css/responsive.css'
    ];
    const combinedCssPath = path.join(__dirname, 'assets/css/combined.css');
    await concat(cssFiles, combinedCssPath);
    console.log('✅ CSS files combined successfully.');

    // --- PASO 2: Optimizar el archivo combinado ---
    const contentFiles = [
        './*.html',
        './assets/js/**/*.js'
    ];

    const purgeCSSResults = await new PurgeCSS().purge({
      content: contentFiles,
      css: [combinedCssPath], // Solo procesamos el archivo combinado
      safelist: {
        standard: [
          'active', 'show', 'fade', 'collapsing', 'collapse', 'collapsed', 'disabled',
          'animated', 'sticky-header', 'ctn-preloader', 'loaded', 'preloader',
          'letters-loading', 'spinner', 'mean-remove', 'lightcase-open',
          'lightcase-is-mobile'
        ],
        deep: [
            /^owl-/, /^mean-/, /^lightcase-/, /^nice-select/,
            /^fa-/, /^fab/, /^fal/, /^far/, /^fas/,
            /modal-/, /dropdown-/, /carousel-/
        ],
      },
      variables: true,
      keyframes: true,
    });

    // --- PASO 3: Guardar el resultado final ---
    const outputPath = path.join(__dirname, 'assets/css/optimized.css');
    fs.writeFileSync(outputPath, purgeCSSResults[0].css);

    // --- PASO 4: Limpiar el archivo combinado temporal ---
    fs.unlinkSync(combinedCssPath);

    console.log(`✅ CSS optimization complete! Final file is at ${outputPath}`);

  } catch (error) {
    console.error('❌ Error during CSS optimization:', error);
  }
})();