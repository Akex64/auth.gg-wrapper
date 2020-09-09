<?php

error_reporting(1);

session_start();

$apikey = "YOURAPIKEY";
$salt = "YOURCUSTOMSALT";

function hash_($string){
	return md5($string.$salt);
}

if(isset($_GET["e"])){
	if(isset($_GET["a"])){
		echo md5($_GET["a"].$salt);
		exit;
	}
}

class auth
{

    private static $certkey = "sha256//tiYvhtK5CL1cwrBCLCdXqpiEW0iNAo/PuASOr4aOsLg=";
    private static $api = "https://api.auth.gg/php/";

    public static function init()
    {
    	$aid = "YOUR-AID";
    	$secret = "YOUR-SECRET";
        $ch = curl_init(self::$api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_PINNEDPUBLICKEY, self::$certkey);
        $_SESSION["aid"] = $aid;
        $_SESSION["secret"] = $secret;
        $values = 
        [
        	"type" => "start", 
        	"aid" => $aid, 
        	"secret" => $secret
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result);
        switch ($data->status)
        {
            case "failed":
            die("error-sql");
            break;
            case "success":
            break;
            default:
            break;
        }
    }
    public static function login($license)
    {
        $ch = curl_init(self::$api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_PINNEDPUBLICKEY, self::$certkey);
        $values = 
        [
        "type" => "login", 
        "username" => $license, 
        "password" => $license,
        "aid" => $_SESSION["aid"], 
        "secret" => $_SESSION["secret"]
    	];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        switch ($data->info)
        {
            case "time expired":
                echo hash_("170".strval(time())); # subscription expired
                return false;
            case "invalid login":
                echo hash_("160".strval(time())); # invalid credentials
                return false;
            case "user does not exist":
                echo hash_("150".strval(time())); #user does not exist
                return false;
            case "success":
 				echo hash_("200".strval(time())); #success / loggedin
                return true;
            default:
            break;
        }
    }
    public static function register($license){
    	
    	$ch = curl_init(self::$api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_PINNEDPUBLICKEY, self::$certkey);
        $values = 
        [
        "type" => "register", 
        "username" => $license, 
        "password" => $license,
        "license"=>$license,
        "email"=>"myemail@example.com",
        "aid" => $_SESSION["aid"], 
        "secret" => $_SESSION["secret"]

    	];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        echo $data->info;
        switch ($data->info)
        {
            case "invalid license":
                echo hash_("300".strval(time())); # invalid license
                return false;
            case "user exists":
                return auth::login($license); #login / success
            case "email used":
                return auth::login($license); #login / success
            case "success":
                return auth::login($license); #login / success
            default:
            break;
        }
    }
}


if(isset($_GET[("check")])){
	if(isset($_GET["token"])){
		if(isset($_GET["hash"])){

			auth::init();  #initializes auth
			$hwid_obj = json_decode(file_get_contents("https://developers.auth.gg/USERS/?type=fetch&authorization=".$apikey."&user=".$_GET["token"]),true); # gets hwid json and decodes it
			$hwid = $hwid_obj["variable"]; #gets variable from decoded json
			if($hwid == "nohwid"){ # we are using the variable for hwid so when you reset just change to "nohwid" ill include a little reset api :)
				$res = file_get_contents("https://developers.auth.gg/USERS/?type=editvar&authorization=".$apikey."&user=".$_GET["token"]."&value=".$_GET["hash"]); 
				auth::login($_GET["token"]); #logs in
			}else{
				if($hwid == $_GET["hash"])
                {
					auth::login($_GET["token"]); #logs in
				}
                else
                {
					echo hash_("140".strval(time())); # invalid hash
				}
			}

		}else{
			echo "invalid parameters"; # not enough parameters
		}
	}else{
		echo "invalid parameters"; # not enough parameters
	}
}

?>
