<?php 
echo '
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="SHORTCUT ICON" href="images/yandexLogo.ico" type="image/x-icon">
    <link rel="stylesheet"  href="styles/style.css">';

    if (!$autorized){ //страница авторизации
echo '
<title>Вход | Яндекс.Диск</title> 
</head>
<body class="authPage">
    <div id="auth-form">
        <form class="ui-form" method="post">
            <img src="images/bg_logo.jpg" alt="yandex-logo" id="yaLogoInAuth">
            <p>Чтобы начать, авторизуйтесь со своего Яндекс аккаунта.</p>
            <p><input name="confirm-redirect" type="submit" value="Авторизоваться"></p>
        </form>
    </div>';
}
    else
{ //страница с файлами

    if (!isset($_GET['dir']))
    {
    $path = '/';
    $yd_file = '/'.$_GET['file'];  

    }
    else
    {
        $path = '/'.$_GET['dir'];
        $yd_file = '/'.$_GET['dir'].'/'.$_GET['file'];  
    }
    // Оставим только названия и тип.
    $fields = '_embedded.items.name,_embedded.items.type';

    $limit = 100;
    
    $fd = curl_init('https://cloud-api.yandex.net/v1/disk/resources?path=' . urlencode($path) . '&fields=' . $fields . '&limit=' . $limit);
    curl_setopt($fd, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
    curl_setopt($fd, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($fd, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($fd, CURLOPT_HEADER, false);
    $fls = curl_exec($fd);
    curl_close($fd);

    $downloadPlace = __DIR__ ;

    $ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/download?path=' . urlencode($yd_file));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($res, true);
    if (empty($res['error'])) {
        $file_name = $downloadPlace . '/' . basename($yd_file);
        $file = @fopen($file_name, 'w');

        $ch = curl_init($res['href']);
        @curl_setopt($ch, CURLOPT_FILE, $file);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);
        @fclose($file);
    }

    $fls = json_decode($fls, true);
    
        echo '<title>Файлы '.$usr['user']['login'].' на Яндекс.Диске</title> 
            </head>
            <body class="filesPage">
                <div class="userData">
                    <h2>'
                    .$usr['user']['display_name'].   
                    '</h2>
                    <p>Использовано '.$usedSpace.' из '.$totalSpace.' ('.$percSpace.')</p>
                </div>
                <h3>Файлы: '.$path.'</h3>
                <div id="newDir">
                <img src="images/createFolder.png">
                </div>
                <div class="userFiles">';

        $countFiles = count($fls['_embedded']['items']);
            for ($i=0; $i < $countFiles ; $i++) {

                if($fls['_embedded']['items'][$i]['type'] == 'dir'){
                    echo '

                    <div class="fileItem dir" >
                        <img src="images/folder.svg" class="dirImg"> 
                        <p class="dirName">'.$fls['_embedded']['items'][$i]['name'].'</p>
                    </div>';

                };

                if($fls['_embedded']['items'][$i]['type'] == 'file'){

                    echo '
                    <div class="fileItem">
                        <img src="images/file.png" class="dirImg"> 
                        <p class="fileName">'.$fls['_embedded']['items'][$i]['name'].'</p>
                    </div>';

                };
            };     
                echo '</div>';
}

echo'</body>
<script src="js/script.js"> </script>
</html>';