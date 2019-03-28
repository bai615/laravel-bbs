<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use App\Services\AliSmsService;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, AliSmsService $smsService)
    {
        $phone = $request->phone;

        if(!app()->environment('production')){

            // 测试环境验证码
            $code = '1234';

        }else {

            // 生成4位随机数，左侧补0
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

            try {
                $result = $smsService->send($phone, $code);
                if ($result['status'] != 'success') {
                    return $this->response->errorInternal('短信发送异常');
                }
            } catch (\Exception $exception) {
                \Log::error('send-phone-code-error:');
                return $this->response->errorInternal('短信发送异常');
            }

        }

        $key = 'varificationCode_' . str_random(15);
        $expiredAt = now()->addMinutes(10);
        // 缓存验证码 10分钟过期
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }

    public function store_easysms(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;

        // 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        try{
            $result = $easySms->send($phone, [
                'content' => "【Lbbs社区】您的验证码是{$code}。如非本人操作，请忽略本短信"
            ]);
        }catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception){
            $message = $exception->getException('yunpian')->getMessage();
            return $this->response->errorInternal($message ?: '短信发送异常');
        }

        $key = 'varificationCode_' . str_random(15);
        $expiredAt = now()->addMinutes(10);
        // 缓存验证码 10分钟过期
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }

    public function store_test()
    {
        return $this->response->array(['test_message' => 'store verification code']);
    }
}
