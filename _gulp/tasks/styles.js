'use strict';

var config       = require('../config');
var gulp         = require('gulp');
var rename         = require('gulp-rename');
var gulpif       = require('gulp-if');
var sourcemaps   = require('gulp-sourcemaps');
var sass      = require('gulp-sass');
var handleErrors = require('../util/handleErrors');
var browserSync  = require('browser-sync').get(config.clientName);
var autoprefixer = require('gulp-autoprefixer');
const shell = require('gulp-shell');


gulp.task('styles', function (done) {

  var createSourcemap = !global.isProd || config.styles.prodSourcemap;

  gulp.src(config.styles.src)
    .pipe(gulpif(createSourcemap, sourcemaps.init()))
    .pipe(
      sass(
        {
          outputStyle: global.isProd ? 'compressed' : 'nested',
          includePaths: config.styles.sassIncludePaths
        }
        ).on('error', handleErrors))
    .pipe(autoprefixer(config.browsersupport))
    .pipe(gulpif(
      createSourcemap,
      sourcemaps.write( global.isProd ? './' : null ))
    )
    .pipe(rename(config.styles.outFile))
    .pipe(gulp.dest(config.styles.dest))
    .pipe(shell(['joia push']))
    .pipe(browserSync.stream({ once: true }));

    done();
});
