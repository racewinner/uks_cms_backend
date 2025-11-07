<?php
namespace App\Services;

class HttpService 
{
    public static function get($url, $data=null) {
        $ch = curl_init();

        if(isset($data)) {
            $params = "";
            foreach($data as $key=>$value) {
                if(strlen($params) > 0) $params .= "&";
                $params .= "$key=$value";
            }
            $url .= "?$params";
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Set content type if necessary
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'error' => curl_error($ch)
            ];
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpCode == 200) {
                $data = json_decode($response, true);
                return [
                    'data' => $data
                ];
            } else {
                return [
                    'error' => "Http Error: $httpCode<br/>url: $url"
                ];
            }
        }
    }

    public static function post($url, $data) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'error' => curl_error($ch)
            ];
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpCode == 200) {
                $data = json_decode($response, true);
                return [
                    'data' => $data
                ];
            } else {
                return [
                    'error' => "Http Error: $httpCode<br/>url: $url"
                ];
            }
        }
    }
}

?>