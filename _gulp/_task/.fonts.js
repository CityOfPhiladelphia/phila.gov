'use strict';

var config      = require('../config');
var changed     = require('gulp-changed');
var gulp        = require('gulp');
var browserSync = require('browser-sync').get(config.clientName);

gulp.task('fonts', function() {

  return gulp.src(config.fonts.src)
    .pipe(changed(config.fonts.dest)) // Ignore unchanged files
    .pipe(gulp.dest(config.fonts.dest))
    .pipe(browserSync.stream({ once: true }));

});
