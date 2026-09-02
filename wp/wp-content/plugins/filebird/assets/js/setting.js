jQuery(document).ready(function () {
  const { utils, i18n } = fbv_data;
  const { $message } = utils;

  var njt_auto_run_import = false;
  var fb_tools_page = new URL(fbv_data.auto_import_url);
  fb_tools_page.searchParams.delete("autorun");

  //import from old version
  jQuery(".njt_fbv_import_from_old_now").on("click", function () {
    var $this = jQuery(this);
    if ($this.hasClass("updating-message")) return false;

    $this.addClass("updating-message");

    get_folders(
      function (res) {
        if (res.success) {
          insert_folder(
            res.data.folders,
            0,
            function () {
              $this.removeClass("updating-message");
              const mess = `${i18n.filebird_db_updated} <a href="${fbv_data.media_url}">${fbv_data.i18n.go_to_media}</a>`;
              $message.success(
                (h) => h("span", { domProps: { innerHTML: mess } }),
                5
              );
              if (njt_auto_run_import) {
                location.replace(fb_tools_page.href);
              }
            },
            function () {
              $this.removeClass("updating-message");
            }
          );
        }
      },
      function () {
        $this.removeClass("updating-message");
        $message.error(
          (h) => h("span", { domProps: { innerHTML: i18n.import_failed } }),
          5
        );
      }
    );

    function get_folders(onDone, onFail) {
      jQuery
        .ajax({
          cache: false,
          dataType: "json",
          contentType: "application/json",
          url: fbv_data.json_url + "/fb-get-old-data",
          method: "POST",
          headers: {
            "X-WP-Nonce": fbv_data.rest_nonce,
          },
        })
        .done(function (res) {
          onDone(res);
        })
        .fail(function (res) {
          onFail(res);
        });
    }
    function insert_folder(folders, index, onDone, onFail) {
      if (typeof folders[index] != "undefined") {
        jQuery
          .ajax({
            cache: false,
            dataType: "json",
            contentType: "application/json",
            url: fbv_data.json_url + "/fb-insert-old-data",
            method: "POST",
            headers: {
              "X-WP-Nonce": fbv_data.rest_nonce,
            },
            data: JSON.stringify({
              folders: folders[index],
              autorun: njt_auto_run_import,
            }),
          })
          .done(function (res) {
            insert_folder(folders, index + 1, onDone, onFail);
          })
          .fail(function (res) {
            onFail();
            $message.error(i18n.please_try_again);
          });
      } else {
        onDone();
      }
    }
  });
  //wipe old data
  jQuery(".njt_fbv_wipe_old_data").on("click", function () {
    if (!confirm(fbv_data.i18n.are_you_sure)) return false;

    var $this = jQuery(this);

    if ($this.hasClass("updating-message")) return false;

    $this.addClass("updating-message");
    jQuery
      .ajax({
        cache: false,
        dataType: "json",
        contentType: "application/json",
        url: fbv_data.json_url + "/fb-wipe-old-data",
        method: "POST",
        headers: {
          "X-WP-Nonce": fbv_data.rest_nonce,
        },
      })
      .done(function (res) {
        $this.removeClass("updating-message");
        $message.success(res.data.mess);
      })
      .fail(function (res) {
        $this.removeClass("updating-message");
        $message.error(res.data.mess);
      });
  });
  //clear all data
  jQuery(".njt_fbv_clear_all_data").on("click", function () {
    if (!confirm(fbv_data.i18n.are_you_sure)) return false;

    var $this = jQuery(this);

    if ($this.hasClass("updating-message")) return false;

    $this.focusout().addClass("updating-message");
    jQuery
      .ajax({
        cache: false,
        dataType: "json",
        contentType: "application/json",
        url: fbv_data.json_url + "/fb-wipe-clear-all-data",
        method: "POST",
        headers: {
          "X-WP-Nonce": fbv_data.rest_nonce,
        },
      })
      .done(function (res) {
        $this.removeClass("updating-message");
        $message.success(res.data.mess);
      })
      .fail(function (res) {
        $this.removeClass("updating-message");
        $message.error(res.data.mess);
      });
  });
  //no thanks btn
  jQuery(".njt_fb_no_thanks_btn").on("click", function () {
    var $this = jQuery(this);
    $this.addClass("updating-message");
    jQuery
      .ajax({
        dataType: "json",
        contentType: "application/json",
        type: "post",
        cache: false,
        url: fbv_data.json_url + "/fb-no-thanks",
        headers: {
          "X-WP-Nonce": fbv_data.rest_nonce,
        },
        data: JSON.stringify({
          site: $this.data("site"),
        }),
        success: function (res) {
          $this.removeClass("updating-message");
          jQuery(".njt.notice.notice-warning." + $this.data("site")).hide();
        },
      })
      .fail(function (res) {
        $this.removeClass("updating-message");
        $message.error(i18n.please_try_again);
      });
  });
  jQuery(".njt-fb-import").on("click", function () {
    var $this = jQuery(this);
    $this.addClass("updating-message");
    jQuery
      .ajax({
        dataType: "json",
        contentType: "application/json",
        url: fbv_data.json_url + "/fb-import",
        method: "POST",
        cache: false,
        headers: {
          "X-WP-Nonce": fbv_data.rest_nonce,
        },
        data: JSON.stringify({
          site: $this.data("site"),
          count: $this.data("count"),
        }),
      })
      .done(function (res) {
        if (res.data.folders) {
          var folders = res.data.folders;
          var site = res.data.site;
          var count = res.data.count;
          import_site(folders, site, 0, { count: count }, function (res) {
            if (res.success) {
              $this.removeClass("updating-message");
              var html_notice =
                '<div class="njt-success-notice notice notice-success is-dismissible"><p>' +
                res.data.mess +
                '</p><button type="button" class="notice-dismiss" onClick="jQuery(\'.njt-success-notice\').remove()"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
              jQuery(html_notice).insertBefore("form#fbv-setting-form");
            }
          });
        } else {
          $this.removeClass("updating-message");
          $message.error(res.data.mess);
        }
      })
      .fail(function (res) {
        $this.removeClass("updating-message");
        $message.error(i18n.please_try_again);
      });
  });
  function import_site(folders, site, index, more_data_when_done, on_done) {
    if (typeof folders[index] != "undefined") {
      jQuery
        .ajax({
          dataType: "json",
          contentType: "application/json",
          url: fbv_data.json_url + "/fb-import-insert-folder",
          method: "POST",
          cache: false,
          headers: {
            "X-WP-Nonce": fbv_data.rest_nonce,
          },
          data: JSON.stringify({
            site: site,
            folders: folders[index],
          }),
        })
        .done(function (res) {
          import_site(folders, site, index + 1, more_data_when_done, on_done);
        });
    } else {
      jQuery
        .ajax({
          dataType: "json",
          contentType: "application/json",
          url: fbv_data.json_url + "/fb-import-after-inserting",
          method: "POST",
          cache: false,
          data: JSON.stringify({
            site: site,
            count: more_data_when_done.count,
          }),
          headers: {
            "X-WP-Nonce": fbv_data.rest_nonce,
          },
        })
        .done(function (res) {
          on_done(res);
        });
    }
  }

  //generate API key
  jQuery(".fbv_generate_api_key_now").on("click", function () {
    if (!confirm(fbv_data.i18n.are_you_sure)) return false;
    var $this = jQuery(this);
    $this.addClass("updating-message");
    jQuery
      .ajax({
        dataType: "json",
        contentType: "application/json",
        type: "post",
        cache: false,
        url: fbv_data.json_url + "/fbv-api",
        data: JSON.stringify({
          act: "generate-key",
        }),
        headers: {
          "X-WP-Nonce": fbv_data.rest_nonce,
        },
        success: function (res) {
          $this.removeClass("updating-message");
          if (res.success) {
            var key = res.data.key;
            jQuery("#fbv_rest_api_key").removeClass("hidden");
            jQuery("#fbv_rest_api_key").val(key);
          } else {
            $message.error(res.data.mess);
          }
        },
      })
      .fail(function (res) {
        $this.removeClass("updating-message");
        $message.error(i18n.please_try_again);
      });
  });
  //submit form
  jQuery("input.njt-submittable").on("change", function () {
    var $this = jQuery(this);
    var data = $this.closest("form").serializeArray();
    let slider = $this.closest(".njt-switch").find("span.slider");
    if (slider.length) {
      slider.addClass("njt_loading");
    }
    jQuery
      .post("options.php", data)
      .success(function (res) {
        if (slider.length) {
          slider.removeClass("njt_loading");
        }
        $message.success(i18n.set_setting_success);
      })
      .error(function (res) {
        if (slider.length) {
          slider.removeClass("njt_loading");
        }
        $message.error(i18n.please_try_again);
      });
  });
  //notice dismiss
  jQuery("#filebird-empty-folder-notice").on("click", function (event) {
    if (jQuery(event.target).hasClass("notice-dismiss")) {
      jQuery
        .ajax({
          dataType: "json",
          url: window.ajaxurl,
          type: "post",
          cache: false,
          data: {
            action: "fbv_first_folder_notice",
            nonce: window.fbv_data.nonce,
          },
        })
        .done(function (result) {})
        .fail(function (res) {});
    }
  });

  function convertArrayOfObjectsToCSV(args) {
    const data = args.data;
    if (!data || !data.length) return;

    const columnDelimiter = args.columnDelimiter || ",";
    const lineDelimiter = args.lineDelimiter || "\n";

    const keys = Object.keys(data[0]);

    let result = "";
    result += keys.join(columnDelimiter);
    result += lineDelimiter;

    data.forEach((item) => {
      ctr = 0;
      keys.forEach((key) => {
        if (ctr > 0) result += columnDelimiter;
        result += item[key];
        ctr++;
      });
      result += lineDelimiter;
    });

    return result;
  }

  function generateDownloadCSV(args) {
    let csv = convertArrayOfObjectsToCSV({ data: args.data });
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });

    if (!csv) return;
    const filename = args.filename || "export.csv";
    const link = document.createElement("a");
    link.setAttribute("href", URL.createObjectURL(blob));
    link.setAttribute("download", filename);
    link.click();
  }

  function getUserFromCSV(fileValue) {
    let fileUpload = new FormData();

    if (!fileValue.length) {
      return Promise.reject("There is no file!");
    }
    if (fileValue.length) {
      fileUpload.append("file", fileValue[0]);
      return jQuery
        .ajax({
          url: fbv_data.json_url + "/import-csv-detail",
          method: "POST",
          processData: false,
          cache: false,
          contentType: false,
          beforeSend: function () {
            // $this.addClass("updating-message");
          },
          data: fileUpload,
          headers: {
            "X-WP-Nonce": fbv_data.rest_nonce,
          },
        })
        .done((res) => {
          let users = {
            "": "",
            "-1": fbv_data.i18n.all_folders,
            0: fbv_data.i18n.common_folders,
          };
          if (Object.entries(res).length) {
            users = {
              ...users,
              ...res,
            };
          }

          jQuery("#njt-fb-csv-user-import").closest("p").removeClass("hidden");
          jQuery("#njt-fb-csv-user-import").html(
            Object.entries(users)
              .sort()
              .map(
                (user) =>
                  jQuery("<option></option>").val(user[0]).html(user[1])[0]
                    .outerHTML
              )
              .join("")
          );
        });
    }
  }

  jQuery(document).on("change", "#njt-fb-csv-user-import", function (event) {
    let description = "";
    const val = event.target.value;

    if (val) {
      if (val === "-1") description = fbv_data.i18n.all_folders_description;
      else if (val === "0")
        description = fbv_data.i18n.common_folders_description;
      else
        description = `${fbv_data.i18n.user_folders_description} ${jQuery(
          "option:selected",
          event.target
        ).text()}.`;
      jQuery(".njt-fb-csv-import").prop("disabled", false);
      jQuery("#njt-fb-csv-user-import-description").html(description);
    } else {
      jQuery(".njt-fb-csv-import").prop("disabled", true);
      jQuery("#njt-fb-csv-user-import-description").empty();
    }
  });

  jQuery(".njt-fb-csv-export").on("click", function () {
    const $this = jQuery(this);
    jQuery
      .ajax({
        dataType: "json",
        contentType: "application/json",
        type: "get",
        cache: false,
        url: fbv_data.json_url + "/export-csv",
        headers: {
          "X-WP-Nonce": fbv_data.rest_nonce,
        },
        beforeSend: function () {
          $this.addClass("updating-message");
        },
        success: function (res) {
          jQuery("#njt-fb-download-csv")
            .unbind("click")
            .bind("click", function () {
              generateDownloadCSV({
                filename: "filebird.csv",
                data: res.folders,
              });
            });

          $this.removeClass("updating-message");
          jQuery("#njt-fb-download-csv").show();
          $message.success(i18n.successfully_exported);
        },
      })
      .fail(function (res) {
        $this.removeClass("updating-message");
        $message.error(i18n.please_try_again);
      });
  });

  jQuery("#njt-fb-upload-csv").on("change", async function (event) {
    const fileValue = event.target.files;
    let fileUpload = new FormData();

    jQuery("#njt-fb-csv-user-import").val("");
    jQuery("#njt-fb-csv-user-import-description").empty();
    jQuery(".njt-fb-csv-import").prop("disabled", true);

    await getUserFromCSV(fileValue);

    if (fileValue.length) {
      fileUpload.append("file", fileValue[0]);
      jQuery(".njt-fb-csv-import").removeClass("hidden");
      jQuery(".njt-fb-csv-import")
        .unbind("click")
        .bind("click", function () {
          fileUpload.append("userId", jQuery("#njt-fb-csv-user-import").val());
          $this = jQuery(this);
          jQuery
            .ajax({
              url: fbv_data.json_url + "/import-csv",
              method: "POST",
              processData: false,
              cache: false,
              contentType: false,
              beforeSend: function () {
                $this.addClass("updating-message");
              },
              data: fileUpload,
              headers: {
                "X-WP-Nonce": fbv_data.rest_nonce,
              },
            })
            .done((res) => {
              if (res.success) {
                $this.removeClass("updating-message");
                $this.text(fbv_data.i18n.imported);
                $this.prop("disabled", true);
                $message.success(i18n.successfully_imported);
              } else {
                $this.removeClass("updating-message");

                if (res.message) {
                  $message.error(res.message);
                  return;
                }
                $message.error(i18n.please_try_again);
              }
            })
            .fail((error) => {
              console.log(error);
              $this.removeClass("updating-message");
              $message.error(i18n.please_try_again);
            });
        });
    }
  });

  jQuery(".fbv-tab-name").on("click", function () {
    var $this = jQuery(this);

    jQuery(".fbv-tab-name").removeClass("nav-tab-active");
    $this.addClass("nav-tab-active");

    jQuery(".fbv-tab-content").addClass("hidden");
    jQuery("#fbv-settings-tab-" + $this.attr("data-id")).removeClass("hidden");
    return false;
  });

  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const page = urlParams.get("page");
  const autorun = urlParams.get("autorun");
  if (page === "filebird-settings" && autorun == "true") {
    njt_auto_run_import = true;
    jQuery(".njt_fbv_import_from_old_now").click();
  }

  jQuery(".fbv-pro-feature").each(function (index, el) {
    jQuery("input", el).on("mousedown click", function (e) {
      e.preventDefault();
    });
    fbv_data.utils.tippy(el, {
      theme: "fbv-pro",
      content: i18n.active_to_use_feature,
      animation: "shift-away",
    });
  });

  jQuery(".njt_fbv_generate_attachment_size").on("click", function (e) {
    const processingStatus = jQuery(
      ".fbv-generate-attachment-size .processing-status"
    );
    const $this = this;
    const TIME_INTERVAL = 2000;
    $this.classList.add("updating-message");
    $this.disabled = true;
    const generating = setInterval(() => {
      jQuery
        .ajax({
          url: fbv_data.json_url + "/generate-attachment-size",
          method: "POST",
          cache: false,
          dataType: "json",
          contentType: "application/json",
          // data: JSON.stringify({
          //   generateAll,
          // }),
          headers: {
            "X-WP-Nonce": fbv_data.rest_nonce,
          },
          beforeSend: function () {},
        })
        .done((json) => {
          if (!json.isProcessing) {
            processingStatus.hide();
            $this.classList.remove("updating-message");
            $this.disabled = false;
            clearInterval(generating);
            return;
          }
          if (processingStatus.css("display") === "none") {
            processingStatus.show();
            console.log("display here");
          }
          processingStatus.text(
            `${fbv_data.i18n.processing} ${json.current}/${json.total}`
          );
        })
        .fail((json) => {
          $this.classList.remove("updating-message");
          $this.disabled = false;
        });
    }, TIME_INTERVAL);
  });

  jQuery(".njt_fbv_sync_wpml").on("click", function () {
    jQuery(this).addClass("updating-message");
    jQuery
      .ajax({
        url: window.ajaxurl,
        method: "POST",
        cache: false,
        data: {
          action: "fbv_sync_wpml",
          nonce: window.fbv_data.nonce,
        },
      })
      .then((res) => {
        jQuery(this).next().text(res.message);
      })
      .catch((err) => console.log(err))
      .always(() => {
        jQuery(this).removeClass("updating-message");
      });
  });
});
