'use strict';
//blueprintinteractive.com/blog/gulp-and-browsersync-mamp
var config      = require('../config');
var browserSync = require('browser-sync').create(config.clientName);
var gulp        = require('gulp');

gulp.task('browserSync', function() {

  browserSync.init({
        proxy: config.proxy
    });

});
