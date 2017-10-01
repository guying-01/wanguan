// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
angular.module('hardware', ['ionic','hardware.controllers','hardware.directives','hardware.routes','hardware.services','hardware.pluginServices','hardware.factory','ngCordova'],function($httpProvider){
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
  var param = function(obj) {
    var query = '', name, value, fullSubName, subName, subValue, innerObj, i;
    for(name in obj) {
      value = obj[name];

      if(value instanceof Array) {
        for(i=0; i<value.length; ++i) {
          subValue = value[i];
          fullSubName = name + '[' + i + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value instanceof Object) {
        for(subName in value) {
          subValue = value[subName];
          fullSubName = name + '[' + subName + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value !== undefined && value !== null)
        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
    }

    return query.length ? query.substr(0, query.length - 1) : query;
  };

  // Override $http service's default transformRequest
  $httpProvider.defaults.transformRequest = [function(data) {
    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
  }]
})
  .config(function ($stateProvider, $urlRouterProvider, $ionicConfigProvider) {
    $ionicConfigProvider.platform.android.tabs.style('bottom');
    $ionicConfigProvider.platform.android.tabs.position('bottom');
  })
  .run(['$ionicPlatform','$rootScope','$cordovaToast','$ionicLoading','$cordovaDialogs','$state','$location','$ionicHistory',function($ionicPlatform,$rootScope,$cordovaToast,$ionicLoading,$cordovaDialogs,$state,$location,$ionicHistory) {

    $rootScope.setRandomColor=function (index) {
      var num=index%3;
      if(num==0){
        return 'list-g list-blue';
      }
      else if(num==1){
        return 'list-g list-green';
      }
      else if(num==2){
        return 'list-g list-yellow';
      }
    }
    $rootScope.exit=function () {
       $cordovaDialogs.confirm('确定退出吗？','提示',['确认','取消'])
         .then(function(buttonIndex){
           if(buttonIndex=='1'){
             $state.go('login')
           }
         })
        }
    $ionicPlatform.ready(function() {
      if(window.cordova && window.cordova.plugins.Keyboard) {
        cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);

        cordova.plugins.Keyboard.disableScroll(true);
      }
      if(window.StatusBar) {
        StatusBar.styleDefault();
      }
      $ionicPlatform.registerBackButtonAction(function (e) {
        //判断处于哪个页面时双击退出
        if ($location.path() == '/login' || $location.path() == '/tabs/orderList' ) {
          if ($rootScope.backButtonPressedOnceToExit) {
            ionic.Platform.exitApp();
          } else {
            $rootScope.backButtonPressedOnceToExit = true;
            $cordovaToast.showShortBottom('再按一次退出系统');
            setTimeout(function () {
              $rootScope.backButtonPressedOnceToExit = false;
            }, 2000);
          }
        }
        else if ($ionicHistory.backView()) {
          $ionicHistory.goBack();
        } else {
          $rootScope.backButtonPressedOnceToExit = true;
          $cordovaToast.showShortTop('再按一次退出系统')
            .then(function(success) {
              // success
            }, function (error) {
              // error
            });
          setTimeout(function () {
            $rootScope.backButtonPressedOnceToExit = false;
          }, 2000);
        }
        e.preventDefault();
        return false;
      }, 101);
    });
}])
