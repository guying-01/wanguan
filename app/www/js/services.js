/**
 * Created by gy on 2017/8/8 11:45.
 */
angular.module('hardware.services',[])
       .service('IonicService',['$http','$q','ConfigService','$state',function ($http,$q,ConfigService,$state) {
            return{
               login:function (data) {
                 var url=ConfigService.getPostUrl()+"login/mobile_login";
                  var defer=$q.defer();
                  $http({
                    url:url,
                    method:'post',
                    data:data
                  }).success(function (data,status,header,config) {
                    defer.resolve(data);
                  }).error(function(e){
                   defer.resolve('error')
                 });
                 return defer.promise;
               },
               getOrderList:function (data) {
                 var url=ConfigService.getPostUrl()+"mobile/getSalesList";
                 var defer=$q.defer();
                 $http({
                   url:url,
                   data:data,
                   method:'post'
                 }).success(function (data,status,header,config) {
                   defer.resolve(data)
                 }).error(function(e){
                   defer.resolve('error')
                 })
                 return defer.promise;
               },
              getGoodsCategory:function (data) {
                var url=ConfigService.getPostUrl()+"mobile/getCategory";
                var defer=$q.defer();
                $http({
                  url:url,
                  data:data,
                  method:'post'
                }).success(function (data,status,header,config) {
                  defer.resolve(data)
                }).error(function(e){
                   defer.resolve('error')
                });
                return defer.promise;
              },
              getGoodsList:function (data) {
                var url=ConfigService.getPostUrl()+"mobile/getGoodsListByCategory";
                var defer=$q.defer();
                $http({
                  url:url,
                  data:data,
                  method:'post'
                }).success(function (data,status,header,config) {
                  defer.resolve(data)
                }).error(function(e){
                   defer.resolve('error')
                });
                return defer.promise;
              },
              goToPay:function (data) {
                var url=ConfigService.getPostUrl()+"/card.php?action=pay";
                var defer=$q.defer();
                $http({
                  url:url,
                  data:data,
                  method:'post'
                }).success(function (data,status,header,config) {
                  defer.resolve(data)
                }).error(function(e){
                   defer.resolve('error')
                });
                return defer.promise;
              },
              search:function (data) {
                var url=ConfigService.getPostUrl()+"mobile/searchGoods";
                var defer=$q.defer();
                $http({
                  url:url,
                  data:data,
                  method:'post'
                }).success(function (data,status,header,config) {
                  defer.resolve(data)
                }).error(function(e){
                   defer.resolve('error')
                });
                return defer.promise;
              },
              checkPayStatus:function (data) {
                var url=ConfigService.getPostUrl()+"/card.php?action=payok";
                var defer=$q.defer();
                $http({
                  url:url,
                  data:data,
                  method:'post'
                }).success(function (data,status,header,config) {
                  defer.resolve(data)
                }).error(function(e){
                   defer.resolve('error')
                });
                return defer.promise;
              },
              getInvoiceInfo:function (data){
              	var url=ConfigService.getPostUrl()+"mobile/getInvoiceInfo";
              	var defer = $q.defer();
              	$http({
              		url:url,
              		data:data,
              		method:'post'
              	}).success(function (data,status,header,config){
              		defer.resolve(data)
              	}).error(function(e){
                   defer.resolve('error')
                });
              	return defer.promise;
              }

            }
       }])
      .service('ConfigService',[function () {
          var userInformation={};
          var postUrl='http://116.62.198.226/index.php/';
//        var postUrl='http://192.168.31.112/index.php/';
          var cartList=[];
          var dataObj={};

        var service={
            getPostUrl:function () {
              return postUrl
            },
            setIdCard:function (idCard,userName) {
              userInformation['cid'] = idCard;
              userInformation['userName'] = userName;
            },
            getIdCard:function () {
              return  userInformation;
            },
            addToCart:function (data) {
              if(data){
                for(var i=0;i<cartList.length;i++){
                  if(cartList[i].id==data.id){
                    return cartList;
                  }
                }
                cartList.push(data);
              }
              else{
                return cartList;
              }
            },
            clearCart:function () {
              cartList=[];
            },
            formatData:function (data) {
              if(data){
                var dataList=data;
                angular.forEach(dataList,function (v,i) {
                  var statuCode=dataList[i].hxStateCode;
                    if(dataObj[statuCode]){
                      dataObj[statuCode].push(dataList[i])
                    }
                    else{
                      dataObj[statuCode]=[];
                      dataObj[statuCode].push(dataList[i])
                    }
                });
              }
                return dataObj;

            }
          };
          return service
      }])
