'use strict';

var config = require('../config');
var gulp   = require('gulp');
var gutil  = require('gulp-util');
var fs     = require('fs');
var replace = require('gulp-replace');
var runSequence = require('run-sequence');

var pkg;



 gulp.task('updateVersion', function(done) {
    runSequence(
        'versionBump',
        'updateThemeBanners',
        'versionBustScripts',
        function(){done();});

 });




/**
 * Update the comment banners in the themes main style.css
 */
 gulp.task('updateThemeBanners', function(done) {

     pkg = require('../../package.json');

    gutil.log('\nUpdating '+config.clientName+' theme banners in root style.css to -v'+pkg.version);

    var banner = [
        '/*',
        'Theme Name:  '+ config.banner.name,
        'Author:      '+ config.banner.author,
        'Author URI:  '+ config.banner.authorURI,
        'Version:     '+ pkg.version,
        'Description: '+ 'production'+config.banner.description,
        'Text Domain: '+ config.banner.name,
        'origin:      '+ config.banner.name+' '+config.banner.origin,
        '*/',
    ''];

   fs.writeFile(config.prod_theme+'/style.css', banner.join('\n'), function(){
    console.log('prouction banner updated')
    updateDevBanner();
   });

   var updateDevBanner = function() {
        //update values for Develpment Theme
            banner[1] =  'Theme Name:  '+config.banner.name+"_dev";
            banner[5] =  'Description: '+"development"+config.banner.description;
            banner[6] =  'Text Domain: '+ config.banner.name+"_dev",
            banner[7] =  'origin:      '+config.banner.name+"_dev "+config.banner.origin;

        fs.writeFile(config.dev_theme+'/style.css', banner.join('\n'), function() {
            console.log('development banner updated')
            done();
        });

   }

 });




/**
 * Update the version variable in themes functions.php
 * this cache busts the scripts that are served from a cdn
 */
gulp.task('versionBustScripts', function(done) {


    gulp.src(config.prod_theme+'/functions.php')
        .pipe(replace(/(\$version=).*/g, '$version="'+pkg.version+'";'))
        .pipe(gulp.dest(config.prod_theme))
        .on('end', function() {
            gutil.log(config.prod_theme+'/functions.php $version updated to: '+pkg.version);
            updateDev();
        });

    var updateDev = function(argument) {
        gulp.src(config.dev_theme+'/functions.php')
            .pipe(replace(/(\$version=).*/g, '$version="'+pkg.version+'";'))
            .pipe(gulp.dest(config.dev_theme))
            .on('end', function(){
                gutil.log(config.dev_theme+'/functions.php $version updated to: '+pkg.version);
                done();
            });
    }


});





