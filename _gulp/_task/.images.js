'use strict';

var config      = require('../config');
var changed     = require('gulp-changed');
var gulp        = require('gulp');
var gulpif      = require('gulp-if');
var imagemin    = require('gulp-imagemin');
var pngquant	= require('imagemin-pngquant');
var svgo		= require('imagemin-svgo');
var jpegoptim	= require('imagemin-jpegoptim');
var browserSync = require('browser-sync').get(config.clientName);



gulp.task('images', function(done) {

  gulp.src(config.images.src)
    .pipe(changed(config.images.dest)) // Ignore unchanged files
    .pipe(gulpif(global.isProd, imagemin({
										 progressive: true,
										 svgoPlugins: [{removeViewBox: false}],
										 use:         [pngquant()]
										}
	))) // Optimize
    .pipe(gulp.dest(config.images.dest))
    .pipe(browserSync.stream({ once: true }));

    done();

});