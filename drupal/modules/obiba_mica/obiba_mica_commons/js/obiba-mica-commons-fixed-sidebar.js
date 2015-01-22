/**
 * @file
 * JsScript to deal with FixSidebar
 */

(function ($) {
  Drupal.behaviors.obiba_mica_commons_fixed_sidebar = {
    attach: function (context, settings) {
      var template = Drupal.settings.template;
      var sidebar = $('#fixed-sidebar');
      var content = $("div.main-container > div.row > .page-header ~ section");
      hide();
      updatePosition();

      $.ajax({
        'async': false,
        'url': Drupal.settings.basePath + 'mica/get_fixed_sidebar/' + template,
        'type': 'GET',
        'success': function (data) {
          // console.log("DATA", JSON.stringify(data));
          show();
          $(".side-bar-content").html(data['fixed_menu']);
        },
        "dataType": "json",
        'error': function (data) {
        }
      });

      function hide() {
        sidebar && $(sidebar).css('z-index', '-1000');
      }

      function show() {
        sidebar && $(sidebar).css('z-index', '1000');
      }

      function updatePosition() {
        if (!content || !sidebar) return;

        if (content.hasClass('col-sm-12')) {
            $(content).toggleClass('col-sm-12 col-sm-11 col-xs-12') && $(sidebar).addClass("pull-right col-sm-1 hidden-xs");
        } else if (content.hasClass('col-sm-9')) {
            $(content).toggleClass('col-sm-9 col-sm-8 col-xs-9') && $(sidebar).addClass("pull-right col-sm-1 hidden-xs");
        }
      }
    }

  }

}(jQuery));