angular.module('hardware.controllers',[])
  .controller('loginCtrl',['$scope','$rootScope','$state','$http','saveToLocalStorage','IonicService','pluginService','ConfigService',function ($scope,$rootScope,$state,$http,saveToLocalStorage,IonicService,pluginService,ConfigService) {
    $scope.data={};
    $scope.login=function () {
      pluginService.loadingOpen();
      var data={
        'username':$scope.data.USERNAME,
        'userpwd':md5($scope.data.PASSWORD),
        'cid':$scope.data.idCard
      };
      IonicService.login(data).then(function (data) {
        pluginService.loadingClose();
      if(data == 'error') {
      	pluginService.showToast('数据请求失败，请稍后再试~');
      	return false
      }
      else if(data.msg=='success'){
          ConfigService.setIdCard($scope.data.idCard,data.username);
          $state.go('tabs.orderList')
        }
      else{
          pluginService.showToast(data);
        }
      })
    }
  }])
  .controller('goodsListCtrl',['$scope','$ionicScrollDelegate','$state','pluginService','IonicService','ConfigService','$ionicModal',function ($scope,$ionicScrollDelegate,$state,pluginService,IonicService,ConfigService,$ionicModal) {
    pluginService.loadingOpen();
    var result=ConfigService.getIdCard();
    var cid=result.cid;
    var data={'cid':cid};
    IonicService.getGoodsCategory(data).then(function (data) {
    	pluginService.loadingClose();
    	if(data == 'error') {
      	pluginService.showToast('数据请求失败，请稍后再试~');
      	return false
     }
      $scope.productList=data;
      var firstId=data[0].categoryId;
      var data={'cid':cid,'categoryId':firstId};
      IonicService.getGoodsList(data).then(function (data) {
        pluginService.loadingClose();
	      if(data == 'error') {
	      	pluginService.showToast('数据请求失败，请稍后再试~');
	      	return false
	      }
		    if(data.result=='false'){
		      $scope.goodsList=null;
		      return false;
		    }else{
		      $scope.goodsList=data;
		    }
      })
    })
    $scope.goodsListScroll=function(){
      $scope.showHide=$ionicScrollDelegate.getScrollPosition().top>200?true:false;
      $scope.$apply();
    };
    $scope.scrollToTop=function () {
      $ionicScrollDelegate.scrollTop(3000);
    };
    $scope.onSwipeLeft=function () {
      $state.go('tabs.personalCenter');
    };
    $scope.onSwipeRight=function () {
      $state.go('tabs.orderList');
    };
    $scope.menuShow=true;
    $scope.i=0;
    $scope.getData=function (id,index) {
      pluginService.loadingOpen();
      $scope.i=index;
      var data={'cid':cid,'categoryId':id}
      IonicService.getGoodsList(data).then(function (data) {
        pluginService.loadingClose();
	      if(data == 'error') {
	      	pluginService.showToast('数据请求失败，请稍后再试~');
	      	return false
	      };
        if(data.result=='false'){
          $scope.goodsList=null;
          return false;
        }
        else{
          $scope.goodsList=data;
        }
      })
    };
    $scope.addToCart=function (index) {
    	if(!$scope.goodsList[index].totalqty || $scope.goodsList[index].totalqty <=0){
    		pluginService.showToast('库存不足，请及时补货');
    		return false;
    	}
      var goods=$scope.goodsList[index];
      goods.num=1;
      ConfigService.addToCart(goods);
      pluginService.showToast(goods.name+'加入购物车成功')
    };
    $scope.goCart=function () {
      $state.go('cart');
    };
    $ionicModal.fromTemplateUrl('searchGoodsList.html', {
      scope: $scope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $scope.modal = modal;
      $scope.addToCart1=function (index) {
      if(!$scope.searchList[index].totalqty || $scope.searchList[index].totalqty <=0){
    		pluginService.showToast('库存不足，请及时补货');
    		return false;
    	}
        var goods=$scope.searchList[index];
        goods.num=1;
        ConfigService.addToCart(goods);
        pluginService.showToast(goods.name+'加入购物车成功')
      };
    });
    $scope.search={};
    $scope.openModal = function() {
      if(!$scope.search.keywords){
        pluginService.showToast('请输入查询商品')
        return false;
      }
      $scope.modal.show();
      pluginService.loadingOpen();
      var data={'cid':cid,'keywords':$scope.search.keywords};
      IonicService.search(data).then(function (data) {
        pluginService.loadingClose();
	     if(data == 'error') {
	      	pluginService.showToast('数据请求失败，请稍后再试~');
	      	return false
	     }
        if(data.result=='false'){
          pluginService.showToast('未查询到相关商品')
          return false;
        }
        $scope.searchList=data;
      })
    };
    $scope.closeModal = function() {
      $scope.modal.hide();
    };
  }])
  .controller('orderListCtrl',['$scope','$state','$ionicTabsDelegate','ConfigService','IonicService','pluginService','$rootScope',function ($scope,$state,$ionicTabsDelegate,ConfigService,IonicService,pluginService,$rootScope) {
    pluginService.loadingOpen();
    var result=ConfigService.getIdCard();
    var cid=result.cid;
    var data={'cid':cid};
    IonicService.getOrderList(data).then(function (data) {
      pluginService.loadingClose();
      if(data == 'error') {
      	pluginService.showToast('数据请求失败，请稍后再试~');
      	return false
      }
      else if(data.length==0){
        $scope.orderList=null;
        $scope.params=null;
        $scope.param=null;
        return false;
      }
      $scope.params=data;
      $scope.orderList=$scope.params;
      $scope.param=ConfigService.formatData(data);

    });
    $scope.onSwipeLeft=function () {
      $state.go('tabs.goodsList');
    };
    $scope.onTabSelected=function (index) {
      if(!$scope.params){
        return false;
      }
      switch (index){
        case 0:$scope.orderList=$scope.param['0'];
          break;
        case 1:$scope.orderList=$scope.param['1'];
          break;
        case 2:$scope.orderList=$scope.param['2'];
          break;
        case 3:$scope.orderList=$scope.params;
          break;
      }
    }
  }])
  .controller('personalCenterCtrl',['$scope','$state','IonicService','pluginService','ConfigService',function ($scope,$state,IonicService,pluginService,ConfigService) {
  	pluginService.loadingOpen();
    var result=ConfigService.getIdCard();
    var cid=result.cid;
    var userName = result.userName;
    var data={'cid':cid};
    IonicService.getInvoiceInfo(data).then(function(data){
    	pluginService.loadingClose();
    	if(data == 'error') {
      	pluginService.showToast('数据请求失败，请稍后再试~');
      	return false
      }
    	$data = data.data;
    	if(data.status == 200 && $data.items.length!=0){
    	 $scope.userName = userName ? userName : '用户名';	
    	 $scope.inventory={};	
    	 $scope.contact={};	
    	 $scope.sales={};	
    	 $scope.inventory['total1'] = $data.items[0].total1 ? $data.items[0].total1 : 0;
    	 $scope.inventory['total2'] = $data.items[0].total2 ? $data.items[0].total2 : 0;
     	 $scope.contact['total1'] = $data.items[2].total1 ?$data.items[2].total1 : 0;
    	 $scope.contact['total2'] = $data.items[2].total2 ?$data.items[2].total2 : 0;
     	 $scope.sales['total1'] = $data.items[3].total1 ? $data.items[3].total1 : 0;
    	 $scope.sales['total2'] = $data.items[3].total2 ? $data.items[3].total2 : 0;
    	}

    })
    $scope.onSwipeRight=function () {
      $state.go('tabs.goodsList');
    };
  }])
  .controller('cartCtrl',['$scope','$state','ConfigService','pluginService',function ($scope,$state,ConfigService,pluginService) {
    $scope.onSwipeRight=function () {
      $state.go('tabs.goodsList')
    };
    $scope.onSwipeLeft=function () {
      $state.go('pay');
    };
    $scope.cartList=ConfigService.addToCart();
    if($scope.cartList.length==0){
      $scope.nothing=true;
    }
    $scope.goodsNumAdd=function (index) {
      if($scope.cartList[index].num >=$scope.cartList[index].totalqty){
      	pluginService.showToast('商品数量不能大于库存数量')     
      	return false;
      }
      $scope.cartList[index].num=parseInt($scope.cartList[index].num)+1;
      
    };
    $scope.goodsNumMin=function (index) {
      if($scope.cartList[index].num<2){
        return false;
      }
      $scope.cartList[index].num=parseInt($scope.cartList[index].num)-1;
    };
    $scope.goToAccount=function () {
      $state.go('pay')
    };
    $scope.clearShopCart=function () {
      ConfigService.clearCart();
      $scope.cartList=ConfigService.addToCart();
    }
  }])
  .controller('payCtrl',['$scope','$state','ConfigService','IonicService','pluginService',function ($scope,$state,ConfigService,IonicService,pluginService) {
    var result=ConfigService.getIdCard();
    var cid=result.cid;
    var str="";
    var orderNum;
    $scope.onSwipeRight=function () {
      $state.go('cart')
    };
    $scope.goodsList=ConfigService.addToCart();
    $scope.totalPrice=0;
    for(var i=0;i<$scope.goodsList.length;i++){
      $scope.totalPrice+=$scope.goodsList[i].salePrice*$scope.goodsList[i].num;
      str+=$scope.goodsList[i].id+'-'+$scope.goodsList[i].num+'-'+$scope.goodsList[i].salePrice+',';
    }
    $scope.money={};
    $scope.money.finalTotalPrice=$scope.totalPrice;
    str=str.substring(0,str.length-1);
    $scope.pay=function () {
      pluginService.loadingOpen();
      var param=ConfigService.getIdCard();
      var data={'cid':cid,'goodsList':str,'totalMoney':$scope.money.finalTotalPrice,'uid':param.uid,'username':param.userName};
      IonicService.goToPay(data).then(function (data) {
        pluginService.loadingClose();
	      if(data == 'error') {
	      	pluginService.showToast('数据请求失败，请稍后再试~');
	      	return false
	      };
        if(data.result=='success'){
          orderNum=data.data.orderNum;
        }
      })
    }
  }]);
