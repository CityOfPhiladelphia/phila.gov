'use strict';

var config      = require('../config');
var gulp        = require('gulp');
var fs        = require('fs');

/**
 * Update theme style.css
 * @param { String} option.env - build environment flag passed to gulp task options are --env=<dev|production>
 * @param {Object} pgk -  json object of package.json
 *
 * @reference: https://gist.github.com/mackensen/98324e6b5d7d34eccaf5
 *
 * NOTE: use `gulp build --env=dev` if just testing production theme in local environment
 */

 gulp.task('updateBanner', function() {
    var pkg  = require('../../package.json');

    console.log('\nUpdating '+config.clientName+' theme -v'+pkg.version+' root style.css');

    var banner = [
        '/*',
        'Theme Name:  '+ config.banner.name,
        'Author:      '+ config.banner.author,
        'Author URI:  '+ config.banner.authorURI,
        'Version:     '+ config.banner.version,
        'Description: '+ "production"+config.banner.description,
        'Text Domain: '+ config.banner.textDomain,
        'origin:      '+ config.banner.name+' '+config.banner.origin,
        '*/',
    ''];


 })


