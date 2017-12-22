'use strict';

var config = require('../config');
var gulp   = require('gulp');
var gutil  = require('gulp-util');
var del    = require('del');

gulp.task('clean', function() {

 	gutil.log('Deleting all '+config.prod_theme+ ' files');
 	return del(config.prod_theme);
});
