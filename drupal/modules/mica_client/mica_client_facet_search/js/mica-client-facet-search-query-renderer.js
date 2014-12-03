(function ($) {

  "use strict";

  var container;
  var contentQuery;
  var contentRefresh;
  var jsonQuery;
  var translation;
  var termsDictionary;

  var AND_OPERATOR = 'and';
  var OR_OPERATOR = 'or';
  var MAX_VIISBLE_AGG_VALUE = 3;

  /**
   * Constructor
   * @constructor
   */
  $.QueryViewRenderer = function (translationMap, dictionary) {
    container = $("<div class='row'>");
    contentRefresh = $("<ul class='facet-query-list'></ul>");
    contentQuery = $("<ul class='facet-query-list'></ul>");
    container.append($("<div class='col-xs-1'>").append(contentRefresh)) //
      .append($("<div class='col-xs-11 col-pull-left-30'>").append(contentQuery));
    translation = translationMap;
    termsDictionary = dictionary;
  };

  /**
   * renders a query view based on selected aggregations
   * @type {{render: render}}
   */
  $.QueryViewRenderer.prototype = {
    render: function (json) {
      jsonQuery = json;
      return parseAndRender();
    }
  };


  /**
   * Updates the query and url
   */
  function update() {
    $.query_href.updateJsonQuery(jsonQuery);
  }

  function renderRefresh() {
    return $("<li><button type='button' class='refresh-button'><i class='flaticon-undo9'></i></button></li>")
      .on("click", function () {
        $.query_href.updateWindowLocation(null);
      });
  }

  function renderMatches(type, value) {
    var matches = renderMatchesElement('matches', type, translate(type));
    var matchesValue = renderValuesContainer().append(renderMatchesElement('matches-value', type, value));
    contentQuery.append(matches.append(renderMatch()).append(matchesValue));
  }

  function renderMatchesElement(cssClass, type, text) {
    var htmlMatchesElement = $("<li></li>").append($("<span class='" + cssClass + "'></span>").text(text));
    htmlMatchesElement.click(function (e) {
      delete jsonQuery[type]['matches'];
      update();
      e.stopPropagation();
    });

    return htmlMatchesElement;
  }

  function renderMatch() {
    return $("<span class='match'>" + translate('match') + "</span>");
  }


  function renderValuesContainer() {
    return $("<ul class='facet-query-list'></ul>");
  }

  function leftParenthesis() {
    return $("<span class='grouping-symbol'>(</span>");
  }

  function rightParenthesis() {
    return $("<span class='grouping-symbol'>)</span>");
  }

  function leftBracket() {
    return $("<span class='grouping-symbol'>[</span>");
  }

  function rightBracket() {
    return $("<span class='grouping-symbol'>]</span>");
  }

  function renderComma() {
    return $("<span class='comma'>,</span>");
  }

  function createTermsMoniker(type, name, value) {
    return type + ":" + name + ":" + value;
  }

  function createOpMoniker(type, aggType, name) {
    return type + ":" + aggType + ":" + name;
  }

  function renderIn() {
    return $("<span class='operation'>in</span>");
  }

  function renderAggregationContainer(type, typeValues, aggType, name, op, showOp) {
    var aggContainer = renderAggregate(type, typeValues, aggType, name);
    var aggValueContainer = renderValuesContainer();
    var left = aggType === 'terms' ? leftParenthesis() : leftBracket();
    var right = aggType === 'terms' ? rightParenthesis() : rightBracket();

    aggContainer
      .append(renderIn())
      .append(left).append(aggValueContainer).append(right);

    if (!showOp) aggContainer.append(renderAndOrOperation(op, createOpMoniker(type, aggType, name)));

    contentQuery.append(aggContainer);

    return aggValueContainer;
  }

  function renderOrOperation(show, moniker) {
    return $("<span data-op='or' id='or-" + moniker + "' " + (show ? "" : "hidden") + "  class='or-operation'>" + translate('or').toUpperCase() + "</span>");
  }

  function renderAndOperation(show, moniker) {
    return $("<span data-op='and' id='and-" + moniker + "' " + (show ? "" : "hidden") + "  class='and-operation'>" + translate('and').toUpperCase() + "</span>");
  }

  function renderAndOrOperation(operator, moniker) {

    var and = renderAndOperation(operator == AND_OPERATOR);
    var or = renderOrOperation(operator == OR_OPERATOR, moniker);
    return $("<span class='clickable'></span>").append(and).append(or).click(function (e) {
      $('[id^=and-]', this).toggle();
      $('[id^=or-]', this).toggle();
      $.query_href.updateQueryOperation(moniker, toggleOperation(operator));
      e.stopPropagation();
    });
  }

  function toggleOperation(op) {
    return OR_OPERATOR === op ? AND_OPERATOR : OR_OPERATOR;
  }

  function renderAggregate(type, typeValues, aggType, name) {
    var aggregate = $("<span class='aggregate'></span>").text(translateAggregation(name)).click(function (e) {
      delete jsonQuery[type][aggType][name];
      update();
      e.stopPropagation();
    });

    var htmlAggregate = $("<li></li>").append(aggregate);

    return htmlAggregate;
  }

  function renderTermsAggregationValue(value) {
    return $("<span class='aggregate-value'></span>").text(value);
  }

  function renderRangeAggregationValue(value) {
    return $("<span class='aggregate-value'></span>").text(value.min + " - " + value.max);
  }

  function renderAggregateValue(aggType, agg, i, value, valuesContaier, hiddenValuesContainer) {
    if (i > 0 && i < MAX_VIISBLE_AGG_VALUE) valuesContaier.append(renderComma());

    var htmlValue =
      aggType === "terms" //
        ? renderTermsAggregationValue(value) //
        : renderRangeAggregationValue(value); //

    htmlValue.click(function (e) {
      agg.values.splice(i, 1);
      if (agg.values.length === 0) delete agg['op'];
      update();
      e.stopPropagation();
    });

    valuesContaier.append(hiddenValuesContainer === null ? htmlValue : hiddenValuesContainer.append(htmlValue));
  }

  function getOperation(op) {
    if ('and' === op || 'or' === op) return op;
    return AND_OPERATOR;
  }

  function renderPlusMinus(parent) {
    var hiddens = $("<span hidden class='no-text-decoration' id='hidden-values'></span>");
    var plusMinus = $("<span class='plus-minus glyphicon glyphicon-plus'></span>").append(hiddens)
      .click(function (e) {
        if ($(this).hasClass('glyphicon-plus')) {
          $(this).removeClass('glyphicon-plus').addClass('glyphicon-minus');
        } else {
          $(this).removeClass('glyphicon-minus').addClass('glyphicon-plus');
        }

        $('[id^=hidden-values]').toggle();
        e.stopPropagation();
      });

    parent.append(plusMinus.append(hiddens));
    return hiddens;
  }

  function getHiddenAttribute(hide) {
    return hide ? "hidden" : "";
  }

  function translate(key) {
    return translation.general[key];
  }

  function translateAggregation(key) {
    var result = key;
    $.each(translation, function (i, v) {
      $.each(v, function (j, o) {
        if (o.aggs === key) {
          result = o.title.replace(/[^-]+-/, '');
          return false;
        }
      });
    });

    return result;
  }

  function parseAndRender() {
    contentRefresh.append(renderRefresh());
    var prevType = null;

    $.each(jsonQuery, function (type, typeValues) {
      var i = 0;
      var total = 0;
      $.each(typeValues, function (aggType, aggs) {
        if (aggType !== 'matches') {
          total += Object.keys(typeValues[aggType]).length;
        }
      })

      $.each(typeValues, function (aggType, aggs) {

        if (aggType === 'matches') {
          var opAdded = false;
          if (contentQuery.children().length > 0) {
            contentQuery.append(renderAndOperation(true, ""));
            opAdded = true;
          }
          renderMatches(type, aggs.replace(/\+/g, ' '));
          if (!opAdded && total > 0) {
            contentQuery.append(renderAndOperation(true, ""));
          }

          return;
        }

        var last = total - 1;

        if (prevType != null && prevType != type) {
          // separate queries by type
          contentQuery.append($("<li><br/></li>"));
        }

        $.each(aggs, function (name, agg) {
          if (!$.isEmptyObject(agg.values) && agg.values.length > 0) {
            var aggValueContainer = renderAggregationContainer(type, typeValues, aggType, name, getOperation(agg.op), i === last);
            var valuesContainer = $("<li></li>");
            var hiddenValuesContainer = null;
            $(aggValueContainer).append(valuesContainer);


            $.each(agg.values, function (i, value) {
              var caption = termsDictionary[createTermsMoniker(type, name, value)] || value;
              if (i >= MAX_VIISBLE_AGG_VALUE && hiddenValuesContainer === null) {
                hiddenValuesContainer = renderPlusMinus(valuesContainer);
                valuesContainer.append(hiddenValuesContainer);
              }

              renderAggregateValue(aggType, //
                agg, //
                i, //
                $.type(caption) ==="string" ? caption.replace(/\+/g, ' ') : caption, //
                valuesContainer, //
                hiddenValuesContainer); //
            });
          }

          i++;
        });
        // save for line break
        prevType = type;
      });


    });

    return container;
  }

}(jQuery));

/**
 * client-server bridge
 */
(function ($) {

  Drupal.behaviors.query_builder = {
    attach: function (context, settings) {
      var jsonQuery = $.query_href.getQueryFromUrl();
      if ($.isEmptyObject(jsonQuery)) return;

      var view = //
        new $.QueryViewRenderer(Drupal.settings.mica_client_facet.facet_conf, Drupal.settings.terms_dictionary) //
          .render(jsonQuery);

      $('#search-query').append(view);
    }
  }
})(jQuery);

