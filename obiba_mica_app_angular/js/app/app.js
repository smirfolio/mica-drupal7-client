/*
 * Copyright (c) 2016 OBiBa. All rights reserved.
 *
 * This program and the accompanying materials
 * are made available under the terms of the GNU Public License v3.0.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @file
 * JavaScript ajax helper for Statistics variables retrieving
 */

'use strict';

var modules = [
  'ngObiba',
  'ngRoute',
  'ngSanitize',
  'ngResource',
  'ui.bootstrap',
  'obiba.form',
  'obiba.graphics',
  'obiba.comments',
  'angularUtils.directives.dirPagination',
  'pascalprecht.translate',
  'ngObibaMica'
];
var sanitizeModules = function (origArr) {
  if (!Array.isArray(origArr)) {
    var res = [];
    for (var i in origArr) {
      res.push(origArr[i]);
    }
    origArr = res;
  }

  var newArr = [],
    origLen = origArr.length,
    found, x, y;

  for (x = 0; x < origLen; x ++) {
    found = undefined;
    for (y = 0; y < newArr.length; y ++) {
      if (origArr[x] === newArr[y]) {
        found = true;
        break;
      }
    }
    if (! found && origArr[x] !== false) {
      newArr.push(origArr[x]);
    }
  }
  return newArr;
};
var drupalModules = sanitizeModules(Drupal.settings.angularjsApp.modules);

/* App Module */
if (drupalModules) {
  modules = modules.concat(drupalModules);
}
var mica = angular.module('mica', modules);

/**
 * Data Access Request related provider configuration
 */
mica.config(['ngObibaMicaSearchProvider', 'ngObibaMicaUrlProvider',
  function (ngObibaMicaSearchProvider, ngObibaMicaUrlProvider) {
    var basePathAndPathPrefix = Drupal.settings.basePath + Drupal.settings.pathPrefix;

    ngObibaMicaUrlProvider.setUrl('DataAccessClientDetailPath', 'mica/data_access/request');
    ngObibaMicaUrlProvider.setUrl('DataAccessClientListPath',  'mica/data_access/requests');
    ngObibaMicaUrlProvider.setUrl('DataAccessFormConfigResource', basePathAndPathPrefix + 'mica/data_access/data-access-form/ws');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestsExportCsvResource', Drupal.settings.basePath + 'mica/data_access/requests/csv/ws' + ((Drupal.settings.basePath.indexOf('?q=')!==-1)?'&':'?') + 'lang=:lang');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestsResource', basePathAndPathPrefix + 'mica/data_access/requests/ws');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestResource', basePathAndPathPrefix + 'mica/data_access/request/:id/ws');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestAttachmentsUpdateResource', basePathAndPathPrefix + 'mica/data_access/request/:id/_attachments/ws');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestAttachmentDownloadResource', basePathAndPathPrefix + 'mica/data_access/request/:id/attachments/:attachmentId/_download/ws');
    ngObibaMicaUrlProvider.setUrl('SchemaFormAttachmentDownloadResource', basePathAndPathPrefix + 'mica/data_access/request/form/attachments/:attachmentName/:attachmentId/_download/ws' + ((Drupal.settings.basePath.indexOf('?q=')!==-1)?'&':'?') + 'path=:path');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestDownloadPdfResource', basePathAndPathPrefix + 'mica/data_access/request/:id/_pdf/ws');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestCommentsResource', basePathAndPathPrefix + 'mica/data_access/request/:id/comments/ws');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestCommentResource', basePathAndPathPrefix + 'mica/data_access/request/:id/comment/:commentId/ws');
    ngObibaMicaUrlProvider.setUrl('DataAccessRequestStatusResource', basePathAndPathPrefix + 'mica/data_access/request/:id/_status/:status/ws');
    ngObibaMicaUrlProvider.setUrl('TempFileUploadResource', basePathAndPathPrefix + 'mica/data_access/request/upload-file');
    ngObibaMicaUrlProvider.setUrl('TempFileResource', basePathAndPathPrefix + 'mica/data_access/request/file/:id');
    ngObibaMicaUrlProvider.setUrl('TaxonomiesSearchResource', basePathAndPathPrefix + 'mica/repository/taxonomies/_search/ws');
    ngObibaMicaUrlProvider.setUrl('TaxonomiesResource', basePathAndPathPrefix + 'mica/repository/taxonomies/_filter/ws');
    ngObibaMicaUrlProvider.setUrl('TaxonomyResource', basePathAndPathPrefix + 'mica/repository/taxonomy/:taxonomy/_filter/ws');
    ngObibaMicaUrlProvider.setUrl('VocabularyResource', basePathAndPathPrefix + 'mica/repository/taxonomy/:taxonomy/vocabulary/:vocabulary/_filter/ws');
    ngObibaMicaUrlProvider.setUrl('JoinQuerySearchResource', basePathAndPathPrefix + 'mica/repository/:type/_rql/ws');
    ngObibaMicaUrlProvider.setUrl('JoinQuerySearchCsvResource', basePathAndPathPrefix + 'mica/repository/:type/_rql_csv/:query/ws');
    ngObibaMicaUrlProvider.setUrl('JoinQuerySearchCsvReportResource', basePathAndPathPrefix + 'mica/repository/:type/_report/:query/ws');
    ngObibaMicaUrlProvider.setUrl('JoinQuerySearchCsvReportByNetworkResource', basePathAndPathPrefix +  'mica/repository/:type/_report_by_network/:networkId/:locale/ws');
    ngObibaMicaUrlProvider.setUrl('JoinQueryCoverageResource', basePathAndPathPrefix + 'mica/repository/variables/_coverage/ws');
    ngObibaMicaUrlProvider.setUrl('JoinQueryCoverageDownloadResource',  basePathAndPathPrefix + 'mica/repository/variables/_coverage_download/:query/ws');
    ngObibaMicaUrlProvider.setUrl('VariablePage', basePathAndPathPrefix + 'mica/variable/:variable');
    ngObibaMicaUrlProvider.setUrl('NetworkPage', basePathAndPathPrefix + 'mica/network/:network');
    ngObibaMicaUrlProvider.setUrl('StudyPage', basePathAndPathPrefix + 'mica/:type/:study');
    ngObibaMicaUrlProvider.setUrl('StudyPopulationsPage', basePathAndPathPrefix + 'mica/:type/:study/#population-:population');
    ngObibaMicaUrlProvider.setUrl('DatasetPage', basePathAndPathPrefix + 'mica/:type/:dataset');
    ngObibaMicaUrlProvider.setUrl('BaseUrl', basePathAndPathPrefix);
    ngObibaMicaUrlProvider.setUrl('FileBrowserFileResource', basePathAndPathPrefix + 'mica/file');
    ngObibaMicaUrlProvider.setUrl('FileBrowserSearchResource', basePathAndPathPrefix + 'mica/files/search');
    ngObibaMicaUrlProvider.setUrl('FileBrowserDownloadUrl', basePathAndPathPrefix + 'mica/file/download' + ((Drupal.settings.basePath.indexOf('?q=')!==-1)?'&':'?') + 'path=:path&inline=:inline&keyToken=:key');
    ngObibaMicaUrlProvider.setUrl('SearchBaseUrl', 'mica/repository#/search');
    ngObibaMicaUrlProvider.setUrl('DocumentSuggestion', basePathAndPathPrefix + 'mica/repository/:documentType/_suggest/ws')
  }]);

mica.provider('SessionProxy',
  function () {
    function Proxy(user) {
      var roles = Object.keys(user.roles).map(function (key) {
        return user.roles[key];
      });
      var real = {login: user.name, roles: roles, profile: null};

      this.login = function () {
        return real.login;
      };

      this.roles = function () {
        return real.roles;
      };

      this.profile = function () {
        return real.profile;
      };
    }

    this.$get = function () {
      return new Proxy(Drupal.settings.angularjsApp.user);
    };
  });

mica.run(['amMoment', function(amMoment){
  amMoment.changeLocale(Drupal.settings.angularjsApp.locale);
}]);

mica.controller('MainController', [
  function () {
  }]);

mica.factory('TranslationService', ['$resource',
  function ($resource) {
    return $resource(Drupal.settings.basePath + 'obiba_mica_app_angular/translation', {}, {
      'get': {method: 'GET'}
    });
  }]);

mica.config(['$routeProvider', '$translateProvider',
  function ($routeProvider, $translateProvider) {
    $routeProvider
      .when('/', {
        controller: 'MainController'
      });
    $translateProvider.preferredLanguage(Drupal.settings.angularjsApp.locale)
      .useLoader('DrupalTranslationLoader', {lang: Drupal.settings.angularjsApp.locale})
      .fallbackLanguage('en')
      .useSanitizeValueStrategy('escaped');
  }]);

mica.factory('DrupalTranslationLoader',
  function ($http, $q) {
    return function (options) {
      var deferred = $q.defer();

      $http({
        method: 'GET',
        url: Drupal.settings.basePath + 'obiba_mica_app_angular/translation/' + options.lang
      }).success(function (data) {
        deferred.resolve(data);
      }).error(function () {
        deferred.reject(options.key);
      });

      return deferred.promise;
    }
  });

mica.factory('ErrorTemplate', function () {
  return {
    getServerError: function (response) {
      if (angular.isObject(response.data)) {
        if (! response.data.messageTemplate) {
          response.data.messageTemplate = 'server.error.' + response.status;
        }
      } else {
        response.data = {messageTemplate: 'server.error.' + response.status};
      }
      return response;
    }
  }

});

mica.factory('ForbiddenDrupalRedirect', function () {

  var createDestinationPath = function (path) {
    if (angular.isDefined(path)) {
      var regExp = new RegExp('(view|edit)\/(.*)$');
      var results = regExp.exec(path);
      if (results && results.length > 1) {
        return '?destination=mica/data_access/request/redirect/' + results[1] + '/' + results[2];
      }

      return '';
    }
  };

  return {
    redirectDrupalMessage: function (response) {
      if (response.status && response.status == 403 && ! Drupal.settings.angularjsApp.authenticated) {
        $.post('un-authorized-error');
        $(window).delay(200).queue(function () {
          window.location = Drupal.settings.basePath + 'user/login' + createDestinationPath(window.location.hash);
        });
      }
    }
  }

});

/**
 * A N G U L A R     G L O B A L     S E R V I C E S
 */

mica.service('ServerErrorAlertService', ['AlertService', 'ServerErrorUtils', 'ErrorTemplate',
  function (AlertService, ServerErrorUtils, ErrorTemplate) {
    this.alert = function (id, response) {
      if (angular.isDefined(response.data)) {
        var errorDto = JSON.parse(response.data);
        if (angular.isDefined(errorDto) && angular.isDefined(errorDto.messageTemplate)) {
          AlertService.alert({
            id: id,
            type: 'danger',
            msgKey: errorDto.messageTemplate,
            msgArgs: errorDto.arguments
          });
          return;
        }
      }

      AlertService.alert({
        id: id,
        type: 'danger',
        msg: ServerErrorUtils.buildMessage(ErrorTemplate.getServerError(response))
      });
    };

    return this;
  }]);

mica.service('AttributeService',
  function () {
    return {
      getAttributes: function (container, names) {
        if (! container && ! container.attributes && ! names) {
          return null;
        }
        return container.attributes.filter(
          function (attribute) {
            return names.indexOf(attribute.name) !== - 1;
          });
      },

      getValue: function (attribute) {
        if (! attribute) {
          return null;
        }
        var value = attribute.values.filter(
          function (value) {
            return value.lang === Drupal.settings.angularjsApp.locale || value.lang === 'und';
          });

        return value.length > 0 ? value[0].value : null;
      }
    }
  });

mica.service('LocalizedStringService',
  function () {
    return {
      getValue: function (localized) {
        if (! localized) {
          return null;
        }
        var value = localized.filter(
          function (locale) {
            return locale.lang === Drupal.settings.angularjsApp.locale || locale.lang === 'und';
          });

        return value.length > 0 ? value[0].value : null;
      },
      getLocal: function () {
        return Drupal.settings.angularjsApp.locale;
      }
    }
  })
  .service('GraphicChartsConfigurations', ['GraphicChartsConfig', function (GraphicChartsConfig) {
    this.setClientConfig = function () {
      GraphicChartsConfig.setOptions(Drupal.settings.GraphicChartsOptions);
    };
    this.getClientConfig = function () {
      return Drupal.settings.GraphicChartsOptions;
    };
  }]);
