

// Фукнция созданная для очистки элементов

    // const fileBtn = document.getElementById('fileBtn');
    // fileBtn.addEventListener('click', showFile);
    
    function updateProd(){
        let ID = document.getElementById('selectProdRPID').value;
        let Stuff = document.getElementById('selectProdRPleft').value;
        $.ajax ({
            url: "Controller.php",
            type: "POST",
            data: {
                Product: {
                    Method: 'Controller.php.updateProds',
                    ID: ID,
                    Stuff: Stuff,  
                },                                    
            },                
            dataType: "html",
            success:  function(data){
                destroy('updDiv');
                document.getElementById('updDiv').innerHTML = data;
                }
        });
    };
    function showInv(){
        let ID = document.getElementById('showProdInv').value;
        $.ajax ({
            url: "Controller.php",
            type: "GET",
            data: {
                Product: {
                    Method: 'Controller.php.inventorize',
                    ID: ID,
                },                                     
            },                
            dataType: "html",
            success:  function(data){
                destroy('invDiv');
                document.getElementById('invDiv').innerHTML = data;
                }
        });
    };
    function showFile(){
        let date = document.getElementById('searchDate').value;
        let ID = document.getElementById('selectProdInv').value;
        $.ajax ({
            url: "Controller.php",
            type: "GET",
            data: {
                Product: {
                    Method: 'Controller.php.findFile', 
                    ID: ID, 
                    Stuff: '', 
                    Date: date,  
                },                                            
            },                
            dataType: "html",
            success:  function(data){
                destroy('fileDiv');
                document.getElementById('fileDiv').innerHTML = data;
                }
        });
    };
    function destroy(destroyTo){
        document.getElementById(destroyTo).innerHTML = "";
    };
