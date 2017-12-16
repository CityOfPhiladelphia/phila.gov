'use strict';

var config       = require('../config');
var gulp         = require('gulp');
var gulpif       = require('gulp-if');
var gutil        = require('gulp-util');
var source       = require('vinyl-source-stream');
var rename       = require('gulp-rename');
var sourcemaps   = require('gulp-sourcemaps');
var buffer       = require('vinyl-buffer');
var streamify    = require('gulp-streamify');
var watchify     = require('watchify');
var browserify   = require('browserify');
var babelify     = require('babelify');
var uglify       = require('gulp-uglify');
var handleErrors = require('../util/handleErrors');
var browserSync  = require('browser-sync').get(config.clientName);
// var debowerify   = require('debowerify');
// var ngAnnotate   = require('browserify-ngannotate');



// Based on: http://blog.avisi.nl/2014/04/25/how-to-keep-a-fast-build-with-browserify-and-reactjs/
function buildScript(file) {


  var bundler = browserify({
    entries:      config.browserify.entries,
    debug:        true,
    cache:        {},
    packageCache: {},
    fullPaths:    !global.isProd
  });


  // only run once if running with a build command
  // otherwise watchify to run continously on file updates
  if (  !global.isProd ) {
    bundler = watchify(bundler);
    bundler.on('update', function() {
      rebundle();
    });
  }

  var transforms = [
    { 'name':babelify, 'options': {'presets':["es2015"]} },
  ];

  transforms.forEach(function(transform) {
    bundler.transform(transform.name, transform.options);
  });

  function rebundle() {
    var stream = bundler.bundle();
    var createSourcemap = global.isProd && config.browserify.prodSourcemap;

    gutil.log('Rebundle...');

    return stream.on('error', handleErrors)
      .pipe(source(file))
      .pipe(gulpif(createSourcemap, buffer()))
      .pipe(gulpif(createSourcemap, sourcemaps.init()))
      .pipe(gulpif(global.isProd, streamify(uglify({
        compress: { drop_console: true }
      }))))
      .pipe(gulpif(createSourcemap, sourcemaps.write('./')))
      .pipe(rename(config.browserify.bundleName))
      .pipe(gulp.dest(config.scripts.dest))
      .pipe(browserSync.stream({ once: true }));
  }

  return rebundle();

}

gulp.task('browserify', function(done) {

  buildScript('Main.js');
  done();

});
