angular.module('hardware.directives',[])
       .directive('commonHeader',function(){
       	  return{
       	   restrict:'ECA',
       	   templateUrl:'template/common_header.html',
       	   transclude:true
       	  }
       })
       .directive('commonFooter',function(){
       	  return{
       	  	restrict:'E',
       	  	transclude:false,
       	  	templateUrl:'template/tabs.html'
       	  }
       })
       .directive('scrollTop',['$window',function($window){
       	  return{
       	  	restrict:'E',
       	  	template:'<div class="scrollTop" ng-show="showHide" ng-click="scrollToTop();"></div>',
       	  	replace:true,
       	  	link:function(scope,ele,attr){

       	  }
       	  }
       }])
       .directive('scrollHeight',['$window',function($window){
         return{
           restrict:'AE',
           link:function(scope,element,attr){
             element[0].style.height=($window.innerHeight-44-49)+'px';
           }
         }
       }])

