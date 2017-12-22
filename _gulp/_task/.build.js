'use strict';

var config      = require('../config');
var gulp        = require('gulp');
var runSequence = require('run-sequence').use(gulp);
var versionBump = require('./versionBump.js');

/**
 * Build:  Copy all php files to a production theme named with value of config.clientName
 */
gulp.task('build',function(done) {

	global.isProd = true;

	return runSequence('browserify',
 				'styles',
 				'images',
 				'copyReplace',
 				'updateVersion',
 				function() { done();}
 			);
});
