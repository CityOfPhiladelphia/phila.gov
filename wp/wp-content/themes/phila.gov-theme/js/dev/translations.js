module.exports = $(function () {
  //BEGIN Translation Bar

  $(document).ready(function () {
    $("#google_translate_element").bind("DOMNodeInserted", function () {
      $(".goog-te-gadget .goog-te-gadget-simple span:first").replaceWith(
        function () {
          return "<a id='gte' role='menuitem'>More languages</>";
        }
      );
    });
    // "hard code" english translations label DD
    $("#translate-english").text("English");
    $("#translate-english-dropdown").text("English");

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
      },
      "hide.zf.dropdown": function (e) {
        setOverflowAuto();
        toggleMenuOpen(false);
        updateBodyState();
      },
    });

    var hoverTimeout;

    $("button#desktop-lang-button").hover(function () {
      clearTimeout(hoverTimeout);
      if (!$("#lang-dropdown").hasClass("is-open")) {
        hoverTimeout = setTimeout(function () {
          $("#lang-dropdown").foundation("open");
        }, 500);
      }
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
      $("#translations-menu> li")
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
      var urlPath = windowPath.split("/");
      var pathItem = urlPath[1];
      var urlLanguage = "";

      switch (pathItem) {
        case "zh":
          urlLanguage = "中文";
          break;
        case "es":
          urlLanguage = "Español";
          break;
        default:
          urlLanguage = "English";
      }
      setActiveLanguage(urlLanguage);
    }
    getUrlLanguage();
  });

  //END Translation Bar
});
