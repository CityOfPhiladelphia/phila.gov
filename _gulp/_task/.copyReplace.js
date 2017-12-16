'use strict';

var config  = require('../config');
var gulp    = require('gulp');
var gutil   = require('gulp-util');
var replace = require('gulp-replace');
var runSequence = require('run-sequence').use(gulp);

gulp.task('copyReplace', function(done) {
	runSequence('copy-php',
				'copy-fonts',
				'copy-scripts',
				'copy-images',
				'copy-css')

	done();
});








/**
* copy all .php and .css files to production theme
* rename theme for production
*/
gulp.task('copy-php',function(done) {

	gutil.log('Copying '+config.clientName+'_dev .php and .css files to '+config.prod_theme);

	gulp.src([config.dev_theme+'/**/*.{php,css}'])
		.pipe(replace(/_paperTheme/g, config.clientName)) //replace base theme name with client name
		.pipe(replace(config.clientName+'_dev', config.clientName)) // remove "_dev" from theme name
		.pipe(gulp.dest(config.prod_theme));

	done();
})



/**
 * copy all minified css files to prodcution theme
 * rename css file author to client name
 */
gulp.task('copy-css', function(done) {

	gutil.log('Copying all .min.css files from '+config.dev_theme+'/assets/css and to '+config.prod_theme+'/assets/css');

	gulp.src([config.dev_theme+'/.tmp/assets/css/**/*.min.css'])
		.pipe(replace(/_paperTheme/g, config.clientName)) // site name replacement
		.pipe(replace(/_author_/g, config.clientName)) // site author replacement
		.pipe(gulp.dest(config.prod_theme+'/assets/css'));
	done();
})


/**
* copy all fonts files to prodcution theme
*/
gulp.task('copy-fonts', function(done) {
	gutil.log('Copying all font files from '+config.dev_theme+'/assets/css/fonts and to '+config.prod_theme+'/assets/css/fonts');
	gulp.src([config.dev_theme+'/assets/css/fonts/**/*']).pipe(gulp.dest(config.prod_theme+'/assets/css/fonts'));

	done();
})


/**
* copy all js files to prodcution theme
*/
gulp.task('copy-scripts', function(done) {
	gutil.log('Copying and concat js files to '+config.prod_theme+'/assets/js/'+config.clientName+'.min.js');
	gulp.src([ config.dev_theme+'/assets/js/'+config.clientName+'_libs.min.js',
			   config.dev_theme+'/assets/js/'+config.clientName+'.min.js',
			   config.dev_theme+'/assets/js/modernizr.custom.js',
			   config.dev_theme+'/assets/js/acf-external-link-toggle.js'])
		.pipe(gulp.dest(config.prod_theme+'/assets/js'));

	done();
});


/**
* copy all image files to prodcution theme
* images optimized on gulp watch
*/
gulp.task('copy-images', function() {
	gutil.log('\nCompressing and optimizing Images to '+ config.prod_theme+'/assets/imgs');
	gulp.src(config.dev_theme+'/assets'+'/imgs/**/*').pipe( gulp.dest( config.prod_theme+'/assets/imgs') );

});




