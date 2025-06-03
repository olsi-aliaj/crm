<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../third_party/node.php';
use Firebase\JWT\JWT as api_JWT;
use Firebase\JWT\Key as api_Key;
use WpOrg\Requests\Requests as api_Requests;

class api_aeiou
{
    private static $bearer = 'k5ua8qyjLZI3mZ21kISqbh3B3v6UUaFw';

    public static function getPurchaseData($code)
    {
        $givemecode = api_Requests::get(GIVE_ME_CODE)->body;
        $bearer     = get_instance()->session->has_userdata('bearer') ? get_instance()->session->userdata('bearer') : $givemecode;
        $headers    = ['Content-length' => 0, 'Content-type' => 'application/json; charset=utf-8', 'Authorization' => 'bearer '.$bearer];
        $verify_url = 'https://api.envato.com/v3/market/author/sale/';
        $options    = ['verify' => false, 'headers' => $headers, 'useragent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'];
        $response   = api_Requests::get($verify_url.'?code='.$code, $headers, $options);

        return ($response->success) ? json_decode($response->body) : false;
    }

    public static function verifyPurchase($code)
    {
        $verify_obj = self::getPurchaseData($code);
        return ((false === $verify_obj) || !is_object($verify_obj) || isset($verify_obj->error) || !isset($verify_obj->sold_at) || ('' == $verify_obj->supported_until)) ? $verify_obj : null;
    }

    public function validatePurchase($module_name)
    {
        return true;
    }
}
