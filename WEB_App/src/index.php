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
        $authPage=true;
        echo '<script type="text/javascript">';
        echo 'var token = /access_token=([^&]+)/.exec(document.location.hash)[1];';
        echo 'document.location.href="' . $_SERVER['REQUEST_URI'] . '?token=" + token;';
        echo '</script>';
    }
    else
    {
        $token = $_GET['token'];
        $ch = curl_init('https://cloud-api.yandex.net/v1/disk/');
       
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, true);

        $usedSpace = round($res['used_space']/pow(1024,2), 1);
        $totalSpace = round($res['total_space']/pow(1024,3),1);
    }

    if ($authPage) {
            echo '<!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <link rel="SHORTCUT ICON" href="images/yandexLogo.ico" type="image/x-icon">
                <link rel="stylesheet"  href="styles/style.css">
                <title>Вход | Яндекс.Диск</title> 
            </head>
            <body>
                <div id="auth-form">
                    <form class="ui-form" method="post">
                        <img src="images/bg_logo.jpg" alt="yandex-logo" id="yaLogoInAuth">
                        <p>Чтобы начать, авторизуйтесь со своего Яндекс аккаунта.</p>
                        <p><input name="confirm-redirect" type="submit" value="Авторизоваться"></p>
                    </form>
                </div>
            </body>
            </html>';
    }
    else
    {
            echo '<!DOCTYPE html>
                    <html lang="ru">
                    <head>
                        <meta charset="UTF-8">
                        <link rel="SHORTCUT ICON" href="images/yandexLogo.ico" type="image/x-icon">
                        <link rel="stylesheet"  href="styles/style.css">
                        <title>Файлы на Яндекс.Диске</title> 
                    </head>
                    <body>
                    <h2>'
                     .$res['user']['display_name'].   
                    '</h2>
                    <p>Использовано '.$usedSpace.' МБ из '.$totalSpace.' ГБ</p></body>
                    </html>';
    }
    
?>
