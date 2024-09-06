SQL dump inside

You can ask straightly to Controller.php
POST http://sklad/Controller.php?.updateProds
GET http://sklad/Controller.php?.inventorize
GET http://sklad/Controller.php?.dowFile
GET http://sklad/Controller.php?.findFile

Example of task body

$.ajax ({
            url: "Controller.php",
            type: "GET",
            data: {
                Product: { 
                    Method: 'Controller.php.findFile', 
                    ID: '1,2,3,4', 
                    Stuff: '-3,-4,2,3', 
                    Date: "2024-09-04",  //Y-m-d format or Y-m-d_TH:i:sP
                },                                            
            },                
            dataType: "html",
            success:  function(){
                }
        });