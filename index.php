<?php 
    $app_id='5dea7068f0b04ee58013ddddbd804820';

    $url='https://oauth.yandex.ru/authorize?response_type=token&client_id='.$app_id;

    if($_POST['confirm-redirect'])
    { 
        header('Location: '.$url);
        exit();      
    }
    
    if (!isset($_GET['token']))
    {
        $autorized=false;

        echo '<script type="text/javascript">';
        echo 'var token = /access_token=([^&]+)/.exec(document.location.hash)[1];';
        echo 'if (token !=null)
                {
                document.location.href="' . $_SERVER['REQUEST_URI'] . '?token=" + token;
                }';
        echo '</script>';
    }
    else
    {
        $autorized=true;
        $token = $_GET['token'];
        $ud = curl_init('https://cloud-api.yandex.net/v1/disk/');
       
        curl_setopt($ud, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
        curl_setopt($ud, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ud, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ud, CURLOPT_HEADER, false);
        $usr = curl_exec($ud);
        curl_close($ud);

        $usr = json_decode($usr, true);

        $usedSpace = round($usr['used_space']/pow(1024,2), 1);
        $totalSpace = round($usr['total_space']/pow(1024,3),1);
    }

     
            echo '<!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <link rel="SHORTCUT ICON" href="images/yandexLogo.ico" type="image/x-icon">
                <link rel="stylesheet"  href="styles/style.css">';

                if (!$autorized){
                echo '<title>Вход | Яндекс.Диск</title> 
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
                        {
                            // Выведем список корневой папки.
                            $path = '/';

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

                            $fls = json_decode($fls, true);
                            
                                echo '<title>Файлы '.$usr['user']['login'].' на Яндекс.Диске</title> 
                                    </head>
                                    <body class="filesPage">
                                        <div class="userData">
                                            <h2>'
                                            .$usr['user']['display_name'].   
                                            '</h2>
                                            <p>Использовано '.$usedSpace.' МБ из '.$totalSpace.' ГБ</p>
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
                                            <div class="fileItem">
                                            <img src="images/folder.svg" class="dirImg"> 
                                            <p>'.$fls['_embedded']['items'][$i]['name'].'</p></div>';
                                        };

                                        if($fls['_embedded']['items'][$i]['type'] == 'file'){
                                            echo '
                                            <div class="fileItem">
                                            <img src="images/file.png" class="dirImg"> 
                                            <p>'.$fls['_embedded']['items'][$i]['name'].'</p></div>';
                                        };
                                    };     
                                        echo '</div>';
                        }

                    echo'</body>
                    </html>';
    

