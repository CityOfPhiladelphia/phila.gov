'use strict';

var fs          = require('fs');
var onlyScripts = require('./util/scriptFilter');
var tasks       = fs.readdirSync('./_gulp/tasks/').filter(onlyScripts);

tasks.forEach(function(task) {
  require('./tasks/' + task);
});


/* =======================================================================
  GULP WORKFLOW: 
	
	1. `$gulp watch`
		-  browserSync
		-  browserify
			- babelify (ES2015 support for required npm modules)
		-  jslint
		-  styles
			- COMPASS 
				- ruby gems
					- compass (1.1.0.alpha.3, 1.0.1)
					- compass-core (1.1.0.alpha.3, 1.0.3, 1.0.1)
					- compass-import-once (1.0.5)
					- sass (3.4.13, 3.4.9)
					- susy (2.1.3)
			- SASS 
			- AUTOPREFIXER 
			- MINIFIACATION 
			- SOURCEMAPS
		- images (compression)
		- fonts

	2. Edit files 
	   BrowserSync auto re-loads browser on save

	3. Commit changes 

	4. `$gulp clean`
		delete previous production theme build 

	5. `$gulp build --v=<patch|minor|major>` 
		build production theme based on config.js clientName
		-  styles
			- COMPASS 
			- SASS 
			- AUTOPREFIXER 
			- MINIFIACATION 
			- SOURCEMAPS
		- images (compression)
		- copyReplace - moves all relevant theme assets to a production theme 
		- updateVersion - theme version control and git tagging (current commit)
			"--v=" flag options, if omitted no version update performed
			 •  <patch>  bug fix                # makes v0.1.0 → v0.1.1
 			 •  <minor>  feature/new functionality      # makes v0.1.1 → v0.2.0
 		 	 •  <major>  release/pushed to production   # makes v0.2.1 → v1.0.0


 	6. `$gulp deploy --env=< dev| staging | production > --plugins(optional)`
 		- build (does not need --v arguement as theme version was properly updated in previous build step )
 		- build-sync
 			rsync production theme to server environment specified with the "--env" param
 		- plugins-sync
 			if "--plugins" flag passed to task rsync wp-content/mu-plugins/ to to server environment specified with the "--env" param
 		- deploy-log 
 			log deployment info to slack channel specified in config.js
		 

 ========================================================================== */
