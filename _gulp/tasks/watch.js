'use strict';

var config  = require('../config');
var gulp    = require('gulp');
var browserSync  = require('browser-sync').get(config.clientName);
const shell = require('gulp-shell');


gulp.task('watch',['browserSync'],function() {
  // Scripts are automatically watched and rebundled by Watchify inside Browserify task

  //JSLINT
  // gulp.watch(config.scripts.src, ['lint']);
  // COMPASS, SASS, AUTOPREFIXER, MINIFIACATION, SOURCEMAPS
  gulp.watch(config.styles.src,  ['styles','EC2-sync']);
  gulp.watch([config.dev_theme+'/**/*.php'],['EC2-sync']);
  // COMPRESSION, PNG SVG JPEG (theme images only not uploads)
  // gulp.watch(config.images.src,  ['images']);
  // reload BrowserSync when new fonts are created
  // gulp.watch(config.fonts.src,   ['fonts']);

});

gulp.task('EC2-sync',function(done) {

    gulp.src([config.dev_theme+'/**/*'])
        .pipe(shell(['joia push']))
        .pipe(browserSync.stream({ once: true }));

    done();
})
