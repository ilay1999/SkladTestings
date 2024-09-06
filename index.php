<html>
<head>
<meta charset="utf-8">
    <title>Тестовый вариант</title>
    <link rel="stylesheet" href="css/main.css"/>
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/main.js"></script>
</head>
    <body>
        <p>Днный интерфейс лишь показывает прием данных через  простые поля, обработка идет по порядку, подразумевается что они(данные) уже приходят массивом и далее разделяются через explode</p>
        <div>
            <h3>Оформить приход  расход по товарам</h3>
            <div>
                <input  id="selectProdRPID" type="text" name="" placeholder="Ввести ИД (пример: 1,2..)">
                <input  id="selectProdRPleft" type="text" name="" placeholder="Значения (пример: 10,-2..)">
                <button id="selectProdRPBtn" onclick="updateProd()">Добавить </button>
            </div>
            <div id="updDiv">
            </div>
        </div>
        <div>
            <h3>Создать инвенторизацию</h3>
            <div>
                <input  id="showProdInv" type="text" name="" placeholder="Ввести ИД (пример: 1,2..)">
                <button id="showProdBtn" onclick="showInv()">Создать инвенторизацию</button>
            </div>
            <div id="invDiv">
            </div>
        </div>
        <div>
            <h3>Поиск инвенторизаций по времени и по товару</h3>
            <div>
                <p>Ввести ИД товара по которым требуется инвенторизация при необходимости указать дату</p>
                <input  id="selectProdInv" type="text" name="" placeholder="Ввести ИД (пример: 1,2..)">
                <input  id="searchDate" type="date" name="">
                <button id="fileBtn" onclick="showFile()">Найти</button>
            </div>
            <div id="fileDiv">
            </div>
        </div>
    </body>
</html>
