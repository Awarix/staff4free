<?php

    include "webapikey.php";
    include "Openid.php";

    $OpenID = new LightOpenID("testoops.e3w.ru");
    
    session_start();

    if(!$OpenID->mode){
        
        if(isset($_GET['login'])){
            $OpenID->identity = "http://steamcommunity.com/openid";
            header("Location: {$OpenID->authUrl()}");
        }
        
        if(!isset($_SESSION['T2SteamAuth'])){
            $login = "<div id=\"login\">Welcome Guest. Please <a href=\"?login\"><img scr=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png\"/></a> to Website Action .</div>";
        }
        
    } elseif($OpenID->mode == "cancel"){
        echo "User has canceled Authenticiation.";
    } else {
        
        if(!isset($_SESSION['T2SteamAuth'])){
            
            $_SESSION['T2SteamAuth'] = $OpenID->validate() ? $OpenID->identify : null;
            $_SESSION['T2SteamID64'] = str_replace("http://steamcommunity.com/openid/id/", "", $_SESSION['T2SteamAuth']);
            
            if($_SESSION['T2SteamAuth'] !== null){
                
                $Steam64 = str_replace("http://steamcommunity.com/openid/id", "", $_SESSION['T2SteamAuth']);
                $profile = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$api}&steamids={$Steam64}");
                $buffer = fopen("cache/{$Steam64}.json", "w+");
                fwrite($buffer, $profile);
                fclose($buffer);
            }
            
            header("Location: index.php");
        }
        
    }
    
    if(isset($_SESSION['T2SteamAuth'])){
        
        $login = "<div id=\"login\"><a href=\"?logout\">Logout</a></div>";
        
    }

    if(isset($_GET['logout'])){
        
        unset($_SESSION['T2SteamAuth']);
        unset($_SESSION['T2SteamID64']);
        header('Location: index.php');
    }
    
    
    
    echo $login;

    echo "<img scr=\"{$Steam->response->players[0]->avatarfull}\"/>";
    
    
?>
