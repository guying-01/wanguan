angular.module('hardware.factory',[])
       .factory('saveToLocalStorage',function () {
           var service={};
           service.saveSessionToLocalstorage=function (test) {
             var userArr=['AREA_CODE','AREA_NAME','AUTH_CODE','IP','page','NAME','RIGHTS','ROLE_ID','LAST_LOGIN','STATUS','role','SKIN']
             userArr.forEach(function (v,i) {
               var reg = new RegExp('[\\s]'+v+'=[\\S]+','g');
               var getData=test.match(reg);
               if(v=='AUTH_CODE'){
                 var str=getData[0].split(',')[0].split('=')[1];
                 str=str.substr(0,str.length-1);
                 localStorage.setItem(userArr[i],str)
               }
               else{
                 localStorage.setItem(userArr[i],getData[0].split(',')[0].split('=')[1])
               }
             })
           }
           return service;
       })
