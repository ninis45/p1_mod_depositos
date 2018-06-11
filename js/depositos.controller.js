(function () {
    'use strict';
    
    angular.module('app.depositos')
    
    .controller('InputCtrl',['$scope','$http','$rootScope','$sce','$uibModal','$cookies','$timeout','Upload','ServiceDeposito',InputCtrl])
    .controller('ModalXLSCtrl', ['$scope','$http', '$uibModalInstance','$cookies','$timeout','Upload',ModalXLSCtrl])
    .controller('IndexCtrl',['$scope','$http','$uibModal',IndexCtrl])
    .controller('AddNuevoMiembroCtrl', ['$scope', '$uibModalInstance','conceptos','ServiceDeposito',AddNuevoMiembroCtrl])
    .factory('ServiceDeposito',[ServiceDeposito]);
    
    function ModalXLSCtrl($scope,$http,$uibModalInstance,$cookies,$timeout,Upload)
    {
        $scope.dispose   = false;
        $scope.depositos = [];
        $scope.cancel = function()
        {
             $uibModalInstance.dismiss("cancel");
        }
        $scope.upload_file = function(file)
        {
            
            $scope.dispose = true;
            
           
            if(!file) return false;
            
            file.upload = Upload.upload({
              url: SITE_URL+'admin/depositos/upload',
              data: {file:file,csrf_hash_name:$cookies.get(pyro.csrf_cookie_name)},
            });
            
            file.upload.then(function (response) {
              var  result = response.data;
              $timeout(function () {
                  //file.result = response.data;
                  $scope.dispose = false;
                  
                  
                  
                  if(result.status)
                  {
                      $scope.depositos = result.data;
                  }
                  console.log($scope.depositos);
                  
                  //$scope.status  = result.status;
                  //$scope.message = result.message;
                  
                  //if(result.status)
                  //{
                    //  $scope.users_result = result.data;
                  //}
                 // if(type == 'xml' )
                  //{
                      //item['total']    = data.total;
                      //item['messages'] = result.message;
                  //}
                  
                  //$scope.id_factura = response.data.data.id_factura;
                  //item[type] = data.id;
                 
                 
              });
            }, function (response) {
              if (response.status > 0)
                $scope.errorMsg = response.status + ': ' + response.data;
            }, function (evt) {
              
              file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            });
        }
    }
    function IndexCtrl($scope,$http,$uibModal)
    {
        
        $scope.open_import = function()
        {
             var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'importModal.html',
                            controller: 'ModalXLSCtrl',
                  
                            resolve: {
                                /*org_path: function () {
                                    return $scope.org_active;
                                }*/
                            }
                      });
        }
    }
    function ServiceDeposito()
    {
        return {
            
            concepto:false
        }
    }
    
    function AddNuevoMiembroCtrl ($scope, $uibModalInstance,conceptos,ServiceDeposito) {             
               
                $scope.add=function(){                                      
                    //$scope.concepto = {concepto:$scope.form.concepto};
                    //conceptos.length= 0;
                    var new_concepto = {concepto:$scope.form.concepto};
                    
                    conceptos.push(new_concepto);
                    
                    ServiceDeposito.concepto = $scope.form.concepto;
                    
                    $uibModalInstance.close();
                };
                $scope.cancel =function(){
                    $uibModalInstance.dismiss('cancel');
                };               
   }
    function InputCtrl($scope,$http,$rootScope,$sce,$uibModal,$cookies,$timeout,Upload,ServiceDeposito)
    {
        $scope.f_centro={};
        $scope.f_director={};
        $scope.conceptos = conceptos;
        $scope.concepto = '';
        $scope.depositos = depositos;
        $scope.tab   = 'single';
        
        console.log($scope.depositos);
        $scope.upload_file = function(file)
        {
            
            $scope.dispose = true;
            
           
            if(!file){ 
                 $scope.dispose = false;
                return false;
            }
            
            file.upload = Upload.upload({
              url: SITE_URL+'admin/depositos/upload',
              data: {file:file,csrf_hash_name:$cookies.get(pyro.csrf_cookie_name)},
            });
            
            file.upload.then(function (response) {
              var  result = response.data;
              $timeout(function () {
                  //file.result = response.data;
                  $scope.dispose = false;
                  
                  
                  
                  if(result.status)
                  {
                      $scope.depositos = result.data;
                  }
                  console.log($scope.depositos);
                  
                  //$scope.status  = result.status;
                  //$scope.message = result.message;
                  
                  //if(result.status)
                  //{
                    //  $scope.users_result = result.data;
                  //}
                 // if(type == 'xml' )
                  //{
                      //item['total']    = data.total;
                      //item['messages'] = result.message;
                  //}
                  
                  //$scope.id_factura = response.data.data.id_factura;
                  //item[type] = data.id;
                 
                 
              });
            }, function (response) {
              if (response.status > 0)
                $scope.errorMsg = response.status + ': ' + response.data;
            }, function (evt) {
              
              file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            });
        }
        $scope.showModal=function () 
        {
            $scope.animationsEnabled = true;
            var modalInstance = $uibModal.open({
                    animation: $scope.animationsEnabled,
                    templateUrl: 'add-miembros.html',
                    controller: 'AddNuevoMiembroCtrl',
                      
                        resolve: 
                        {
                              conceptos: function () 
                              {
                                    return $scope.conceptos;
                              }
                        }
                      });
        }
        
        $scope.$watch('f_centro.selected',function(newValue,oldValue){
            
            
            
            var id_centro = newValue;
           
            if(!newValue)
            {
               return ;
            }
            
            
            if(oldValue && oldValue != newValue )$scope.f_director='';
            
            $http.post(SITE_URL+'admin/depositos/list_directores',{id_centro:id_centro}).then(function(response){
                
                $scope.f_directores = response.data;
                
            });
           
            
            
            
        });
         $scope.$watch('f_director.selected',function(newValue,oldValue){
            
                if(newValue == oldValue)
                {
                   return ;
                }
              
              $scope.f_banco = newValue.banco;
              $scope.f_tarjeta = newValue.no_tarjeta;
         
         });
         $scope.$watch('conceptos',function(newValue,oldValue){
              
              
              if(newValue == oldValue) return false;
              
              $scope.concepto = ServiceDeposito.concepto;
            
         },true);
    }
})();