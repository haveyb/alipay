<?php
namespace haveyb\AliPay;

class AliPay extends Base
{
    private $orderInfo;
    public function __construct($type = 'RSA2', $orderInfo = []) {
        $this->logs('log.txt', 'this is test log');
        $this->orderInfo = $orderInfo;
        if ($type == 'MD5') {
            $this->md5Pay();
        } elseif ($type == 'RSA') {
            $this->rsaPay();
        } elseif($type == 'RSA2') {
            $this->rsa2Pay();
        }
    }

    public function md5Pay(){
        $params = $this->getOldPayParams($this->orderInfo, 'MD5');

        $params = $this->setSign($params, 'MD5');

        $url = PAY_GATEWAY . '?' . $this->getUrl($params);
         header("location:" . $url);
    }

    public function rsaPay(){

        $params = $this->getOldPayParams($this->orderInfo, 'RSA');

        $params = $this->setSign($params, 'RSA');

         $url = PAY_GATEWAY . '?' . $this->getUrl($params);
         header("location:" . $url);
    }

    public function rsa2Pay(){
        $orderInfo = $this->orderInfo;
        //公共参数
        $pub_params = [
            'app_id'    => ALI_PAY_APP_ID,
            'method'    =>  'alipay.trade.page.pay', // 接口名称 固定值
            'format'    =>  'JSON', // 固定值
            'return_url'    => RETURN_URL,
            'charset'    =>  'UTF-8',
            'sign_type'    =>  'RSA2',
            'sign'    =>  '', // 签名
            'timestamp'    => date('Y-m-d H:i:s'), // 发送时间
            'version'    =>  '1.0', // 固定值
            'notify_url'    => NOTIFY_URL,
            'biz_content'    =>  '', //业务请求参数的集合
        ];
        
        //业务参数
        $api_params = [
            'product_code'  => 'FAST_INSTANT_TRADE_PAY', // 固定值
            'out_trade_no'  => $orderInfo['order_id'], // 商户订单号
            'total_amount'  => $orderInfo['total_fee'], // 总价 单位为元
            'subject'  => $orderInfo['order_title'], // 订单标题
            'body' => $orderInfo['goods_desc']
        ];
        $pub_params['biz_content'] = json_encode($api_params,JSON_UNESCAPED_UNICODE);

        $pub_params =  $this->setSign($pub_params, 'RSA2');

        $url = RSA2_PAY_GATEWAY . '?' . $this->getUrl($pub_params);
        header("location:" . $url);
    }

    /**
     * 获取老版md5或rsa方式的请求参数
     *
     * @param $orderInfo
     * @param string $type
     * @return array
     */
    private function getOldPayParams($orderInfo, $type = 'MD5')
    {
        return [
            // 固定值
            'service' => 'create_direct_pay_by_user',
            'partner' => ALI_PID,
            // 卖家用户号 seller_id、seller_email、seller_account_name至少传一个
            'seller_id' => ALI_PID,
            // 网站编码格式
            '_input_charset' => 'UTF-8',
            // 加密方式 老版的RSA 或 MD5
            'sign_type' => $type,
            'sign' => '',
            // 同步通知地址
            'return_url' => RETURN_URL,
            // 异步通知地址
            'notify_url' => NOTIFY_URL ,
            //支付类型，固定值
            'payment_type' => 1,

            // 订单标题，最长128汉字
            'subject' => isset($orderInfo['order_title']),
            // 商城网站唯一订单号
            'out_trade_no' => isset($orderInfo['order_id']),
            // 交易总金额，单位为元
            'total_fee' => $orderInfo['total_fee'],
            // 商品描述，可空
            'body' => isset($orderInfo['goods_desc']),
        ];
    }

}
