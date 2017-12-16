'use strict';

var config  = require('../config');
var gulp    = require('gulp');


gulp.task('watch',['browserSync'],function() {
  // Scripts are automatically watched and rebundled by Watchify inside Browserify task

  //JSLINT
  // gulp.watch(config.scripts.src, ['lint']);
  // COMPASS, SASS, AUTOPREFIXER, MINIFIACATION, SOURCEMAPS
  gulp.watch(config.styles.src,  ['styles']);
  // COMPRESSION, PNG SVG JPEG (theme images only not uploads)
  // gulp.watch(config.images.src,  ['images']);
  // reload BrowserSync when new fonts are created
  // gulp.watch(config.fonts.src,   ['fonts']);

});
