module.exports = $(function () {
  //BEGIN Translation Bar

  const targetNode = document.getElementById("google_translate_element");

if (targetNode) {
  const observer = new MutationObserver(function (mutationsList) {
    for (const mutation of mutationsList) {
      if (mutation.type === "childList" && $("#google_translate_element").length) {
        const $firstOption = $(".goog-te-combo option:first");

        if ($firstOption.length && $firstOption.text() === "Select Language") {
          $firstOption.text("More Languages");
        }

        $("i.fa-plus").on("click", function () {
          $("select.goog-te-combo").trigger("click");
        });

        $(".goog-te-gadget")
          .contents()
          .filter(function () {
            return (
              this.nodeType === 3 && !$(this).parent().hasClass("goog-te-combo")
            );
          })
          .remove();

        if ($(".goog-te-gadget span:first").length) {
          $(".goog-te-gadget span:first").remove();
        }

        const targetDiv = $("#\\:0\\.targetLanguage");
        if (!targetDiv.has(".fa.fa-plus").length) {
          const icon = $("<i>").addClass("fa fa-plus");
          targetDiv.prepend(icon);
        }

        const gadgetDiv = $(".goog-te-gadget");
        const select = $("select.goog-te-combo");
        select.on("focus", function () {
          gadgetDiv.focus();
        });

        // Optionally, stop observing after one mutation
        // observer.disconnect();
      }
    }
  });

  observer.observe(targetNode, { childList: true, subtree: true });
}

    // "hard code" english translations label DD
    $("#translate-english").text("English");
    $("#translate-english-dropdown").text("English");
    $("#translate-spanish").text("Español");
    $("#translate-spanish-dropdown").text("Español");
    $("#translate-chinese").text("中文");
    $("#translate-chinese-dropdown").text("中文");
    $("#translate-arabic-dropdown").text("عربي");
    $("#translate-haitian-creole-dropdown").text("Ayisyen");
    $("#translate-french-dropdown").text("Français");
    $("#translate-swahili-dropdown").text("Kiswahili");
    $("#translate-portuguese-dropdown").text("Português");
    $("#translate-russian-dropdown").text("русский");
    $("#translate-vietnamese-dropdown").text("Tiếng Việt");


    function setOverflowHidden() {
      $("html, body").css({
        overflow: "hidden",
        height: "100%",
      });
    }

    function setOverflowAuto() {
      $("html, body").css({
        overflow: "auto",
        height: "auto",
      });
    }

    function updateBodyState() {
      if ($(window).width() <= 779) {
        if ($("#lang-dropdown").hasClass("is-open") === true) {
          setOverflowHidden();
        }
      } else {
        setOverflowAuto();
      }
    }

    $(window).resize(function () {
      updateBodyState();
    });

    $("#lang-dropdown").on({
      click: function (e) {
        updateBodyState();
      },
      "show.zf.dropdown": function (e) {
        // setOverflowHidden();
        toggleMenuOpen(true);
        updateBodyState();
        $("#mobile-lang-button").focus();
        $("#desktop-lang-button").focus();
      },
      "hide.zf.dropdown": function (e) {
        setOverflowAuto();
        toggleMenuOpen(false);
        updateBodyState();
        $("#mobile-lang-button").focus();
        $("#desktop-lang-button").focus();
      },
    });

    function toggleMenuOpen(isOpen) {
      var $langDropdown = $("#lang-dropdown");
      var $caretIcon = $(".translations-nav i.translate-caret");
      var action = isOpen ? "addClass" : "removeClass";
      $langDropdown.toggleClass("js-dropdown-active", isOpen);
      $langDropdown.toggleClass("is-open", isOpen);
      $caretIcon[action]("rotated");
    }

    function setActiveLanguage(urlLanguage) {
      $(".translations-nav li")
        .find("a")
        .each(function () {
          if (urlLanguage === $.trim($(this).text())) {
            $(this).addClass("active");
          } else {
            $(this).removeClass("active");
          }
        });
    }
    function getUrlLanguage() {
      var windowPath = $(location)[0]; 
      if (windowPath) {
        var langPath = windowPath.pathname.split("/")[1];
        var urlLanguage = "";

        switch (langPath) {
          case "ar":
            urlLanguage = "عربي";
            break;
          case "zh":
            urlLanguage = "中文";
            break;
          case "es":
            urlLanguage = "Español";
            break;
          case "ht":
            urlLanguage = "Ayisyen";
            break;
          case "fr":
            urlLanguage = "Français";
            break;
          case "sw":
            urlLanguage = "Kiswahili";
            break;
          case "pt":
            urlLanguage = "Português";
            break;
          case "ru":
            urlLanguage = "русский";
            break;
          case "vi":
            urlLanguage = "Tiếng Việt";
            break;
          default:
            urlLanguage = "English";
        }
        setActiveLanguage(urlLanguage);
      }
    }
    getUrlLanguage();
  });

  //END Translation Bar

