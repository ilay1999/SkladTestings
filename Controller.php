<?php 
        require_once('fpdf/tfpdf/tfpdf.php');
    //параметры для обработки страницы и асинхронной работы элементов ожидает параметров либо через аякслибо через якорь
        $Call = sanitize($_SERVER['REQUEST_URI']);
        $ProdGet = $_GET['Product']??0;
        $Products = $_POST['Product']??0;
    //Подключение к базе
        class DBase{
            private $Params = array(
                'host' => '127.0.0.1',
                'db_name' => 'sklad',
                'username' => 'root',
                'password' => ''
            );
            public $conn;
        // Cоединение с БД
            public function getConnection(){
                $this->conn = null;
                try {
                    $this->conn = new PDO("mysql:host=" . $this->Params['host'] . ";dbname=" . $this->Params['db_name'], $this->Params['username'], $this->Params['password']);
                    $this->conn->exec("set names utf8");
                } catch (PDOException $exception) {
                    echo "Ошибка: " . $exception->getMessage();
                }
                return $this->conn;
            }
        };
    //Сканирование по дате и ИД дериктории с дампами инвенторизаций
        class FindFiile{
            private $path = 'storage/';
            public $resultArr =[];
        //Основная публичная функция для поиска возвращает только массив с необходимыми для просмотра  данными
            public function Files($find){
                $files = scandir($this->path);
                $this->resultArr = $this->fileNameParser($files ,$find);
                return $this->resultArr;
            }
        //Разбирает имя файла на компоненты и на их основе смотрит подходящие, (выводит файл если есть хоть 1 совпадение по ИД так же учитывает была ли указана в фильтре дата для поиска)
            private function fileNameParser($files ,$findParams){
                $ResultArr = [];
                if(!empty($findParams['ID']) || !empty($findParams['Date'])){
                    foreach($files as $filen){
                        if ($filen != '.' && $filen != '..') {
                            $idsArr = explode(',', "".$filen);
                            foreach($idsArr as $id){
                                if(str_contains($findParams['ID'].",", $id.",")){
                                    if (!empty($findParams['ID']) && !empty($findParams['Date']) && str_contains($filen, $findParams['Date'])) {
                                        array_push($ResultArr, $this->path.$filen);
                                    }else if(empty($findParams['Date'])){
                                        array_push($ResultArr, $this->path.$filen);
                                    };
                                    break;
                                };
                            };
                            if (empty($findParams['ID']) && !empty($findParams['Date']) && str_contains($filen, $findParams['Date'])) {
                                array_push($ResultArr, $this->path.$filen);
                            };
                        };
                    };
                }else{
                    foreach($files as $filen){
                        if($filen != '.' && $filen != '..'){
                            array_push($ResultArr, $this->path.$filen);
                        };
                    };
                    return $ResultArr;
                };
                return $ResultArr;
            }  
        };
        class generatePDF{
            private $path = 'storage/';
            private $header = array(
                'Name' => "Название",'Left' => "Осталось",'Cost' => "Стоимость",'Error' => "Ошибка инв.",
            );

            public function generateInvPDF($IDs, $Products, $date){
                $filename = $this->path.$IDs.',_'.$date.'.pdf';

                $pdf = new tFPDF();
                $pdf->AddPage();
                $pdf->AddFont('DejaVu','','DejaVuSerif-Bold.ttf',true);
                $pdf->SetFont('DejaVu','',14);
                $this->prepareInvPDF($this->header, $Products, $pdf);
                $pdf->Output($filename,'F');
                return "Файл инвенторизации создан: ".$filename;
            }
            private function prepareInvPDF($header, $Products, $pdf){
                foreach($header as $col){
                    $pdf->Cell(47,7,$col,1);
                };
                $pdf->Ln();
                foreach($Products as $row)
                {
                    $row = explode(";", $row);
                    foreach($row as $col){
                        $pdf->Cell(47,6,$col,1);
                    };
                    $pdf->Ln();
                };
            }
        };
        //Для обращения к функциям через ajax
        switch ($ProdGet['Method']??$Products['Method']??$Call??0) {
            case 'Controller.php.updateProds':
                updateProds($Products);
                http_response_code(200);
                break;
            case 'Controller.php.inventorize':
                showInventorize($ProdGet);
                http_response_code(200);
                break;
            case 'Controller.php.dowFile':
                dowFile($ProdGet);
                http_response_code(200);
                break;
            case 'Controller.php.findFile':
                findFile($ProdGet);
                http_response_code(200);
                break;
        };
    //Добавление удаление остатка товара
        function updateProds($Products){
            $SqlLine = "";
            $ResultArr = [];
            $send = new DBase;
            $idsArr = explode(',', "".$Products['ID']);
            $StuffArr = explode(',', "".$Products['Stuff']);
            for($i=0; $i<=count($idsArr)-1; $i++){
                array_push($ResultArr, array(
                    'Stuff' => $StuffArr[$i],
                    'ID' => $idsArr[$i],
                ));
            };
            $connect = $send->getConnection();
            foreach($ResultArr as $Product){
                $SqlLine = $SqlLine . 'UPDATE `products` SET `left` = `left` + '.$Product['Stuff'].' WHERE `products`.`event_ID` = '.$Product['ID'].';';
            };
            $connect->prepare($SqlLine)->execute();
            ?><p>Значения следующих товаров обновлены: <?=$Products['ID']?></p><?php
        };
    //Отрисовка файлов подходящих по параметрам на странице
        function findFile($findParams){
            $filesBatch = new FindFiile;
            $files = $filesBatch -> Files($findParams);
            foreach($files as $fileName){
                ?><div class="productLine"><p><?= sanitize($fileName); ?></p><a href="<?= $fileName ?>" target="_blank">Посмотреть</a></p></div><?php
            };
        };
    //Инвенторизация запрос данных
        function inventorize($ProdGet){
            $SqlLine = '';
            $toReturn = [];
            $send = new DBase;
            $connect = $send->getConnection();
            $ID = $ProdGet['ID'];
            if(empty($ID) || is_null($ID)){
                $SqlLine = 'SELECT * FROM products;';
            }else{
                $idsArr = explode(',', "".$ID);
                foreach($idsArr as $Product){
                    $SqlLine = $SqlLine.','.$Product;
                };
                $SqlLine = sanitize($SqlLine);
                $SqlLine = "SELECT * FROM products WHERE FIND_IN_SET(event_ID, '$SqlLine');";
            };
            $toReturn = $connect -> query($SqlLine) -> fetchAll();
            return $toReturn;
        };
    //Инвенторизация сборка данных для страницы
        function showInventorize($ProdGet){
            $arrResult = []; 
            $IDs = $ProdGet['ID'];
            $Products = inventorize($ProdGet);
            foreach($Products as $Product){
                if($Product['left']<0){
                    $Error = $Product['left'];
                    $Product['left'] = 0;
                }else{
                    $Error = 0;
                };
                $Line = $Product['name'].";".$Product['left'].";".$Product['cost'].";".$Error;
                array_push($arrResult, $Line);
                ?><div class="productLine">
                    <p>Инвенторизация товара: <?= $Product['name']; ?> :(Проведена)</p>
                    <p><?= $Product['left']; ?></p>
                    <p><?= $Product['cost']; ?>-Р.</p>
                    <p><?= $Error; ?></p>
                </div><?php
            };
            $Result = InventorizePDF($IDs, $arrResult);
            ?><p><?= sanitize($Result); ?></p><?php
            return $Result;
        };
    //Собирает инвенторизацию в  PDF фаил
        function InventorizePDF($IDs, $Products){
            $date = sanitize(strval(Date('Y-m-d_TH:i:sP')));
            $createPDF = new generatePDF;
            $Result = $createPDF ->generateInvPDF($IDs, $Products, $date);
            return $Result;
        };
    //Для предостережения от иньекций так же удаление управляющих символов
        function sanitize($param){
            $cleanArr = array(
                "INSERT",  "INTO", "DELETE",  "UPDATE", "VALUES",  "FROM", "IF", "WHERE",  "CREATE", "NULL",  "SELECT", "INTO",  "(",
                ")",  "`", "'",  "$", "[",  "]", "{",  "}", "?", "/", ":",  "storage");
                $paramA = str_replace($cleanArr, "",$param);
            return $paramA;
        };
    
