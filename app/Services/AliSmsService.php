<?php

namespace App\Services;

use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class AliSmsService
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    public function send($phone, $code)
    {
        \Log::info('=============== ali sms start ===============');
        $response = [
            'status' => self::STATUS_FAILURE,
            'message' => '',
        ];
        \Log::info('send-data:', ['phone' => $phone, 'code' => $code]);
        try {
            $config = config('sms');

            $client = new Client($config);
            $sendSms = new SendSms;
            $sendSms->setPhoneNumbers($phone);
            $sendSms->setSignName($config['signName']);
            $sendSms->setTemplateCode($config['templateCode']);
            $sendSms->setTemplateParam(['code' => $code]);
            $result = $client->execute($sendSms);
            \Log::info('send-return:', ['result' => json_encode($result)]);
            if($result && $result->Code == 'OK'){
                $response['status'] = self::STATUS_SUCCESS;
                $response['message'] = 'OK';
            }else{
                $response['message'] = $result->Code ?? '短信发送异常';
            }
        }catch (\Exception $exception){
            $response['message'] = $exception->getMessage();
            \Log::info('send-error:', ['errmsg' => $exception->getMessage()]);
        }
        \Log::info('=============== ali sms end ===============');

        return $response;
    }
}
