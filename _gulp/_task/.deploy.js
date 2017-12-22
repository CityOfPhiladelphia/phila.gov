'use strict';
var config = require('../config');

var gulp   = require('gulp'),
	gulpif = require('gulp-if'),
	gutil  = require('gulp-util'),
	Rsync  = require('rsyncwrapper'),
    slack  = require('gulp-slack')(config.slack),
    post   = require('gulp-post'),
    runSequence = require('run-sequence');


//https://github.com/gulpjs/gulp/blob/master/docs/recipes/pass-arguments-from-cli.md
var minimist     = require('minimist'),
    options      = minimist(process.argv.slice(2)),
    knownOptions = {
       string:  'env',
       default: { env: process.env.NODE_ENV || 'staging' }
   };



gulp.task('deploy', function(done) {
	if(options.env == 'production' ) global.isRelease = true;

	runSequence('build',
				'build-sync',
				'plugins-sync',
				'deploy-log',
				function() {done();}
				);
});


/**
 * Rsync the built production theme to a server enviornment specifed
 * by the --env=<environment> arguement passed to 'gulp deploy'
 * defaults to the development server enviornment
 */
gulp.task('build-sync', function(done) {
	var pjson = require('../../package.json');
	// if --env= arguement passed gulp command use aruguement
	// else rsync to development environment
	options.env = options.env ? options.env : 'dev';

	gutil.log('RSYNC '+config.clientName+' -v'+pjson.version+' THEME to '+options.env.toUpperCase()+' environment '+config.env[options.env].username+'@'+config.env[options.env].hostname+':'+config.env[options.env].themes);

	Rsync({
		src:       config.prod_theme,
		dest:      config.env[options.env].username+'@'+config.env[options.env].hostname+':'+config.env[options.env].themes,
		ssh:       true,
		recursive: true,
		deleteAll: true // Careful, this could cause data loss
		},
		function (error,stdout,stderr,cmd) {
    		if ( error ) {
        	// failed
        		gutil.log(cmd)
        		gutil.log(error.message);
    		} else {
    			//success
    			gutil.log('SYNCED '+config.clientName+' -v'+pjson.version+' THEME to '+options.env.toUpperCase()+' environment '+config.env[options.env].username+'@'+config.env[options.env].hostname+':'+config.env[options.env].themes);

    		}
    		done();
		}
	);

});


/**
 * Rsync the wp-content/mu-plugins to a server enviornment specifed
 * by the --env=<environment> passed to 'gulp deploy'
 * defaults to the development server enviornment
 */
gulp.task('plugins-sync', function(done) {
	var pjson = require('../../package.json');
	options.env = options.env ? options.env : 'dev';

	// only sync mu-plugins if --plugins is passed to gulp command
	// otherwise exit task
	if(options.plugins){

		gutil.log('RSYNC '+config.clientName+' -v'+pjson.version+' MU-PLUGINS to '+options.env.toUpperCase()+' environment '+config.env[options.env].username+'@'+config.env[options.env].hostname+':'+config.env[options.env].wp_content);


	Rsync({
		src:       config.mu_plugins,
		dest:      config.env[options.env].username+'@'+config.env[options.env].hostname+':'+config.env[options.env].wp_content,
		ssh:       true,
		recursive: true,
		deleteAll: true // Careful, this could cause data loss
		},
		function (error,stdout,stderr,cmd) {
    		if ( error ) {
        	// failed
        		gutil.log(cmd)
        		gutil.log(error.message);
    		} else {
    			//success
    			gutil.log('SYNCED '+config.clientName+' -v'+pjson.version+' MU-PLUGINS to '+options.env.toUpperCase()+' environment '+config.env[options.env].username+'@'+config.env[options.env].hostname+':'+config.env[options.env].wp_content);

    		}
    		done();
		}
	);
	}else{
		gutil.log('MU-PLUGINS not synced');
		done();
	}


});





gulp.task('deploy-log', function(done) {
	var pjson = require('../../package.json');
	var plugins = options.plugins ? ' and mu-plugins' :'';
	return gulp.src(config.prod_theme).pipe(slack('*DEPLOYED:* `' +config.clientName+'` `-v'+pjson.version+'` theme'+plugins+' to `'+options.env.toUpperCase()+'` environment'));
})