<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Description of Codelord_secure
 *
 * @author Codelord
 */
class Codelord_secure extends Component {

    public $APPLICATION_ID = 'CODELORD';
    public $API_KEY_ANDROID = 'CODELORD_ANDROID';
    public $API_KEY_WEBAPP = 'CODELORD_ANDROID';

    public function _checkAuth() {
        date_default_timezone_set("Asia/Kolkata");
        /**
         * This Header is used for getting data for authentication
         */
        //         $headers = $_SERVER;
//
//            foreach ($headers as $header => $value) {
//                 echo "$header: $value <br />\n";
//            }
        //     echo 'aa'.$_SERVER['HTTP_API_' . self::TOKEN_ID . '_API_KEY_ANDROID'];
        //echo json_encode($_SERVER);
        // Yii::$app->end();
        $API_KEY_ANDROID = null;
        $API_KEY_WEBAPP = null;
        try {
            $API_KEY_ANDROID = $_SERVER['HTTP_API_' . $this->APPLICATION_ID . '_API_KEY_ANDROID'];
        } catch (\Exception $e) {
            
        }
        try {
            $API_KEY_WEBAPP = $_SERVER['HTTP_API_' . $this->APPLICATION_ID . '_API_KEY_WEBAPP'];
        } catch (\Exception $e) {
            
        }
        if (isset($API_KEY_ANDROID)) {

            if ($API_KEY_ANDROID != $this->API_KEY_ANDROID) {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Authentication Fail !',
                    'Error' => 'Please check your api key again'
                ];
                $this->_sendResponse(403, $Responce);
            }
        } else if (isset($API_KEY_WEBAPP)) {

            if ($API_KEY_WEBAPP != $this->API_KEY_WEBAPP) {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Authentication Fail !',
                    'Error' => 'Please check your api key again'
                ];
                $this->_sendResponse(403, $Responce);
            }
        } else {
            $Responce = [
                'Status_code' => '403',
                'Success' => 'False',
                'Message' => 'Forbidden',
                'Error' => 'Please check your api key again'
            ];
            $this->_sendResponse(403);
        }
    }

    /**
     * Sends the API response 
     * 
     * @param int $status 
     * @param string $body 
     * @param string $content_type 
     * @access private
     * @return void
     */
    public function _sendResponse($status = 200, $body = '', $content_type = 'application/json') {
// set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
// and the content type
        header('Content-type: ' . $content_type);
// pages with body are easy
        if ($body != '') {
// send the body
            echo json_encode($body);
        } else {
// create some body messages
            $message = '';
// this is purely optional, but makes the pages a little nicer to read
// for your users.  Since you won't likely send a lot of different status codes,
// this also shouldn't be too ponderous to maintain
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to use this service.';
                    break;
                case 403:
                    $message = 'Forbidden to use this service.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }
            $body = [
                'Status_code' => $status,
                'Success' => 'False',
                'Message' => $message,
            ];
            echo json_encode($body);
        }
        Yii::$app->end();
    }

    /**
     * Gets the message for a status code
     * 
     * @param mixed $status 
     * @access private
     * @return string
     */
    private function _getStatusCodeMessage($status) {
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}
