jQuery(document).ready(function () {
    const wooActive = window.yayRecommended.woo_active;
    if(wooActive === '') {
        getData('featured');
    } else {
        getData('woocommerce');
    }
    
    jQuery(".yay-recommended-plugins-layout .plugin-install-tab").on("click", function () {
        if(jQuery(this).children().hasClass("current") === false) {
            getData(jQuery(this).attr("data-tab"));
            jQuery( ".yay-recommended-plugins-layout .plugin-install-tab a" ).each(function() {
                if(jQuery(this).hasClass("current") === true) {
                    jQuery(this).removeClass("current");
                }
            });
            jQuery(this).children().addClass("current");
        }
    })
    jQuery('body').on('click', '.yay-recommended-plugins-layout .plugin-action-buttons .activate-now', function() {
        const file = jQuery(this).attr("data-plugin-file");
        activatePlugin(jQuery(this), file);
    });
    jQuery('body').on('click', '.yay-recommended-plugins-layout .plugin-action-buttons .install-now', function() {
        if(!jQuery(this).hasClass("updating-message") ) {
            const plugin = jQuery(this).attr("data-install-url");
            installPlugin(jQuery(this), plugin);
        }
    });
    jQuery('body').on('click', '.yay-recommended-plugins-layout .plugin-action-buttons .update-now', function() {
        if(!jQuery(this).hasClass("updating-message") ) {
            const plugin = jQuery(this).attr("data-plugin");
            updatePlugin(jQuery(this), plugin);
        }
    });
})
function getData(tab) {
    const loadingHtml = '<div class="loading-content"><span class="spinner is-active"></span></div>';
    jQuery(".yay-recommended-plugins-layout #the-list").html(loadingHtml);
    jQuery(".yay-recommended-plugins-layout .plugin-install-tab").addClass("loading-process");
    jQuery.ajax({
        url: yayRecommended.admin_ajax,
        type: "POST",
        data: {
            action: "yay_recommended_get_plugin_data",
            tab: tab,
            nonce: yayRecommended.nonce
        },
        success: function(response) {
            if(response.success === true) {
                const html = response.data.html;
                jQuery( ".yay-recommended-plugins-layout #the-list" ).html(html);
                jQuery(".yay-recommended-plugins-layout .plugin-install-tab").removeClass("loading-process");
            }
        }
    })
}

function activatePlugin(element,file) {
    element.addClass("button-disabled");
    element.attr("disabled", "disabled");
    element.text("Processing...");
    jQuery.ajax({
        url: yayRecommended.admin_ajax,
        type: "POST",
        data: {
            action: "yay_recommended_activate_plugin",
            file: file,
            nonce: yayRecommended.nonce
        },
        success: function(response) {
            if(response.success === true) {
                const pluginStatus =  jQuery(".yay-recommended-plugins-layout .plugin-status-inactive[data-plugin-file='" + file +"']")
                pluginStatus.text("Active");
                pluginStatus.addClass("plugin-status-active");
                pluginStatus.removeClass("plugin-status-inactive");
                element.removeClass("active-now");
                element.text("Activated");
            } else {
                element.removeClass("button-disabled");
                element.prop("disabled", false);
                element.text("Activated");
            }
        },
    })
}

function installPlugin(element,plugin) {
    element.removeClass("button-primary");
    element.addClass("updating-message");
    element.text("Installing...");
    jQuery.ajax({
        url: yayRecommended.admin_ajax,
        type: "POST",
        data: {
            action: "yay_recommended_upgrade_plugin",
            type: 'install',
            plugin: plugin,
            nonce: yayRecommended.nonce
        },
        success: function(response) {
            if(response.success === true) {
                element.removeClass("updating-message");
                element.addClass("updated-message installed button-disabled");
                element.attr("disabled", "disabled");
                element.removeAttr("data-install-url");
                element.text("Installed!");
                setTimeout(() => {
                    const pluginStatus =  jQuery(".yay-recommended-plugins-layout .plugin-status-not-install[data-plugin-url='" + plugin +"']")
                    pluginStatus.text("Active");
                    pluginStatus.addClass("plugin-status-active");
                    pluginStatus.removeClass("plugin-status-not-install");
                    pluginStatus.removeAttr("data-install-url");
                    element.removeClass("install-now updated-message installed");
                    element.text("Activated");
                    element.removeAttr("aria-label");
                }, 500);
                
            } else {
                element.removeClass("updating-message");
                element.addClass("button-primary");
                element.text("Install Now");
            }
        },
    })
}

function updatePlugin(element,plugin) {
    element.addClass("updating-message");
    element.text("Updating...");
    jQuery.ajax({
        url: yayRecommended.admin_ajax,
        type: "POST",
        data: {
            action: "yay_recommended_upgrade_plugin",
            type: "update",
            plugin: plugin,
            nonce: yayRecommended.nonce
        },
        success: function(response) {
            if(response.success === true) {
                element.removeClass("updating-message");
                element.addClass("updated-message button-disabled");
                element.attr("disabled", "disabled");
                element.text("Updated!");
                if(response.data.active === false) {
                    const pluginStatus =  jQuery(".yay-recommended-plugins-layout .plugin-status-inactive[data-plugin-file='" + plugin +"']");
                    pluginStatus.text("Active");
                    pluginStatus.addClass("plugin-status-active");
                    pluginStatus.removeClass("plugin-status-inactive");
                    pluginStatus.removeAttr("data-plugin-file");
                }
            } else {
                element.removeClass("updating-message");
                element.text("Update Now");
            }
        },
    })
}
