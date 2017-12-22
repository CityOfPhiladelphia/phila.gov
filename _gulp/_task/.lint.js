'use strict';

var config = require('../config');
var gulp   = require('gulp');
var jshint = require('gulp-jshint');

gulp.task('lint', function() {
	console.log(config.scripts.src)
  return gulp.src([config.scripts.src, config.dev_theme+'/assets/js/Main.js'])
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'));
});