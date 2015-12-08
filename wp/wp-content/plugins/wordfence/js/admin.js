(function($) {
	if (!window['wordfenceAdmin']) { //To compile for checking: java -jar /usr/local/bin/closure.jar --js=admin.js --js_output_file=test.js
		window['wordfenceAdmin'] = {
			loading16: '<div class="wfLoading16"></div>',
			loadingCount: 0,
			dbCheckTables: [],
			dbCheckCount_ok: 0,
			dbCheckCount_skipped: 0,
			dbCheckCount_errors: 0,
			issues: [],
			ignoreData: false,
			iconErrorMsgs: [],
			scanIDLoaded: 0,
			colorboxQueue: [],
			mode: '',
			visibleIssuesPanel: 'new',
			preFirstScanMsgsLoaded: false,
			newestActivityTime: 0, //must be 0 to force loading of all initially
			elementGeneratorIter: 1,
			reloadConfigPage: false,
			nonce: false,
			tickerUpdatePending: false,
			activityLogUpdatePending: false,
			lastALogCtime: 0,
			activityQueue: [],
			totalActAdded: 0,
			maxActivityLogItems: 1000,
			scanReqAnimation: false,
			debugOn: false,
			blockedCountriesPending: [],
			ownCountry: "",
			schedStartHour: false,
			currentPointer: false,
			countryMap: false,
			countryCodesToSave: "",
			performanceScale: 3,
			performanceMinWidth: 20,
			tourClosed: false,
			welcomeClosed: false,
			passwdAuditUpdateInt: false,
			_windowHasFocus: true,
			serverTimestampOffset: 0,

			init: function() {
				this.nonce = WordfenceAdminVars.firstNonce;
				this.debugOn = WordfenceAdminVars.debugOn == '1' ? true : false;
				this.tourClosed = WordfenceAdminVars.tourClosed == '1' ? true : false;
				this.welcomeClosed = WordfenceAdminVars.welcomeClosed == '1' ? true : false;
				var startTicker = false;
				var self = this;

				$(window).on('blur', function() {
					self._windowHasFocus = false;
				}).on('focus', function() {
					self._windowHasFocus = true;
				}).focus();

				$(document).focus();

				// (docs|support).wordfence.com GA links
				$(document).on('click', 'a', function() {
					if (this.href && this.href.indexOf('utm_source') > -1) {
						return;
					}
					var utm = '';
					if (this.host == 'docs.wordfence.com') {
						utm = 'utm_source=plugin&utm_medium=pluginUI&utm_campaign=docsIcon';
					}
					if (utm) {
						utm = (this.search ? '&' : '?') + utm;
						this.href = this.protocol + '//' + this.host + this.pathname + this.search + utm + this.hash;
					}

					if (this.href == 'http://support.wordfence.com/') {
						this.href = 'https://support.wordfence.com/support/home?utm_source=plugin&utm_medium=pluginUI&utm_campaign=supportLink';
					}
				});

				if (jQuery('#wordfenceMode_scan').length > 0) {
					this.mode = 'scan';
					jQuery('#wfALogViewLink').prop('href', WordfenceAdminVars.siteBaseURL + '?_wfsf=viewActivityLog&nonce=' + this.nonce);
					jQuery('#consoleActivity').scrollTop(jQuery('#consoleActivity').prop('scrollHeight'));
					jQuery('#consoleScan').scrollTop(jQuery('#consoleScan').prop('scrollHeight'));
					this.noScanHTML = jQuery('#wfNoScanYetTmpl').tmpl().html();
					this.loadIssues();
					this.startActivityLogUpdates();
					if (this.needTour()) {
						this.scanTourStart();
					}
				} else if (jQuery('#wordfenceMode_activity').length > 0) {
					this.mode = 'activity';
					this.setupSwitches('wfLiveTrafficOnOff', 'liveTrafficEnabled', function() {
					});
					jQuery('#wfLiveTrafficOnOff').change(function() {
						if (/^(?:falcon|php)$/.test(WordfenceAdminVars.cacheType)) {
							jQuery('#wfLiveTrafficOnOff').attr('checked', false);
							self.colorbox('400px', "Live Traffic not available in high performance mode", "Please note that you can't enable live traffic when Falcon Engine or basic caching is enabled. This is done for performance reasons. If you want live traffic, go to the 'Performance Setup' menu and disable caching.");
						} else {
							self.updateSwitch('wfLiveTrafficOnOff', 'liveTrafficEnabled', function() {
								window.location.reload(true);
							});
						}
					});

					if (WordfenceAdminVars.liveTrafficEnabled) {
						this.activityMode = 'hit';
					} else {
						this.activityMode = 'loginLogout';
						this.switchTab(jQuery('#wfLoginLogoutTab'), 'wfTab1', 'wfDataPanel', 'wfActivity_loginLogout', function() {
							WFAD.activityTabChanged();
						});
					}
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWelcomeContent3', 'wfHeading', 'top', 'left', "Learn about Site Performance", function() {
							self.tourRedir('WordfenceSitePerf');
						});
					}
				} else if (jQuery('#wordfenceMode_options').length > 0) {
					this.mode = 'options';
					jQuery('.wfConfigElem').change(function() {
						jQuery('#securityLevel').val('CUSTOM');
					});
					this.updateTicker(true);
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfContentBasicOptions', 'wfMarkerBasicOptions', 'top', 'left', "Learn about Live Traffic Options", function() {
							self.tour('wfContentLiveTrafficOptions', 'wfMarkerLiveTrafficOptions', 'bottom', 'left', "Learn about Scanning Options", function() {
								self.tour('wfContentScansToInclude', 'wfMarkerScansToInclude', 'bottom', 'left', "Learn about Firewall Rules", function() {
									self.tour('wfContentFirewallRules', 'wfMarkerFirewallRules', 'bottom', 'left', "Learn about Login Security", function() {
										self.tour('wfContentLoginSecurity', 'wfMarkerLoginSecurity', 'bottom', 'left', "Learn about Other Options", function() {
											self.tour('wfContentOtherOptions', 'wfMarkerOtherOptions', 'bottom', 'left', false, false);
										});
									});
								});
							});
						});
					}
				} else if (jQuery('#wordfenceMode_blockedIPs').length > 0) {
					this.mode = 'blocked';
					this.staticTabChanged();
					this.updateTicker(true);
					startTicker = true;
					if (this.needTour()) {
						this.tour('wfWelcomeContent4', 'wfHeading', 'top', 'left', "Learn about Auditing Passwords", function() {
							self.tourRedir('WordfencePasswdAudit');
						});
					}
				} else if (jQuery('#wordfenceMode_passwd').length > 0) {
					this.mode = 'passwd';
					startTicker = false;
					this.doPasswdAuditUpdate();
					if (this.needTour()) {
						this.tour('wfWelcomePasswd', 'wfHeading', 'top', 'left', "Learn about Cellphone Sign-in", function() {
							self.tourRedir('WordfenceTwoFactor');
						});
					}
				} else if (jQuery('#wordfenceMode_twoFactor').length > 0) {
					this.mode = 'twoFactor';
					startTicker = false;
					if (this.needTour()) {
						this.tour('wfWelcomeTwoFactor', 'wfHeading', 'top', 'left', "Learn how to Block Countries", function() {
							self.tourRedir('WordfenceCountryBlocking');
						});
					}
					this.loadTwoFactor();

				} else if (jQuery('#wordfenceMode_countryBlocking').length > 0) {
					this.mode = 'countryBlocking';
					startTicker = false;
					if (this.needTour()) {
						this.tour('wfWelcomeContentCntBlk', 'wfHeading', 'top', 'left', "Learn how to Schedule Scans", function() {
							self.tourRedir('WordfenceScanSchedule');
						});
					}
				} else if (jQuery('#wordfenceMode_rangeBlocking').length > 0) {
					this.mode = 'rangeBlocking';
					startTicker = false;
					if (this.needTour()) {
						this.tour('wfWelcomeContentRangeBlocking', 'wfHeading', 'top', 'left', "Learn how to Customize Wordfence", function() {
							self.tourRedir('WordfenceSecOpt');
						});
					}
					this.calcRangeTotal();
					this.loadBlockRanges();
				} else if (jQuery('#wordfenceMode_whois').length > 0) {
					this.mode = 'whois';
					startTicker = false;
					if (this.needTour()) {
						this.tour('wfWelcomeContentWhois', 'wfHeading', 'top', 'left', "Learn how to use Advanced Blocking", function() {
							self.tourRedir('WordfenceRangeBlocking');
						});
					}
					this.calcRangeTotal();
					this.loadBlockRanges();

				} else if (jQuery('#wordfenceMode_scanScheduling').length > 0) {
					this.mode = 'scanScheduling';
					startTicker = false;
					this.sched_modeChange();
					if (this.needTour()) {
						this.tour('wfWelcomeContentScanSched', 'wfHeading', 'top', 'left', "Learn about WHOIS", function() {
							self.tourRedir('WordfenceWhois');
						});
					}
				} else if (jQuery('#wordfenceMode_caching').length > 0) {
					this.mode = 'caching';
					startTicker = false;
					if (this.needTour()) {
						this.tour('wfWelcomeContentCaching', 'wfHeading', 'top', 'left', "Learn about IP Blocking", function() {
							self.tourRedir('WordfenceBlockedIPs');
						});
					}
					this.loadCacheExclusions();
				} else {
					this.mode = false;
				}
				if (this.mode) { //We are in a Wordfence page
					if (startTicker) {
						this.updateTicker();
						this.liveInt = setInterval(function() {
							self.updateTicker();
						}, WordfenceAdminVars.actUpdateInterval);
					}
					jQuery(document).bind('cbox_closed', function() {
						self.colorboxIsOpen = false;
						self.colorboxServiceQueue();
					});
				}
			},
			needTour: function() {
				if ((!this.tourClosed) && this.welcomeClosed) {
					return true;
				} else {
					return false;
				}
			},
			sendTestEmail: function(email) {
				var self = this;
				this.ajax('wordfence_sendTestEmail', {email: email}, function(res) {
					if (res.result) {
						self.colorbox('400px', "Test Email Sent", "Your test email was sent to the requested email address. The result we received from the WordPress wp_mail() function was: " +
						res.result + "<br /><br />A 'True' result means WordPress thinks the mail was sent without errors. A 'False' result means that WordPress encountered an error sending your mail. Note that it's possible to get a 'True' response with an error elsewhere in your mail system that may cause emails to not be delivered.");
					}
				});
			},
			loadAvgSitePerf: function() {
				var self = this;
				this.ajax('wordfence_loadAvgSitePerf', {limit: jQuery('#wfAvgPerfNum').val()}, function(res) {
					res['scale'] = self.performanceScale;
					res['min'] = self.performanceMinWidth;
					jQuery('#wfAvgSitePerfContent').empty();
					var newElem = jQuery('#wfAvgPerfTmpl').tmpl(res);
					newElem.prependTo('#wfAvgSitePerfContent').fadeIn();
				});
			},
			updateSwitch: function(elemID, configItem, cb) {
				var setting = jQuery('#' + elemID).is(':checked');
				this.updateConfig(configItem, jQuery('#' + elemID).is(':checked') ? 1 : 0, cb);
			},
			setupSwitches: function(elemID, configItem, cb) {
				jQuery('.wfOnOffSwitch-checkbox').change(function() {
					jQuery.data(this, 'lastSwitchChange', (new Date()).getTime());
				});
				var self = this;
				jQuery('div.wfOnOffSwitch').mouseup(function() {
					var elem = jQuery(this);
					setTimeout(function() {
						var checkedElem = elem.find('.wfOnOffSwitch-checkbox');
						if ((new Date()).getTime() - jQuery.data(checkedElem[0], 'lastSwitchChange') > 300) {
							checkedElem.prop('checked', !checkedElem.is(':checked'));
							self.updateSwitch(elemID, configItem, cb);
						}
					}, 50);
				});
			},
			scanTourStart: function() {
				var self = this;
				this.tour('wfWelcomeContent1', 'wfHeading', 'top', 'left', "Continue the Tour", function() {
					self.tour('wfWelcomeContent2', 'wfHeading', 'top', 'left', "Learn how to use Wordfence", function() {
						self.tour('wfWelcomeContent3', 'wfHeading', 'top', 'left', "Learn about Live Traffic", function() {
							self.tourRedir('WordfenceActivity');
						});
					});
				});
			},
			tourRedir: function(menuItem) {
				window.location.href = 'admin.php?page=' + menuItem;
			},
			updateConfig: function(key, val, cb) {
				this.ajax('wordfence_updateConfig', {key: key, val: val}, function() {
					cb();
				});
			},
			tourFinish: function() {
				this.ajax('wordfence_tourClosed', {}, function(res) {
				});
			},
			downgradeLicense: function() {
				this.colorbox('400px', "Confirm Downgrade", "Are you sure you want to downgrade your Wordfence Premium License? This will disable all Premium features and return you to the free version of Wordfence. <a href=\"https://www.wordfence.com/manage-wordfence-api-keys/\" target=\"_blank\">Click here to renew your paid membership</a> or click the button below to confirm you want to downgrade.<br /><br /><input type=\"button\" value=\"Downgrade and disable Premium features\" onclick=\"WFAD.downgradeLicenseConfirm();\" /><br />");
			},
			downgradeLicenseConfirm: function() {
				jQuery.colorbox.close();
				this.ajax('wordfence_downgradeLicense', {}, function(res) {
					location.reload(true);
				});
			},
			tour: function(contentID, elemID, edge, align, buttonLabel, buttonCallback) {
				var self = this;
				if (this.currentPointer) {
					this.currentPointer.pointer('destroy');
					this.currentPointer = false;
				}
				var options = {
					buttons: function(event, t) {
						var buttonElem = jQuery('<div id="wfTourButCont"><a id="pointer-close" style="margin-left:5px" class="button-secondary">End the Tour</a></div><div><a id="wfRateLink" href="http://wordpress.org/extend/plugins/wordfence/" target="_blank" style="font-size: 10px; font-family: Verdana;">Help spread the word by rating us 5&#9733; on WordPress.org</a></div>');
						buttonElem.find('#pointer-close').bind('click.pointer', function(evtObj) {
							var evtSourceElem = evtObj.srcElement ? evtObj.srcElement : evtObj.target;
							if (evtSourceElem.id == 'wfRateLink') {
								return true;
							}
							self.tourFinish();
							t.element.pointer('close');
							return false;
						});
						return buttonElem;
					},
					close: function() {
					},
					content: jQuery('#' + contentID).tmpl().html(),
					pointerWidth: 400,
					position: {
						edge: edge,
						align: align
					}
				};
				this.currentPointer = jQuery('#' + elemID).pointer(options).pointer('open');
				if (buttonLabel && buttonCallback) {
					jQuery('#pointer-close').after('<a id="pointer-primary" class="button-primary">' + buttonLabel + '</a>');
					jQuery('#pointer-primary').click(buttonCallback);
				}
			},
			startTourAgain: function() {
				var self = this;
				this.ajax('wordfence_startTourAgain', {}, function(res) {
					self.tourClosed = false;
					self.scanTourStart();
				});
			},
			showLoading: function() {
				this.loadingCount++;
				if (this.loadingCount == 1) {
					jQuery('<div id="wordfenceWorking">Wordfence is working...</div>').appendTo('body');
				}
			},
			removeLoading: function() {
				this.loadingCount--;
				if (this.loadingCount == 0) {
					jQuery('#wordfenceWorking').remove();
				}
			},
			startActivityLogUpdates: function() {
				var self = this;
				setInterval(function() {
					self.updateActivityLog();
				}, parseInt(WordfenceAdminVars.actUpdateInterval));
			},
			updateActivityLog: function() {
				if (this.activityLogUpdatePending || !this.windowHasFocus()) {
					return;
				}
				this.activityLogUpdatePending = true;
				var self = this;
				this.ajax('wordfence_activityLogUpdate', {
					lastctime: this.lastALogCtime
				}, function(res) {
					self.doneUpdateActivityLog(res);
				}, function() {
					self.activityLogUpdatePending = false;
				}, true);

			},
			doneUpdateActivityLog: function(res) {
				this.actNextUpdateAt = (new Date()).getTime() + parseInt(WordfenceAdminVars.actUpdateInterval);
				if (res.ok) {
					if (res.items.length > 0) {
						this.activityQueue.push.apply(this.activityQueue, res.items);
						this.lastALogCtime = res.items[res.items.length - 1].ctime;
						this.processActQueue(res.currentScanID);
					}
				}
				this.activityLogUpdatePending = false;
			},
			processActQueue: function(currentScanID) {
				if (this.activityQueue.length > 0) {
					this.addActItem(this.activityQueue.shift());
					this.totalActAdded++;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#consoleActivity div:first').remove();
						this.totalActAdded--;
					}
					var timeTillNextUpdate = this.actNextUpdateAt - (new Date()).getTime();
					var maxRate = 50 / 1000; //Rate per millisecond
					var bulkTotal = 0;
					while (this.activityQueue.length > 0 && this.activityQueue.length / timeTillNextUpdate > maxRate) {
						var item = this.activityQueue.shift();
						if (item) {
							bulkTotal++;
							this.addActItem(item);
						}
					}
					this.totalActAdded += bulkTotal;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#consoleActivity div:lt(' + bulkTotal + ')').remove();
						this.totalActAdded -= bulkTotal;
					}
					var minDelay = 100;
					var delay = minDelay;
					if (timeTillNextUpdate < 1) {
						delay = minDelay;
					} else {
						delay = Math.round(timeTillNextUpdate / this.activityQueue.length);
						if (delay < minDelay) {
							delay = minDelay;
						}
					}
					var self = this;
					setTimeout(function() {
						self.processActQueue();
					}, delay);
				}
				jQuery('#consoleActivity').scrollTop(jQuery('#consoleActivity').prop('scrollHeight'));
			},
			processActArray: function(arr) {
				for (var i = 0; i < arr.length; i++) {
					this.addActItem(arr[i]);
				}
			},
			addActItem: function(item) {
				if (!item) {
					return;
				}
				if (!item.msg) {
					return;
				}
				if (item.msg.indexOf('SUM_') == 0) {
					this.processSummaryLine(item);
					jQuery('#consoleSummary').scrollTop(jQuery('#consoleSummary').prop('scrollHeight'));
					jQuery('#wfStartingScan').addClass('wfSummaryOK').html('Done.');
				} else if (this.debugOn || item.level < 4) {

					var html = '<div class="wfActivityLine';
					if (this.debugOn) {
						html += ' wf' + item.type;
					}
					html += '">[' + item.date + ']&nbsp;' + item.msg + '</div>';
					jQuery('#consoleActivity').append(html);
					if (/Scan complete\./i.test(item.msg)) {
						this.loadIssues();
					}
				}
			},
			processSummaryLine: function(item) {
				var msg, summaryUpdated;
				if (item.msg.indexOf('SUM_START:') != -1) {
					msg = item.msg.replace('SUM_START:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult"><div class="wfSummaryLoading"></div></div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDBAD') != -1) {
					msg = item.msg.replace('SUM_ENDBAD:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryBad').html('Problems found.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDFAILED') != -1) {
					msg = item.msg.replace('SUM_ENDFAILED:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryBad').html('Failed.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDOK') != -1) {
					msg = item.msg.replace('SUM_ENDOK:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryOK').html('Secure.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDSUCCESS') != -1) {
					msg = item.msg.replace('SUM_ENDSUCCESS:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryOK').html('Success.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDERR') != -1) {
					msg = item.msg.replace('SUM_ENDERR:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryErr').html('An error occurred.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_DISABLED:') != -1) {
					msg = item.msg.replace('SUM_DISABLED:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult">Disabled [<a href="admin.php?page=WordfenceSecOpt">Visit Options to Enable</a>]</div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_PAIDONLY:') != -1) {
					msg = item.msg.replace('SUM_PAIDONLY:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult"><a href="https://www.wordfence.com/wordfence-signup/" target="_blank">Paid Members Only</a></div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_FINAL:') != -1) {
					msg = item.msg.replace('SUM_FINAL:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg wfSummaryFinal">' + msg + '</div><div class="wfSummaryResult wfSummaryOK">Scan Complete.</div><div class="wfClear"></div>');
				} else if (item.msg.indexOf('SUM_PREP:') != -1) {
					msg = item.msg.replace('SUM_PREP:', '');
					jQuery('#consoleSummary').empty().html('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult" id="wfStartingScan"><div class="wfSummaryLoading"></div></div><div class="wfClear"></div>');
				} else if (item.msg.indexOf('SUM_KILLED:') != -1) {
					msg = item.msg.replace('SUM_KILLED:', '');
					jQuery('#consoleSummary').empty().html('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult wfSummaryOK">Scan Complete.</div><div class="wfClear"></div>');
				}
			},
			processActQueueItem: function() {
				var item = this.activityQueue.shift();
				if (item) {
					jQuery('#consoleActivity').append('<div class="wfActivityLine wf' + item.type + '">[' + item.date + ']&nbsp;' + item.msg + '</div>');
					this.totalActAdded++;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#consoleActivity div:first').remove();
						this.totalActAdded--;
					}
					if (item.msg == 'Scan complete.') {
						this.loadIssues();
					}
				}
			},
			updateTicker: function(forceUpdate) {
				if ((!forceUpdate) && (this.tickerUpdatePending || !this.windowHasFocus())) {
					return;
				}
				this.tickerUpdatePending = true;
				var self = this;
				var alsoGet = '';
				var otherParams = '';
				if (this.mode == 'activity' && /^(?:404|hit|human|ruser|gCrawler|crawler|loginLogout)$/.test(this.activityMode)) {
					alsoGet = 'logList_' + this.activityMode;
					otherParams = this.newestActivityTime;
				} else if (this.mode == 'perfStats') {
					alsoGet = 'perfStats';
					otherParams = this.newestActivityTime;
				}
				this.ajax('wordfence_ticker', {
					alsoGet: alsoGet,
					otherParams: otherParams
				}, function(res) {
					self.handleTickerReturn(res);
				}, function() {
					self.tickerUpdatePending = false;
				}, true);
			},
			handleTickerReturn: function(res) {
				this.tickerUpdatePending = false;
				var newMsg = "";
				var oldMsg = jQuery('#wfLiveStatus').text();
				if (res.msg) {
					newMsg = res.msg;
				} else {
					newMsg = "Idle";
				}
				if (newMsg && newMsg != oldMsg) {
					jQuery('#wfLiveStatus').hide().html(newMsg).fadeIn(200);
				}
				var haveEvents, newElem;
				this.serverTimestampOffset = (new Date().getTime() / 1000) - res.serverTime;

				if (this.mode == 'activity') {
					if (res.alsoGet != 'logList_' + this.activityMode) {
						return;
					} //user switched panels since ajax request started
					if (res.events.length > 0) {
						this.newestActivityTime = res.events[0]['ctime'];
					}
					haveEvents = false;
					if (jQuery('#wfActivity_' + this.activityMode + ' .wfActEvent').length > 0) {
						haveEvents = true;
					}
					if (res.events.length > 0) {
						if (!haveEvents) {
							jQuery('#wfActivity_' + this.activityMode).empty();
						}
						for (i = res.events.length - 1; i >= 0; i--) {
							var elemID = '#wfActEvent_' + res.events[i].id;
							if (jQuery(elemID).length < 1) {
								res.events[i]['activityMode'] = this.activityMode;
								if (this.activityMode == 'loginLogout') {
									newElem = jQuery('#wfLoginLogoutEventTmpl').tmpl(res.events[i]);
								} else {
									newElem = jQuery('#wfHitsEventTmpl').tmpl(res.events[i]);
								}
								jQuery(newElem).find('.wfTimeAgo').data('wfctime', res.events[i].ctime);
								newElem.prependTo('#wfActivity_' + this.activityMode).fadeIn();
							}
						}
						this.reverseLookupIPs();
					} else {
						if (!haveEvents) {
							jQuery('#wfActivity_' + this.activityMode).html('<div>No events to report yet.</div>');
						}
					}
					var self = this;
					this.updateTimeAgo();
				} else if (this.mode == 'perfStats') {
					haveEvents = false;
					if (jQuery('#wfPerfStats .wfPerfEvent').length > 0) {
						haveEvents = true;
					}
					if (res.events.length > 0) {
						if (!haveEvents) {
							jQuery('#wfPerfStats').empty();
						}
						var curLength = parseInt(jQuery('#wfPerfStats').css('width'));
						if (res.longestLine > curLength) {
							jQuery('#wfPerfStats').css('width', (res.longestLine + 200) + 'px');
						}
						this.newestActivityTime = res.events[0]['ctime'];
						for (var i = res.events.length - 1; i >= 0; i--) {
							res.events[i]['scale'] = this.performanceScale;
							res.events[i]['min'] = this.performanceMinWidth;
							newElem = jQuery('#wfPerfStatTmpl').tmpl(res.events[i]);
							jQuery(newElem).find('.wfTimeAgo').data('wfctime', res.events[i].ctime);
							newElem.prependTo('#wfPerfStats').fadeIn();
						}
					} else {
						if (!haveEvents) {
							jQuery('#wfPerfStats').html('<p>No events to report yet.</p>');
						}
					}
					this.updateTimeAgo();
				}
			},
			reverseLookupIPs: function() {
				var ips = [];
				jQuery('.wfReverseLookup').each(function(idx, elem) {
					var txt = jQuery(elem).text();
					if (/^\d+\.\d+\.\d+\.\d+$/.test(txt) && (!jQuery(elem).data('wfReverseDone'))) {
						jQuery(elem).data('wfReverseDone', true);
						ips.push(jQuery(elem).text());
					}
				});
				if (ips.length < 1) {
					return;
				}
				var uni = {};
				var uniqueIPs = [];
				for (var i = 0; i < ips.length; i++) {
					if (!uni[ips[i]]) {
						uni[ips[i]] = true;
						uniqueIPs.push(ips[i]);
					}
				}
				this.ajax('wordfence_reverseLookup', {
						ips: uniqueIPs.join(',')
					},
					function(res) {
						if (res.ok) {
							jQuery('.wfReverseLookup').each(function(idx, elem) {
								var txt = jQuery(elem).text();
								for (var ip in res.ips) {
									if (txt == ip) {
										if (res.ips[ip]) {
											jQuery(elem).html('<strong>Hostname:</strong>&nbsp;' + res.ips[ip]);
										} else {
											jQuery(elem).html('');
										}
									}
								}
							});
						}
					}, false, false);
			},
			killScan: function() {
				var self = this;
				this.ajax('wordfence_killScan', {}, function(res) {
					if (res.ok) {
						self.colorbox('400px', "Kill requested", "A termination request has been sent to any running scans.");
					} else {
						self.colorbox('400px', "Kill failed", "We failed to send a termination request.");
					}
				});
			},
			startScan: function() {
				var scanReqAnimation = setInterval(function() {
					var str = jQuery('#wfStartScanButton1').prop('value');
					var ch = str.charAt(str.length - 1);
					if (ch == '/') {
						ch = '-';
					}
					else if (ch == '-') {
						ch = '\\';
					}
					else if (ch == '\\') {
						ch = '|';
					}
					else if (ch == '|') {
						ch = '/';
					}
					else {
						ch = '/';
					}
					jQuery('#wfStartScanButton1,#wfStartScanButton2').prop('value', "Requesting a New Scan " + ch);
				}, 100);
				setTimeout(function(res) {
					clearInterval(scanReqAnimation);
					jQuery('#wfStartScanButton1,#wfStartScanButton2').prop('value', "Start a Wordfence Scan");
				}, 3000);
				this.ajax('wordfence_scan', {}, function(res) {
				});
			},
			displayPWAuditJobs: function(res) {
				if (res && res.results && res.results.length > 0) {
					var wfAuditJobs = $('#wfAuditJobs');
					jQuery('#wfAuditJobs').empty();
					jQuery('#wfAuditJobsTable').tmpl().appendTo(wfAuditJobs);
					var wfAuditJobsBody = wfAuditJobs.find('.wf-pw-audit-tbody');
					for (var i = 0; i < res.results.length; i++) {
						jQuery('#wfAuditJobsInProg').tmpl(res.results[i]).appendTo(wfAuditJobsBody);
					}
				} else {
					jQuery('#wfAuditJobs').empty().html("<p>You don't have any password auditing jobs in progress or completed yet.</p>");
				}
			},
			loadIssues: function(callback) {
				if (this.mode != 'scan') {
					return;
				}
				var self = this;
				this.ajax('wordfence_loadIssues', {}, function(res) {
					self.displayIssues(res, callback);
				});
			},
			sev2num: function(str) {
				if (/wfProbSev1/.test(str)) {
					return 1;
				} else if (/wfProbSev2/.test(str)) {
					return 2;
				} else {
					return 0;
				}
			},
			displayIssues: function(res, callback) {
				var self = this;
				try {
					res.summary['lastScanCompleted'] = res['lastScanCompleted'];
				} catch (err) {
					res.summary['lastScanCompleted'] = 'Never';
				}
				jQuery('.wfIssuesContainer').hide();
				for (var issueStatus in res.issuesLists) {
					var containerID = 'wfIssues_dataTable_' + issueStatus;
					var tableID = 'wfIssuesTable_' + issueStatus;
					if (jQuery('#' + containerID).length < 1) {
						//Invalid issue status
						continue;
					}
					if (res.issuesLists[issueStatus].length < 1) {
						if (issueStatus == 'new') {
							if (res.lastScanCompleted == 'ok') {
								jQuery('#' + containerID).html('<p style="font-size: 20px; color: #0A0;">Congratulations! No security problems were detected by Wordfence.</p>');
							} else if (res['lastScanCompleted']) {
								//jQuery('#' + containerID).html('<p style="font-size: 12px; color: #A00;">The latest scan failed: ' + res.lastScanCompleted + '</p>');
							} else {
								jQuery('#' + containerID).html();
							}

						} else {
							jQuery('#' + containerID).html('<p>There are currently <strong>no issues</strong> being ignored on this site.</p>');
						}
						continue;
					}
					jQuery('#' + containerID).html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="' + tableID + '"></table>');

					jQuery.fn.dataTableExt.oSort['severity-asc'] = function(y, x) {
						x = WFAD.sev2num(x);
						y = WFAD.sev2num(y);
						if (x < y) {
							return 1;
						}
						if (x > y) {
							return -1;
						}
						return 0;
					};
					jQuery.fn.dataTableExt.oSort['severity-desc'] = function(y, x) {
						x = WFAD.sev2num(x);
						y = WFAD.sev2num(y);
						if (x > y) {
							return 1;
						}
						if (x < y) {
							return -1;
						}
						return 0;
					};

					jQuery('#' + tableID).dataTable({
						"bFilter": false,
						"bInfo": false,
						"bPaginate": false,
						"bLengthChange": false,
						"bAutoWidth": false,
						"aaData": res.issuesLists[issueStatus],
						"aoColumns": [
							{
								"sTitle": '<div class="th_wrapp">Severity</div>',
								"sWidth": '128px',
								"sClass": "center",
								"sType": 'severity',
								"fnRender": function(obj) {
									var cls = 'wfProbSev' + obj.aData.severity;
									return '<span class="' + cls + '"></span>';
								}
							},
							{
								"sTitle": '<div class="th_wrapp">Issue</div>',
								"bSortable": false,
								"sWidth": '400px',
								"sType": 'html',
								fnRender: function(obj) {
									var tmplName = 'issueTmpl_' + obj.aData.type;
									return jQuery('#' + tmplName).tmpl(obj.aData).html();
								}
							}
						]
					});
				}
				if (callback) {
					jQuery('#wfIssues_' + this.visibleIssuesPanel).fadeIn(500, function() {
						callback();
					});
				} else {
					jQuery('#wfIssues_' + this.visibleIssuesPanel).fadeIn(500);
				}
				return true;
			},
			ajax: function(action, data, cb, cbErr, noLoading) {
				if (typeof(data) == 'string') {
					if (data.length > 0) {
						data += '&';
					}
					data += 'action=' + action + '&nonce=' + this.nonce;
				} else if (typeof(data) == 'object') {
					data['action'] = action;
					data['nonce'] = this.nonce;
				}
				if (!cbErr) {
					cbErr = function() {
					};
				}
				var self = this;
				if (!noLoading) {
					this.showLoading();
				}
				jQuery.ajax({
					type: 'POST',
					url: WordfenceAdminVars.ajaxURL,
					dataType: "json",
					data: data,
					success: function(json) {
						if (!noLoading) {
							self.removeLoading();
						}
						if (json && json.nonce) {
							self.nonce = json.nonce;
						}
						if (json && json.errorMsg) {
							self.colorbox('400px', 'An error occurred', json.errorMsg);
						}
						cb(json);
					},
					error: function() {
						if (!noLoading) {
							self.removeLoading();
						}
						cbErr();
					}
				});
			},
			colorbox: function(width, heading, body) {
				this.colorboxQueue.push([width, heading, body]);
				this.colorboxServiceQueue();
			},
			colorboxServiceQueue: function() {
				if (this.colorboxIsOpen) {
					return;
				}
				if (this.colorboxQueue.length < 1) {
					return;
				}
				var elem = this.colorboxQueue.shift();
				this.colorboxOpen(elem[0], elem[1], elem[2]);
			},
			colorboxOpen: function(width, heading, body) {
				this.colorboxIsOpen = true;
				jQuery.colorbox({width: width, html: "<h3>" + heading + "</h3><p>" + body + "</p>"});
			},
			scanRunningMsg: function() {
				this.colorbox('400px', "A scan is running", "A scan is currently in progress. Please wait until it finishes before starting another scan.");
			},
			errorMsg: function(msg) {
				this.colorbox('400px', "An error occurred:", msg);
			},
			bulkOperation: function(op) {
				var self = this;
				if (op == 'del' || op == 'repair') {
					var ids = jQuery('input.wf' + op + 'Checkbox:checked').map(function() {
						return jQuery(this).val();
					}).get();
					if (ids.length < 1) {
						this.colorbox('400px', "No files were selected", "You need to select files to perform a bulk operation. There is a checkbox in each issue that lets you select that file. You can then select a bulk operation and hit the button to perform that bulk operation.");
						return;
					}
					if (op == 'del') {
						this.colorbox('400px', "Are you sure you want to delete?", "Are you sure you want to delete a total of " + ids.length + " files? Do not delete files on your system unless you're ABSOLUTELY sure you know what you're doing. If you delete the wrong file it could cause your WordPress website to stop functioning and you will probably have to restore from backups. If you're unsure, Cancel and work with your hosting provider to clean your system of infected files.<br /><br /><input type=\"button\" value=\"Delete Files\" onclick=\"WFAD.bulkOperationConfirmed('" + op + "');\" />&nbsp;&nbsp;<input type=\"button\" value=\"Cancel\" onclick=\"jQuery.colorbox.close();\" /><br />");
					} else if (op == 'repair') {
						this.colorbox('400px', "Are you sure you want to repair?", "Are you sure you want to repair a total of " + ids.length + " files? Do not repair files on your system unless you're sure you have reviewed the differences between the original file and your version of the file in the files you are repairing. If you repair a file that has been customized for your system by a developer or your hosting provider it may leave your system unusable. If you're unsure, Cancel and work with your hosting provider to clean your system of infected files.<br /><br /><input type=\"button\" value=\"Repair Files\" onclick=\"WFAD.bulkOperationConfirmed('" + op + "');\" />&nbsp;&nbsp;<input type=\"button\" value=\"Cancel\" onclick=\"jQuery.colorbox.close();\" /><br />");
					}
				} else {
					return;
				}
			},
			bulkOperationConfirmed: function(op) {
				jQuery.colorbox.close();
				var self = this;
				this.ajax('wordfence_bulkOperation', {
					op: op,
					ids: jQuery('input.wf' + op + 'Checkbox:checked').map(function() {
						return jQuery(this).val();
					}).get()
				}, function(res) {
					self.doneBulkOperation(res);
				});
			},
			doneBulkOperation: function(res) {
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorbox('400px', res.bulkHeading, res.bulkBody);
					});
				} else {
					this.loadIssues(function() {
					});
				}
			},
			deleteFile: function(issueID, force) {
				var self = this;
				this.ajax('wordfence_deleteFile', {
					issueID: issueID,
					forceDelete: force
				}, function(res) {
					self.doneDeleteFile(res);
				});
			},
			doneDeleteFile: function(res) {
				var cb = false;
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorbox('400px', "Success deleting file", "The file " + res.file + " was successfully deleted.");
					});
				} else if (res.cerrorMsg) {
					this.loadIssues(function() {
						self.colorbox('400px', 'An error occurred', res.cerrorMsg);
					});
				}
			},
			deleteDatabaseOption: function(issueID) {
				var self = this;
				this.ajax('wordfence_deleteDatabaseOption', {
					issueID: issueID
				}, function(res) {
					self.doneDeleteDatabaseOption(res);
				});
			},
			doneDeleteDatabaseOption: function(res) {
				var cb = false;
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorbox('400px', "Success removing option", "The option " + res.option_name + " was successfully removed.");
					});
				} else if (res.cerrorMsg) {
					this.loadIssues(function() {
						self.colorbox('400px', 'An error occurred', res.cerrorMsg);
					});
				}
			},
			restoreFile: function(issueID) {
				var self = this;
				this.ajax('wordfence_restoreFile', {
					issueID: issueID
				}, function(res) {
					self.doneRestoreFile(res);
				});
			},
			doneRestoreFile: function(res) {
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorbox("400px", "File restored OK", "The file " + res.file + " was restored successfully.");
					});
				} else if (res.cerrorMsg) {
					this.loadIssues(function() {
						self.colorbox('400px', 'An error occurred', res.cerrorMsg);
					});
				}
			},
			deleteIssue: function(id) {
				var self = this;
				this.ajax('wordfence_deleteIssue', {id: id}, function(res) {
					self.loadIssues();
				});
			},
			updateIssueStatus: function(id, st) {
				var self = this;
				this.ajax('wordfence_updateIssueStatus', {id: id, 'status': st}, function(res) {
					if (res.ok) {
						self.loadIssues();
					}
				});
			},
			updateAllIssues: function(op) { // deleteIgnored, deleteNew, ignoreAllNew
				var head = "Please confirm";
				var body;
				if (op == 'deleteIgnored') {
					body = "You have chosen to remove all ignored issues. Once these issues are removed they will be re-scanned by Wordfence and if they have not been fixed, they will appear in the 'new issues' list. Are you sure you want to do this?";
				} else if (op == 'deleteNew') {
					body = "You have chosen to mark all new issues as fixed. If you have not really fixed these issues, they will reappear in the new issues list on the next scan. If you have not fixed them and want them excluded from scans you should choose to 'ignore' them instead. Are you sure you want to mark all new issues as fixed?";
				} else if (op == 'ignoreAllNew') {
					body = "You have chosen to ignore all new issues. That means they will be excluded from future scans. You should only do this if you're sure all new issues are not a problem. Are you sure you want to ignore all new issues?";
				} else {
					return;
				}
				this.colorbox('450px', head, body + '<br /><br /><center><input type="button" name="but1" value="Cancel" onclick="jQuery.colorbox.close();" />&nbsp;&nbsp;&nbsp;<input type="button" name="but2" value="Yes I\'m sure" onclick="jQuery.colorbox.close(); WFAD.confirmUpdateAllIssues(\'' + op + '\');" /><br />');
			},
			confirmUpdateAllIssues: function(op) {
				var self = this;
				this.ajax('wordfence_updateAllIssues', {op: op}, function(res) {
					self.loadIssues();
				});
			},
			es: function(val) {
				if (val) {
					return val;
				} else {
					return "";
				}
			},
			noQuotes: function(str) {
				return str.replace(/"/g, '&#34;').replace(/\'/g, '&#145;');
			},
			commify: function(num) {
				return ("" + num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
			},
			switchToLiveTab: function(elem) {
				jQuery('.wfTab1').removeClass('selected');
				jQuery(elem).addClass('selected');
				jQuery('.wfDataPanel').hide();
				var self = this;
				jQuery('#wfActivity').fadeIn(function() {
					self.completeLiveTabSwitch();
				});
			},
			completeLiveTabSwitch: function() {
				this.ajax('wordfence_loadActivityLog', {}, function(res) {
					var html = '<a href="#" class="wfALogMailLink" onclick="WFAD.emailActivityLog(); return false;"></a><a href="#" class="wfALogReloadLink" onclick="WFAD.reloadActivityData(); return false;"></a>';
					if (res.events && res.events.length > 0) {
						jQuery('#wfActivity').empty();
						for (var i = 0; i < res.events.length; i++) {
							var timeTaken = '0.0000';
							if (res.events[i + 1]) {
								timeTaken = (res.events[i].ctime - res.events[i + 1].ctime).toFixed(4);
							}
							var red = "";
							if (res.events[i].type == 'error') {
								red = ' class="wfWarn" ';
							}
							html += '<div ' + red + 'class="wfALogEntry"><span ' + red + 'class="wfALogTime">[' + res.events[i].type + '&nbsp;:&nbsp;' + timeTaken + '&nbsp;:&nbsp;' + res.events[i].timeAgo + ' ago]</span>&nbsp;' + res.events[i].msg + "</div>";
						}
						jQuery('#wfActivity').html(html);
					} else {
						jQuery('#wfActivity').html("<p>&nbsp;&nbsp;No activity to report yet. Please complete your first scan.</p>");
					}
				});
			},
			emailActivityLog: function() {
				this.colorbox('400px', 'Email Wordfence Activity Log', "Enter the email address you would like to send the Wordfence activity log to. Note that the activity log may contain thousands of lines of data. This log is usually only sent to a member of the Wordfence support team. It also contains your PHP configuration from the phpinfo() function for diagnostic data.<br /><br /><input type='text' value='support@wordfence.com' size='20' id='wfALogRecip' /><input type='button' value='Send' onclick=\"WFAD.completeEmailActivityLog();\" /><input type='button' value='Cancel' onclick='jQuery.colorbox.close();' /><br /><br />");
			},
			completeEmailActivityLog: function() {
				jQuery.colorbox.close();
				var email = jQuery('#wfALogRecip').val();
				if (!/^[^@]+@[^@]+$/.test(email)) {
					alert("Please enter a valid email address.");
					return;
				}
				var self = this;
				this.ajax('wordfence_sendActivityLog', {email: jQuery('#wfALogRecip').val()}, function(res) {
					if (res.ok) {
						self.colorbox('400px', 'Activity Log Sent', "Your Wordfence activity log was sent to " + email + "<br /><br /><input type='button' value='Close' onclick='jQuery.colorbox.close();' /><br /><br />");
					}
				});
			},
			reloadActivityData: function() {
				jQuery('#wfActivity').html('<div class="wfLoadingWhite32"></div>'); //&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
				this.completeLiveTabSwitch();
			},
			switchToSummaryTab: function(elem) {
				jQuery('.wfTab1').removeClass('selected');
				jQuery(elem).addClass('selected');
				jQuery('.wfDataPanel').hide();
				jQuery('#wfSummaryTables').fadeIn();
			},
			switchIssuesTab: function(elem, type) {
				jQuery('.wfTab2').removeClass('selected');
				jQuery('.wfIssuesContainer').hide();
				jQuery(elem).addClass('selected');
				this.visibleIssuesPanel = type;
				jQuery('#wfIssues_' + type).fadeIn();
			},
			switchTab: function(tabElement, tabClass, contentClass, selectedContentID, callback) {
				jQuery('.' + tabClass).removeClass('selected');
				jQuery(tabElement).addClass('selected');
				jQuery('.' + contentClass).hide().html('<div class="wfLoadingWhite32"></div>');
				var func = function() {
				};
				if (callback) {
					func = function() {
						callback();
					};
				}
				jQuery('#' + selectedContentID).fadeIn(func);
			},
			activityTabChanged: function() {
				var mode = jQuery('.wfDataPanel:visible')[0].id.replace('wfActivity_', '');
				if (!mode) {
					return;
				}
				this.activityMode = mode;
				this.reloadActivities();
			},
			reloadActivities: function() {
				jQuery('#wfActivity_' + this.activityMode).html('<div class="wfLoadingWhite32"></div>');
				this.newestActivityTime = 0;
				this.updateTicker(true);
			},
			staticTabChanged: function() {
				var mode = jQuery('.wfDataPanel:visible')[0].id.replace('wfActivity_', '');
				if (!mode) {
					return;
				}
				this.activityMode = mode;

				var self = this;
				this.ajax('wordfence_loadStaticPanel', {
					mode: this.activityMode
				}, function(res) {
					self.completeLoadStaticPanel(res);
				});
			},
			completeLoadStaticPanel: function(res) {
				var contentElem = '#wfActivity_' + this.activityMode;
				jQuery(contentElem).empty();
				if (res.results && res.results.length > 0) {
					var tmpl;
					if (this.activityMode == 'topScanners' || this.activityMode == 'topLeechers') {
						tmpl = '#wfLeechersTmpl';
					} else if (this.activityMode == 'blockedIPs') {
						tmpl = '#wfBlockedIPsTmpl';
					} else if (this.activityMode == 'lockedOutIPs') {
						tmpl = '#wfLockedOutIPsTmpl';
					} else if (this.activityMode == 'throttledIPs') {
						tmpl = '#wfThrottledIPsTmpl';
					} else {
						return;
					}
					var i, j, chunk = 1000;
					var bigArray = res.results.slice(0);
					res.results = false;
					for (i = 0, j = bigArray.length; i < j; i += chunk) {
						res.results = bigArray.slice(i, i + chunk);
						jQuery(tmpl).tmpl(res).appendTo(contentElem);
					}
					this.reverseLookupIPs();
				} else {
					if (this.activityMode == 'topScanners' || this.activityMode == 'topLeechers') {
						jQuery(contentElem).html("No site hits have been logged yet. Check back soon.");
					} else if (this.activityMode == 'blockedIPs') {
						jQuery(contentElem).html("No IP addresses have been blocked yet. If you manually block an IP address or if Wordfence automatically blocks one, it will appear here.");
					} else if (this.activityMode == 'lockedOutIPs') {
						jQuery(contentElem).html("No IP addresses have been locked out from signing in or using the password recovery system.");
					} else if (this.activityMode == 'throttledIPs') {
						jQuery(contentElem).html("No IP addresses have been throttled yet. If an IP address accesses the site too quickly and breaks one of the Wordfence rules, it will appear here.");
					} else {
						return;
					}
				}
			},
			loadPasswdAuditResults: function() {
				var self = this;
				this.ajax('wordfence_passwdLoadResults', {}, function(res) {
					self.displayPWAuditResults(res);
				});
			},
			doPasswdAuditUpdate: function(freq) {
				this.loadPasswdAuditJobs();
				this.loadPasswdAuditResults();
			},
			stopPasswdAuditUpdate: function() {
				clearInterval(this.passwdAuditUpdateInt);
			},
			killPasswdAudit: function(jobID) {
				var self = this;
				this.ajax('wordfence_killPasswdAudit', {jobID: jobID}, function(res) {
					if (res.ok) {
						self.colorbox('300px', "Stop Requested", "We have sent a request to stop the password audit in progress. It may take a few minutes before results stop appearing. You can immediately start another audit if you'd like.");
					}
				});
			},
			displayPWAuditResults: function(res) {
				if (res && res.results && res.results.length > 0) {
					var wfAuditResults = $('#wfAuditResults');
					jQuery('#wfAuditResults').empty();
					jQuery('#wfAuditResultsTable').tmpl().appendTo(wfAuditResults);
					var wfAuditResultsBody = wfAuditResults.find('.wf-pw-audit-tbody');
					for (var i = 0; i < res.results.length; i++) {
						jQuery('#wfAuditResultsRow').tmpl(res.results[i]).appendTo(wfAuditResultsBody);
					}
				} else {
					jQuery('#wfAuditResults').empty().html("<p>You don't have any user accounts with a weak password at this time.</p>");
				}
			},
			loadPasswdAuditJobs: function() {
				var self = this;
				this.ajax('wordfence_passwdLoadJobs', {}, function(res) {
					if (res && res.results && res.results.length > 0) {
						var stat = res.results[0].jobStatus;
						if (stat == 'running' || stat == 'queued') {
							setTimeout(function() {
								self.doPasswdAuditUpdate()
							}, 10000);
						}
					}

					self.displayPWAuditJobs(res);
				});
			},
			deletePasswdAudit: function(jobID) {
				var self = this;
				this.ajax('wordfence_deletePasswdAudit', {jobID: jobID}, function(res) {
					self.loadPasswdAuditJobs(res);
				});
			},
			doFixWeakPasswords: function() {
				var self = this;
				var mode = jQuery('#wfPasswdFixAction').val();
				var ids = jQuery('input.wfUserCheck:checked').map(function() {
					return jQuery(this).val();
				}).get();
				if (ids.length < 1) {
					self.colorbox('300px', "Please select users", "You did not select any users from the list. Select which site members you want to email or to change their passwords.");
					return;
				}
				this.ajax('wordfence_weakPasswordsFix', {
					mode: mode,
					ids: ids.join(',')
				}, function(res) {
					if (res.ok && res.title && res.msg) {
						self.colorbox('300px', res.title, res.msg);
					}
				});
			},
			ucfirst: function(str) {
				str = "" + str;
				return str.charAt(0).toUpperCase() + str.slice(1);
			},
			makeIPTrafLink: function(IP) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=IPTraf&nonce=' + this.nonce + '&IP=' + encodeURIComponent(IP);
			},
			makeDiffLink: function(dat) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=diff&nonce=' + this.nonce +
					'&file=' + encodeURIComponent(this.es(dat['file'])) +
					'&cType=' + encodeURIComponent(this.es(dat['cType'])) +
					'&cKey=' + encodeURIComponent(this.es(dat['cKey'])) +
					'&cName=' + encodeURIComponent(this.es(dat['cName'])) +
					'&cVersion=' + encodeURIComponent(this.es(dat['cVersion']));
			},
			makeViewFileLink: function(file) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=view&nonce=' + this.nonce + '&file=' + encodeURIComponent(file);
			},
			makeViewOptionLink: function(option, siteID) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=viewOption&nonce=' + this.nonce + '&option=' + encodeURIComponent(option) + '&site_id=' + encodeURIComponent(siteID);
			},
			makeTimeAgo: function(t) {
				var months = Math.floor(t / (86400 * 30));
				var days = Math.floor(t / 86400);
				var hours = Math.floor(t / 3600);
				var minutes = Math.floor(t / 60);
				if (months > 0) {
					days -= months * 30;
					return this.pluralize(months, 'month', days, 'day');
				} else if (days > 0) {
					hours -= days * 24;
					return this.pluralize(days, 'day', hours, 'hour');
				} else if (hours > 0) {
					minutes -= hours * 60;
					return this.pluralize(hours, 'hour', minutes, 'min');
				} else if (minutes > 0) {
					//t -= minutes * 60;
					return this.pluralize(minutes, 'minute');
				} else {
					return Math.round(t) + " seconds";
				}
			},
			pluralize: function(m1, t1, m2, t2) {
				if (m1 != 1) {
					t1 = t1 + 's';
				}
				if (m2 != 1) {
					t2 = t2 + 's';
				}
				if (m1 && m2) {
					return m1 + ' ' + t1 + ' ' + m2 + ' ' + t2;
				} else {
					return m1 + ' ' + t1;
				}
			},
			calcRangeTotal: function() {
				var range = jQuery('#ipRange').val();
				if (!range) {
					return;
				}
				range = range.replace(/ /g, '');
				if (range && /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\s*\-\s*\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/.test(range)) {
					var ips = range.split('-');
					var total = this.inet_aton(ips[1]) - this.inet_aton(ips[0]) + 1;
					if (total < 1) {
						jQuery('#wfShowRangeTotal').html("<span style=\"color: #F00;\">Invalid. Starting IP is greater than ending IP.</span>");
						return;
					}
					jQuery('#wfShowRangeTotal').html("<span style=\"color: #0A0;\">Valid: " + total + " addresses in range.</span>");
				} else {
					jQuery('#wfShowRangeTotal').empty();
				}
			},
			loadBlockRanges: function() {
				var self = this;
				this.ajax('wordfence_loadBlockRanges', {}, function(res) {
					self.completeLoadBlockRanges(res);
				});

			},
			completeLoadBlockRanges: function(res) {
				jQuery('#currentBlocks').empty();
				if (res.results && res.results.length > 0) {
					jQuery('#wfBlockedRangesTmpl').tmpl(res).prependTo('#currentBlocks');
				} else {
					jQuery('#currentBlocks').html("You have not blocked any IP ranges or other patterns yet.");
				}
			},
			whois: function(val) {
				val = val.replace(' ', '');
				if (!/\w+/.test(val)) {
					this.colorbox('300px', "Enter a valid IP or domain", "Please enter a valid IP address or domain name for your whois lookup.");
					return;
				}
				var self = this;
				jQuery('#whoisbutton').attr('disabled', 'disabled');
				jQuery('#whoisbutton').attr('value', 'Loading...');
				this.ajax('wordfence_whois', {
					val: val
				}, function(res) {
					jQuery('#whoisbutton').removeAttr('disabled');
					jQuery('#whoisbutton').attr('value', 'Look up IP or Domain');
					if (res.ok) {
						self.completeWhois(res);
					}
				});
			},
			completeWhois: function(res) {
				var self = this;
				if (res.ok && res.result && res.result.rawdata && res.result.rawdata.length > 0) {
					var rawhtml = "";
					for (var i = 0; i < res.result.rawdata.length; i++) {
						res.result.rawdata[i] = jQuery('<div />').text(res.result.rawdata[i]).html();
						res.result.rawdata[i] = res.result.rawdata[i].replace(/([^\s\t\r\n:;]+@[^\s\t\r\n:;\.]+\.[^\s\t\r\n:;]+)/, "<a href=\"mailto:$1\">$1<\/a>");
						res.result.rawdata[i] = res.result.rawdata[i].replace(/(https?:\/\/[^\/]+[^\s\r\n\t]+)/, "<a target=\"_blank\" href=\"$1\">$1<\/a>");
						var redStyle = "";
						if (this.getQueryParam('wfnetworkblock')) {
							redStyle = " style=\"color: #F00;\"";
						}

						function wfm21(str, ipRange, offset, totalStr) {
							var ips = ipRange.split(/\s*\-\s*/);
							var totalIPs = NaN;
							if (ips[0].indexOf(':') < 0) {
								var ip1num = self.inet_aton(ips[0]);
								var ip2num = self.inet_aton(ips[1]);
								totalIPs = ip2num - ip1num + 1;
							}
							return "<a href=\"admin.php?page=WordfenceRangeBlocking&wfBlockRange=" + ipRange + "\"" + redStyle + ">" + ipRange + " [" + (!isNaN(totalIPs) ? "<strong>" + totalIPs + "</strong> addresses in this network. " : "") + "Click to block this network]<\/a>";
						}

						function buildRangeLink2(str, octet1, octet2, octet3, octet4, cidrRange) {

							octet3 = octet3.length > 0 ? octet3 : '0';
							octet4 = octet4.length > 0 ? octet4 : '0';

							var rangeStart = [octet1, octet2, octet3, octet4].join('.');
							var rangeStartNum = self.inet_aton(rangeStart);
							cidrRange = parseInt(cidrRange, 10);
							if (!isNaN(rangeStartNum) && cidrRange > 0 && cidrRange < 32) {
								var rangeEndNum = rangeStartNum;
								for (var i = 32, j = 1; i >= cidrRange; i--, j *= 2) {
									rangeEndNum |= j;
								}
								rangeEndNum = rangeEndNum >>> 0;
								var ipRange = self.inet_ntoa(rangeStartNum) + '-' + self.inet_ntoa(rangeEndNum);
								var totalIPs = rangeEndNum - rangeStartNum;
								return "<a href=\"admin.php?page=WordfenceRangeBlocking&wfBlockRange=" + ipRange + "\"" + redStyle + ">" + ipRange + " [" + (!isNaN(totalIPs) ? "<strong>" + totalIPs + "</strong> addresses in this network. " : "") + "Click to block this network]<\/a>";
							}
							return str;
						}

						res.result.rawdata[i] = res.result.rawdata[i].replace(/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3} - \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|[a-f0-9:.]{3,} - [a-f0-9:.]{3,})/i, wfm21);
						res.result.rawdata[i] = res.result.rawdata[i].replace(/(\d{1,3})\.(\d{1,3})\.?(\d{0,3})\.?(\d{0,3})\/(\d{1,3})/i, buildRangeLink2);
						rawhtml += res.result.rawdata[i] + "<br />";
					}
					jQuery('#wfrawhtml').html(rawhtml);
				} else {
					jQuery('#wfrawhtml').html('<span style="color: #F00;">Sorry, but no data for that IP or domain was found.</span>');
				}
			},
			blockIPUARange: function(ipRange, hostname, uaRange, referer, reason) {
				if (!/\w+/.test(reason)) {
					this.colorbox('300px', "Please specify a reason", "You forgot to include a reason you're blocking this IP range. We ask you to include this for your own record keeping.");
					return;
				}
				ipRange = ipRange.replace(/ /g, '').toLowerCase();
				if (ipRange) {
					var range = ipRange.split('-'),
						validRange;
					if (range.length !== 2) {
						validRange = false;
					} else if (range[0].match(':')) {
						validRange = this.inet_pton(range[0]) !== false && this.inet_pton(range[1]) !== false;
					} else if (range[0].match('.')) {
						validRange = this.inet_aton(range[0]) !== false && this.inet_aton(range[1]) !== false;
					}
					if (!validRange) {
						this.colorbox('300px', 'Specify a valid IP range', "Please specify a valid IP address range in the form of \"1.2.3.4 - 1.2.3.5\" without quotes. Make sure the dash between the IP addresses in a normal dash (a minus sign on your keyboard) and not another character that looks like a dash.");
						return;
					}
				}
				if (hostname && !/^[a-z0-9\.\*\-]+$/i.test(hostname)) {
					this.colorbox('300px', 'Specify a valid hostname', '<i>' + this.htmlEscape(hostname) + '</i> is not valid hostname');
					return;
				}
				if (!(/\w+/.test(ipRange) || /\w+/.test(uaRange) || /\w+/.test(referer) || /\w+/.test(hostname))) {
					this.colorbox('300px', 'Specify an IP range, Hostname or Browser pattern', "Please specify either an IP address range, Hostname or a web browser pattern to match.");
					return;
				}
				var self = this;
				this.ajax('wordfence_blockIPUARange', {
					ipRange: ipRange,
					hostname: hostname,
					uaRange: uaRange,
					referer: referer,
					reason: reason
				}, function(res) {
					if (res.ok) {
						self.loadBlockRanges();
						return;
					}
				});
			},
			unblockRange: function(id) {
				var self = this;
				this.ajax('wordfence_unblockRange', {
					id: id
				}, function(res) {
					self.loadBlockRanges();
				});
			},
			blockIP: function(IP, reason) {
				var self = this;
				this.ajax('wordfence_blockIP', {
					IP: IP,
					reason: reason
				}, function(res) {
					if (res.errorMsg) {
						return;
					} else {
						self.reloadActivities();
					}
				});
			},
			blockIPTwo: function(IP, reason, perm) {
				var self = this;
				this.ajax('wordfence_blockIP', {
					IP: IP,
					reason: reason,
					perm: (perm ? '1' : '0')
				}, function(res) {
					if (res.errorMsg) {
						return;
					} else {
						self.staticTabChanged();
					}
				});
			},
			unlockOutIP: function(IP) {
				var self = this;
				this.ajax('wordfence_unlockOutIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			unblockIP: function(IP) {
				var self = this;
				this.ajax('wordfence_unblockIP', {
					IP: IP
				}, function(res) {
					self.reloadActivities();
				});
			},
			unblockNetwork: function(id) {
				var self = this;
				this.ajax('wordfence_unblockRange', {
					id: id
				}, function(res) {
					self.reloadActivities();
				});
			},
			unblockIPTwo: function(IP) {
				var self = this;
				this.ajax('wordfence_unblockIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			permBlockIP: function(IP) {
				var self = this;
				this.ajax('wordfence_permBlockIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			makeElemID: function() {
				return 'wfElemGen' + this.elementGeneratorIter++;
			},
			pulse: function(sel) {
				jQuery(sel).fadeIn(function() {
					setTimeout(function() {
						jQuery(sel).fadeOut();
					}, 2000);
				});
			},
			getCacheStats: function() {
				var self = this;
				this.ajax('wordfence_getCacheStats', {}, function(res) {
					if (res.ok) {
						self.colorbox('400px', res.heading, res.body);
					}
				});
			},
			clearPageCache: function() {
				var self = this;
				this.ajax('wordfence_clearPageCache', {}, function(res) {
					if (res.ok) {
						self.colorbox('400px', res.heading, res.body);
					}
				});
			},
			switchToFalcon: function() {
				var self = this;
				this.ajax('wordfence_checkFalconHtaccess', {}, function(res) {
					if (res.ok) {
						self.colorbox('400px', "Enabling Falcon Engine", 'First read this <a href="http://www.wordfence.com/introduction-to-wordfence-falcon-engine/" target="_blank">Introduction to Falcon Engine</a>. Falcon modifies your website configuration file which is called your .htaccess file. To enable Falcon we ask that you make a backup of this file. This is a safety precaution in case for some reason Falcon is not compatible with your site.<br /><br /><a href="' + WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadHtaccess&nonce=' + self.nonce + '" onclick="jQuery(\'#wfNextBut\').prop(\'disabled\', false); return true;">Click here to download a backup copy of your .htaccess file now</a><br /><br /><input type="button" name="but1" id="wfNextBut" value="Click to Enable Falcon Engine" disabled="disabled" onclick="WFAD.confirmSwitchToFalcon(0);" />');
					} else if (res.nginx) {
						self.colorbox('400px', "Enabling Falcon Engine", 'You are using an Nginx web server and using a FastCGI processor like PHP5-FPM. To use Falcon you will need to manually modify your nginx.conf configuration file and reload your Nginx server for the changes to take effect. You can find the <a href="http://www.wordfence.com/blog/2014/05/nginx-wordfence-falcon-engine-php-fpm-fastcgi-fast-cgi/" target="_blank">rules you need to make these changes to nginx.conf on this page on wordfence.com</a>. Once you have made these changes, compressed cached files will be served to your visitors directly from Nginx making your site extremely fast. When you have made the changes and reloaded your Nginx server, you can click the button below to enable Falcon.<br /><br /><input type="button" name="but1" id="wfNextBut" value="Click to Enable Falcon Engine" onclick="WFAD.confirmSwitchToFalcon(1);" />');
					} else if (res.err) {
						self.colorbox('400px', "We encountered a problem", "We can't modify your .htaccess file for you because: " + res.err + "<br /><br />Advanced users: If you would like to manually enable Falcon yourself by editing your .htaccess, you can add the rules below to the beginning of your .htaccess file. Then click the button below to enable Falcon. Don't do this unless you understand website configuration.<br /><textarea style='width: 300px; height:100px;' readonly>" + jQuery('<div/>').text(res.code).html() + "</textarea><br /><input type='button' value='Enable Falcon after manually editing .htaccess' onclick='WFAD.confirmSwitchToFalcon(1);' />");
					}
				});
			},
			confirmSwitchToFalcon: function(noEditHtaccess) {
				jQuery.colorbox.close();
				var cacheType = 'falcon';
				var self = this;
				this.ajax('wordfence_saveCacheConfig', {
						cacheType: cacheType,
						noEditHtaccess: noEditHtaccess
					}, function(res) {
						if (res.ok) {
							self.colorbox('400px', res.heading, res.body);
						}
					}
				);
			},
			saveCacheConfig: function() {
				var cacheType = jQuery('input:radio[name=cacheType]:checked').val();
				if (cacheType == 'falcon') {
					return this.switchToFalcon();
				}
				var self = this;
				this.ajax('wordfence_saveCacheConfig', {
						cacheType: cacheType
					}, function(res) {
						if (res.ok) {
							self.colorbox('400px', res.heading, res.body);
						}
					}
				);
			},
			saveCacheOptions: function() {
				var self = this;
				this.ajax('wordfence_saveCacheOptions', {
						allowHTTPSCaching: (jQuery('#wfallowHTTPSCaching').is(':checked') ? 1 : 0),
						addCacheComment: (jQuery('#wfaddCacheComment').is(':checked') ? 1 : 0),
						clearCacheSched: (jQuery('#wfclearCacheSched').is(':checked') ? 1 : 0)
					}, function(res) {
						if (res.updateErr) {
							self.colorbox('400px', "You need to manually update your .htaccess", res.updateErr + "<br />Your option was updated but you need to change the Wordfence code in your .htaccess to the following:<br /><textarea style='width: 300px; height: 120px;'>" + jQuery('<div/>').text(res.code).html() + '</textarea>');
						}
					}
				);
			},
			saveConfig: function() {
				var qstr = jQuery('#wfConfigForm').serialize();
				var self = this;
				jQuery('.wfSavedMsg').hide();
				jQuery('.wfAjax24').show();
				this.ajax('wordfence_saveConfig', qstr, function(res) {
					jQuery('.wfAjax24').hide();
					if (res.ok) {
						if (res['paidKeyMsg']) {
							self.colorbox('400px', "Congratulations! You have been upgraded to Premium Scanning.", "You have upgraded to a Premium API key. Once this page reloads, you can choose which premium scanning options you would like to enable and then click save. Click the button below to reload this page now.<br /><br /><center><input type='button' name='wfReload' value='Reload page and enable Premium options' onclick='window.location.reload(true);' /></center>");
							return;
						} else if (res['reload'] == 'reload' || WFAD.reloadConfigPage) {
							self.colorbox('400px', "Please reload this page", "You selected a config option that requires a page reload. Click the button below to reload this page to update the menu.<br /><br /><center><input type='button' name='wfReload' value='Reload page' onclick='window.location.reload(true);' /></center>");
							return;
						} else {
							self.pulse('.wfSavedMsg');
						}
					} else if (res.errorMsg) {
						return;
					} else {
						self.colorbox('400px', 'An error occurred', 'We encountered an error trying to save your changes.');
					}
				});
			},
			changeSecurityLevel: function() {
				var level = jQuery('#securityLevel').val();
				for (var k in WFSLevels[level].checkboxes) {
					if (k != 'liveTraf_ignorePublishers') {
						jQuery('#' + k).prop("checked", WFSLevels[level].checkboxes[k]);
					}
				}
				for (var k in WFSLevels[level].otherParams) {
					if (!/^(?:apiKey|securityLevel|alertEmails|liveTraf_ignoreUsers|liveTraf_ignoreIPs|liveTraf_ignoreUA|liveTraf_hitsMaxSize|maxMem|maxExecutionTime|actUpdateInterval)$/.test(k)) {
						jQuery('#' + k).val(WFSLevels[level].otherParams[k]);
					}
				}
			},
			clearAllBlocked: function(op) {
				if (op == 'blocked') {
					body = "Are you sure you want to clear all blocked IP addresses and allow visitors from those addresses to access the site again?";
				} else if (op == 'locked') {
					body = "Are you sure you want to clear all locked IP addresses and allow visitors from those addresses to sign in again?";
				} else {
					return;
				}
				this.colorbox('450px', "Please confirm", body +
				'<br /><br /><center><input type="button" name="but1" value="Cancel" onclick="jQuery.colorbox.close();" />&nbsp;&nbsp;&nbsp;' +
				'<input type="button" name="but2" value="Yes I\'m sure" onclick="jQuery.colorbox.close(); WFAD.confirmClearAllBlocked(\'' + op + '\');"><br />');
			},
			confirmClearAllBlocked: function(op) {
				var self = this;
				this.ajax('wordfence_clearAllBlocked', {op: op}, function(res) {
					self.staticTabChanged();
				});
			},
			setOwnCountry: function(code) {
				this.ownCountry = (code + "").toUpperCase();
			},
			loadBlockedCountries: function(str) {
				var codes = str.split(',');
				for (var i = 0; i < codes.length; i++) {
					jQuery('#wfCountryCheckbox_' + codes[i]).prop('checked', true);
				}
			},
			saveCountryBlocking: function() {
				var action = jQuery('#wfBlockAction').val();
				var redirURL = jQuery('#wfRedirURL').val();
				var bypassRedirURL = jQuery('#wfBypassRedirURL').val();
				var bypassRedirDest = jQuery('#wfBypassRedirDest').val();
				var bypassViewURL = jQuery('#wfBypassViewURL').val();

				if (action == 'redir' && (!/^https?:\/\/[^\/]+/i.test(redirURL))) {
					this.colorbox('400px', "Please enter a URL for redirection", "You have chosen to redirect blocked countries to a specific page. You need to enter a URL in the text box provided that starts with http:// or https://");
					return;
				}
				if (bypassRedirURL || bypassRedirDest) {
					if (!(bypassRedirURL && bypassRedirDest)) {
						this.colorbox('400px', "Missing data from form", "If you want to set up a URL that will bypass country blocking, you must enter a URL that a visitor can hit and the destination they will be redirected to. You have only entered one of these components. Please enter both.");
						return;
					}
					if (bypassRedirURL == bypassRedirDest) {
						this.colorbox('400px', "URLs are the same", "The URL that a user hits to bypass country blocking and the URL they are redirected to are the same. This would cause a circular redirect. Please fix this.");
						return;
					}
				}
				if (bypassRedirURL && (!/^(?:\/|http:\/\/)/.test(bypassRedirURL))) {
					this.invalidCountryURLMsg(bypassRedirURL);
					return;
				}
				if (bypassRedirDest && (!/^(?:\/|http:\/\/)/.test(bypassRedirDest))) {
					this.invalidCountryURLMsg(bypassRedirDest);
					return;
				}
				if (bypassViewURL && (!/^(?:\/|http:\/\/)/.test(bypassViewURL))) {
					this.invalidCountryURLMsg(bypassViewURL);
					return;
				}

				var codesArr = [];
				var ownCountryBlocked = false;
				var self = this;
				jQuery('.wfCountryCheckbox').each(function(idx, elem) {
					if (jQuery(elem).is(':checked')) {
						var code = jQuery(elem).val();
						codesArr.push(code);
						if (code == self.ownCountry) {
							ownCountryBlocked = true;
						}
					}
				});
				this.countryCodesToSave = codesArr.join(',');
				if (ownCountryBlocked) {
					this.colorbox('400px', "Please confirm blocking yourself", "You are about to block your own country. This could lead to you being locked out. Please make sure that your user profile on this machine has a current and valid email address and make sure you know what it is. That way if you are locked out, you can send yourself an unlock email. If you're sure you want to block your own country, click 'Confirm' below, otherwise click 'Cancel'.<br />" +
					'<input type="button" name="but1" value="Confirm" onclick="jQuery.colorbox.close(); WFAD.confirmSaveCountryBlocking();" />&nbsp;<input type="button" name="but1" value="Cancel" onclick="jQuery.colorbox.close();" />');
				} else {
					this.confirmSaveCountryBlocking();
				}
			},
			invalidCountryURLMsg: function(URL) {
				this.colorbox('400px', "Invalid URL", "URL's that you provide for bypassing country blocking must start with '/' or 'http://' without quotes. The URL that is invalid is: " + this.htmlEscape(URL));
				return;
			},
			confirmSaveCountryBlocking: function() {
				var action = jQuery('#wfBlockAction').val();
				var redirURL = jQuery('#wfRedirURL').val();
				var loggedInBlocked = jQuery('#wfLoggedInBlocked').is(':checked') ? '1' : '0';
				var loginFormBlocked = jQuery('#wfLoginFormBlocked').is(':checked') ? '1' : '0';
				var restOfSiteBlocked = jQuery('#wfRestOfSiteBlocked').is(':checked') ? '1' : '0';
				var bypassRedirURL = jQuery('#wfBypassRedirURL').val();
				var bypassRedirDest = jQuery('#wfBypassRedirDest').val();
				var bypassViewURL = jQuery('#wfBypassViewURL').val();

				jQuery('.wfAjax24').show();
				var self = this;
				this.ajax('wordfence_saveCountryBlocking', {
					blockAction: action,
					redirURL: redirURL,
					loggedInBlocked: loggedInBlocked,
					loginFormBlocked: loginFormBlocked,
					restOfSiteBlocked: restOfSiteBlocked,
					bypassRedirURL: bypassRedirURL,
					bypassRedirDest: bypassRedirDest,
					bypassViewURL: bypassViewURL,
					codes: this.countryCodesToSave
				}, function(res) {
					jQuery('.wfAjax24').hide();
					self.pulse('.wfSavedMsg');
				});
			},
			paidUsersOnly: function(msg) {
				var pos = jQuery('#paidWrap').position();
				var width = jQuery('#paidWrap').width();
				var height = jQuery('#paidWrap').height();
				jQuery('<div style="position: absolute; left: ' + pos.left + 'px; top: ' + pos.top + 'px; background-color: #FFF; width: ' + width + 'px; height: ' + height + 'px;"><div class="paidInnerMsg">' + msg + ' <a href="https://www.wordfence.com/wordfence-signup/" target="_blank">Click here to upgrade and gain access to this feature.</div></div>').insertAfter('#paidWrap').fadeTo(10000, 0.7);
			},
			sched_modeChange: function() {
				var self = this;
				if (jQuery('#schedMode').val() == 'auto') {
					jQuery('.wfSchedCheckbox').attr('disabled', true);
				} else {
					jQuery('.wfSchedCheckbox').attr('disabled', false);
				}
			},
			sched_shortcut: function(mode) {
				if (jQuery('#schedMode').val() == 'auto') {
					this.colorbox('400px', 'Change the scan mode', "You need to change the scan mode to manually scheduled scans if you want to select scan times.");
					return;
				}
				jQuery('.wfSchedCheckbox').prop('checked', false);
				if (this.schedStartHour === false) {
					this.schedStartHour = Math.floor((Math.random() * 24));
				} else {
					this.schedStartHour++;
					if (this.schedStartHour > 23) {
						this.schedStartHour = 0;
					}
				}
				if (mode == 'onceDaily') {
					for (var i = 0; i <= 6; i++) {
						jQuery('#wfSchedDay_' + i + '_' + this.schedStartHour).attr('checked', true);
					}
				} else if (mode == 'twiceDaily') {
					var secondHour = this.schedStartHour + 12;
					if (secondHour >= 24) {
						secondHour = secondHour - 24;
					}
					for (var i = 0; i <= 6; i++) {
						jQuery('#wfSchedDay_' + i + '_' + this.schedStartHour).attr('checked', true);
						jQuery('#wfSchedDay_' + i + '_' + secondHour).attr('checked', true);
					}
				} else if (mode == 'oddDaysWE') {
					var startDay = Math.floor((Math.random()));
					jQuery('#wfSchedDay_1_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_3_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_5_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_6_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_0_' + this.schedStartHour).attr('checked', true);
				} else if (mode == 'weekends') {
					var startDay = Math.floor((Math.random()));
					jQuery('#wfSchedDay_6_' + this.schedStartHour).attr('checked', true);
					jQuery('#wfSchedDay_0_' + this.schedStartHour).attr('checked', true);
				} else if (mode == 'every6hours') {
					for (var i = 0; i <= 6; i++) {
						for (var hour = this.schedStartHour; hour < this.schedStartHour + 24; hour = hour + 6) {
							var displayHour = hour;
							if (displayHour >= 24) {
								displayHour = displayHour - 24;
							}
							jQuery('#wfSchedDay_' + i + '_' + displayHour).attr('checked', true);
						}
					}
				}

			},
			sched_save: function() {
				var schedMode = jQuery('#schedMode').val();
				var schedule = [];
				for (var day = 0; day <= 6; day++) {
					var hours = [];
					for (var hour = 0; hour <= 23; hour++) {
						var elemID = '#wfSchedDay_' + day + '_' + hour;
						hours[hour] = jQuery(elemID).is(':checked') ? '1' : '0';
					}
					schedule[day] = hours.join(',');
				}
				var scheduleTxt = schedule.join('|');
				var self = this;
				this.ajax('wordfence_saveScanSchedule', {
					schedMode: schedMode,
					schedTxt: scheduleTxt
				}, function(res) {
					jQuery('#wfScanStartTime').html(res.nextStart);
					jQuery('.wfAjax24').hide();
					self.pulse('.wfSaveMsg');
				});
			},
			twoFacStatus: function(msg) {
				jQuery('#wfTwoFacMsg').html(msg);
				jQuery('#wfTwoFacMsg').fadeIn(function() {
					setTimeout(function() {
						jQuery('#wfTwoFacMsg').fadeOut();
					}, 2000);
				});
			},
			addTwoFactor: function(username, phone) {
				var self = this;
				this.ajax('wordfence_addTwoFactor', {
					username: username,
					phone: phone
				}, function(res) {
					if (res.ok) {
						self.twoFacStatus('User added! Check the user\'s phone to get the activation code.');
						jQuery('<div id="twoFacCont_' + res.userID + '">' + jQuery('#wfTwoFacUserTmpl').tmpl(res).html() + '</div>').prependTo(jQuery('#wfTwoFacUsers'));
					}
				});
			},
			twoFacActivate: function(userID, code) {
				var self = this;
				this.ajax('wordfence_twoFacActivate', {
					userID: userID,
					code: code
				}, function(res) {
					if (res.ok) {
						jQuery('#twoFacCont_' + res.userID).html(
							jQuery('#wfTwoFacUserTmpl').tmpl(res)
						);
						self.twoFacStatus('Cellphone Sign-in activated for user.');
					}
				});
			},
			delTwoFac: function(userID) {
				this.ajax('wordfence_twoFacDel', {
					userID: userID
				}, function(res) {
					if (res.ok) {
						jQuery('#twoFacCont_' + res.userID).fadeOut(function() {
							jQuery(this).remove();
						});
					}
				});
			},
			loadTwoFactor: function() {
				this.ajax('wordfence_loadTwoFactor', {}, function(res) {
					if (res.users && res.users.length > 0) {
						for (var i = 0; i < res.users.length; i++) {
							jQuery('<div id="twoFacCont_' + res.users[i].userID + '">' +
							jQuery('#wfTwoFacUserTmpl').tmpl(res.users[i]).html() + '</div>').appendTo(jQuery('#wfTwoFacUsers'));
						}
					}
				});
			},
			getQueryParam: function(name) {
				name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
				var regexS = "[\\?&]" + name + "=([^&#]*)";
				var regex = new RegExp(regexS);
				var results = regex.exec(window.location.search);
				if (results == null) {
					return "";
				} else {
					return decodeURIComponent(results[1].replace(/\+/g, " "));
				}
			},
			inet_aton: function(dot) {
				var d = dot.split('.');
				return ((((((+d[0]) * 256) + (+d[1])) * 256) + (+d[2])) * 256) + (+d[3]);
			},
			inet_ntoa: function(num) {
				var d = num % 256;
				for (var i = 3; i > 0; i--) {
					num = Math.floor(num / 256);
					d = num % 256 + '.' + d;
				}
				return d;
			},

			inet_pton: function(a) {
				//  discuss at: http://phpjs.org/functions/inet_pton/
				// original by: Theriault
				//   example 1: inet_pton('::');
				//   returns 1: '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0'
				//   example 2: inet_pton('127.0.0.1');
				//   returns 2: '\x7F\x00\x00\x01'

				var r, m, x, i, j, f = String.fromCharCode;
				m = a.match(/^(?:\d{1,3}(?:\.|$)){4}/); // IPv4
				if (m) {
					m = m[0].split('.');
					m = f(m[0]) + f(m[1]) + f(m[2]) + f(m[3]);
					// Return if 4 bytes, otherwise false.
					return m.length === 4 ? m : false;
				}
				r = /^((?:[\da-f]{1,4}(?::|)){0,8})(::)?((?:[\da-f]{1,4}(?::|)){0,8})$/;
				m = a.match(r); // IPv6
				if (m) {
					// Translate each hexadecimal value.
					for (j = 1; j < 4; j++) {
						// Indice 2 is :: and if no length, continue.
						if (j === 2 || m[j].length === 0) {
							continue;
						}
						m[j] = m[j].split(':');
						for (i = 0; i < m[j].length; i++) {
							m[j][i] = parseInt(m[j][i], 16);
							// Would be NaN if it was blank, return false.
							if (isNaN(m[j][i])) {
								return false; // Invalid IP.
							}
							m[j][i] = f(m[j][i] >> 8) + f(m[j][i] & 0xFF);
						}
						m[j] = m[j].join('');
					}
					x = m[1].length + m[3].length;
					if (x === 16) {
						return m[1] + m[3];
					} else if (x < 16 && m[2].length > 0) {
						return m[1] + (new Array(16 - x + 1))
								.join('\x00') + m[3];
					}
				}
				return false; // Invalid IP.
			},
			inet_ntop: function(a) {
				//  discuss at: http://phpjs.org/functions/inet_ntop/
				// original by: Theriault
				//   example 1: inet_ntop('\x7F\x00\x00\x01');
				//   returns 1: '127.0.0.1'
				//   example 2: inet_ntop('\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\1');
				//   returns 2: '::1'

				var i = 0,
					m = '',
					c = [];
				a += '';
				if (a.length === 4) { // IPv4
					return [
						a.charCodeAt(0), a.charCodeAt(1), a.charCodeAt(2), a.charCodeAt(3)].join('.');
				} else if (a.length === 16) { // IPv6
					for (i = 0; i < 16; i++) {
						c.push(((a.charCodeAt(i++) << 8) + a.charCodeAt(i))
							.toString(16));
					}
					return c.join(':')
						.replace(/((^|:)0(?=:|$))+:?/g, function(t) {
							m = (t.length > m.length) ? t : m;
							return t;
						})
						.replace(m || ' ', '::');
				} else { // Invalid length
					return false;
				}
			},

			removeCacheExclusion: function(id) {
				this.ajax('wordfence_removeCacheExclusion', {id: id}, function(res) {
					window.location.reload(true);
				});
			},
			addCacheExclusion: function(patternType, pattern) {
				if (/^https?:\/\//.test(pattern)) {
					this.colorbox('400px', "Incorrect pattern for exclusion", "You can not enter full URL's for exclusion from caching. You entered a full URL that started with http:// or https://. You must enter relative URL's e.g. /exclude/this/page/. You can also enter text that might be contained in the path part of a URL or at the end of the path part of a URL.");
					return;
				}

				this.ajax('wordfence_addCacheExclusion', {
					patternType: patternType,
					pattern: pattern
				}, function(res) {
					if (res.ok) { //Otherwise errorMsg will get caught
						window.location.reload(true);
					}
				});
			},
			loadCacheExclusions: function() {
				this.ajax('wordfence_loadCacheExclusions', {}, function(res) {
					if (res.ex instanceof Array && res.ex.length > 0) {
						for (var i = 0; i < res.ex.length; i++) {
							var newElem = jQuery('#wfCacheExclusionTmpl').tmpl(res.ex[i]);
							newElem.prependTo('#wfCacheExclusions').fadeIn();
						}
						jQuery('<h2>Cache Exclusions</h2>').prependTo('#wfCacheExclusions');
					} else {
						jQuery('<h2>Cache Exclusions</h2><p style="width: 500px;">There are not currently any exclusions. If you have a site that does not change often, it is perfectly normal to not have any pages you want to exclude from the cache.</p>').prependTo('#wfCacheExclusions');
					}

				});
			},
			exportSettings: function() {
				var self = this;
				this.ajax('wordfence_exportSettings', {}, function(res) {
					if (res.ok && res.token) {
						self.colorbox('400px', "Export Successful", "We successfully exported your site settings. To import your site settings on another site, copy and paste the token below into the import text box on the destination site. Keep this token secret. It is like a password. If anyone else discovers the token it will allow them to import your settings excluding your API key.<br /><br />Token:<input type=\"text\" size=\"20\" value=\"" + res.token + "\" onclick=\"this.select();\" /><br />");
					} else if (res.err) {
						self.colorbox('400px', "Error during Export", res.err);
					} else {
						self.colorbox('400px', "An unknown error occurred", "An unknown error occurred during the export. We received an undefined error from your web server.");
					}
				});
			},
			importSettings: function(token) {
				var self = this;
				this.ajax('wordfence_importSettings', {token: token}, function(res) {
					if (res.ok) {
						self.colorbox('400px', "Import Successful", "You successfully imported " + res.totalSet + " options. Your import is complete. Please reload this page or click the button below to reload it:<br /><br /><input type=\"button\" value=\"Reload Page\" onclick=\"window.location.reload(true);\" />");
					} else if (res.err) {
						self.colorbox('400px', "Error during Import", res.err);
					} else {
						self.colorbox('400px', "Error during Export", "An unknown error occurred during the import");
					}
				});
			},
			startPasswdAudit: function(auditType, emailAddr) {
				var self = this;
				this.ajax('wordfence_startPasswdAudit', {auditType: auditType, emailAddr: emailAddr}, function(res) {
					self.loadPasswdAuditJobs();
					if (res.ok) {
						self.colorbox('400px', "Password Audit Started", "Your password audit started successfully. The results will appear here once it is complete. You will also receive an email letting you know the results are ready at: " + emailAddr);
					} else if (!res.errorMsg) { //error displayed
						self.colorbox('400px', "Error Starting Audit", "An unknown error occurred when trying to start your password audit.");
					}
				});
			},
			windowHasFocus: function() {
				if (typeof document.hasFocus === 'function') {
					return document.hasFocus();
				}
				// Older versions of Opera
				return this._windowHasFocus;
			},

			htmlEscape: function(html) {
				return String(html)
					.replace(/&/g, '&amp;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#39;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;');
			},

			permanentlyBlockAllIPs: function(type) {
				var self = this;
				this.ajax('wordfence_permanentlyBlockAllIPs', {
					type: type
				}, function(res) {
					$('#wfTabs').find('.wfTab1').eq(0).trigger('click');
				});
			},

			showTimestamp: function(timestamp, serverTime, format) {
				serverTime = serverTime === undefined ? new Date().getTime() / 1000 : serverTime;
				format = format === undefined ? '${dateTime} (${timeAgo} ago)' : format;
				var date = new Date(timestamp * 1000);

				return jQuery.tmpl(format, {
					dateTime: date.toLocaleDateString() + ' ' + date.toLocaleTimeString(),
					timeAgo: this.makeTimeAgo(serverTime - timestamp)
				});
			},

			updateTimeAgo: function() {
				var self = this;
				jQuery('.wfTimeAgo-timestamp').each(function(idx, elem) {
					var el = jQuery(elem);
					var timestamp = el.data('wfctime');
					if (!timestamp) {
						timestamp = el.attr('data-timestamp');
					}
					var serverTime = (new Date().getTime() / 1000) - self.serverTimestampOffset;
					var format = el.data('wfformat');
					if (!format) {
						format = el.attr('data-format');
					}
					el.html(self.showTimestamp(timestamp, serverTime, format));
				});
			}
		};
		window['WFAD'] = window['wordfenceAdmin'];

		setInterval(function() {
			WFAD.updateTimeAgo();
		}, 1000);
	}
	jQuery(function() {
		wordfenceAdmin.init();
	});
})(jQuery);
