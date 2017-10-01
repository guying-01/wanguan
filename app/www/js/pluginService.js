/**
 * Created by gy on 2017/8/9 19:40.
 */
angular.module('hardware.pluginServices',['ngCordova'])
       .service('pluginService',['$cordovaToast','$ionicLoading','$cordovaDialogs','$state',function ($cordovaToast,$ionicLoading,$cordovaDialogs,$state) {
           return {
             showToast:function (msg) {
               $cordovaToast.showShortBottom(msg);
             },
             loadingOpen:function () {
               $ionicLoading.show({
                 template:' <ion-spinner icon="ripple" class="spinner-calm"></ion-spinner><p>加载中...</p>'
               })
             },
             loadingClose:function () {
               $ionicLoading.hide();
             },
             exit:function () {
               $cordovaDialogs.confirm('确定退出吗？','提示',['确认','取消'])
                 .then(function(buttonIndex){
                   if(buttonIndex=='1'){
                     $state.go('login')
                   }
                 })
             }
           }
       }])
