<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class SmsController extends Controller
{
    //
    public function index()
    {
        $config = config('sms');

        $client  = new Client($config);
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers('phone_number');
        $sendSms->setSignName($config['signName']);
        $sendSms->setTemplateCode($config['templateCode']);
        $sendSms->setTemplateParam(['code' => rand(100000, 999999)]);
        $sendSms->setOutId('demo');

        print_r($client->execute($sendSms));

        /**
         * 支持
        官方网址： https://www.aliyun.com/product/sms?spm=5176.8142029.388261.339.WL7atM
        官方API文档： https://help.aliyun.com/document_detail/55451.html?spm=5176.doc55289.6.556.pMlBIe
        composer： https://getcomposer.org/

         * stdClass Object ( [Message] => OK [RequestId] => D7E470CD-A3F0-48BB-ADCC-61E309A22756 [BizId] => 326404353691140716^0 [Code] => OK )
         *
         * stdClass Object ( [Message] => phone_number invalid mobile number [RequestId] => 907CDC3C-5318-4593-AE7F-0832BA334C0B [Code] => isv.MOBILE_NUMBER_ILLEGAL )
         */

        return response('OK');
    }
}
