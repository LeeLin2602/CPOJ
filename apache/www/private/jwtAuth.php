<?php
class jwtAuth{
    private static $header = array(
        'alg'=>'HS256',
        'typ'=>'JWT'
    );
    private static $secret = '53299622de72ecb94dd87fa83f8c124f';

    public static function getToken(array $payload){
        if(is_array($payload)){
            $b64header = self::base64urlEncode(json_encode(self::$header, JSON_UNESCAPED_UNICODE));
            $b64payload = self::base64urlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));
            $sign = self::signature($b64header.'.'.$b64payload, self::$secret, self::$header['alg']);
            $token = $b64header.'.'.$b64payload.'.'.$sign;
            return $token;
        }
    }
    public static function verifyToken(string $Token){
        $token = explode('.', $Token);
        if(count($token) != 3) return false;
        list($b64header, $b64payload, $sign) = $token;
        $tHeader = json_decode(self::base64urlDecode($b64header), JSON_OBJECT_AS_ARRAY);
        $payload = json_decode(self::base64urlDecode($b64payload), JSON_OBJECT_AS_ARRAY);
        if(empty($tHeader['alg'])) return false;
        if(self::signature($b64header.'.'.$b64payload, self::$secret, $tHeader['alg']) !== $sign) return false;
        if(isset($payload['iat']) && $payload['iat'] > time()) return false;
        if(isset($payload['exp']) && $payload['exp'] < time()) return false;
        if(isset($payload['nbf']) && $payload['nbf'] > time()) return false;

        return $payload;
    }

    private static function base64urlEncode(string $input){
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
    private static function base64urlDecode(string $input){
        $remainder = strlen($input) % 4;
        if($remainder){
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
    private static function signature(string $input, string $key, string $alg = 'HS256'){
        $alg_config=array(
            'HS256'=>'sha256'
        );
        return self::base64urlEncode(hash_hmac($alg_config[$alg], $input, $key,true));
    }
}
?>
