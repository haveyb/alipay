<?php
namespace haveyb\AliPay;

include 'config.php';

class Base extends Rsa
{
    /**
     * 返回按照支付宝要求处理的urI
     *
     * @param $arr
     * @param string $type
     * @return string
     */
    public function getHandledUrI($arr, $type = 'RSA2')
    {
        // 筛选
        if (isset($arr['sign'])) {
            unset($arr['sign']);
        }
        if (isset($arr['sign_type']) && $type == 'RSA') {
            unset($arr['sign_type']);
        }
        // 排序
        ksort($arr);
        // 拼接
        return $this->getUrl($arr,false);
    }

    /**
     * 将数组转换为 uri 格式的字符串
     *
     * @param $arr
     * @param bool $encode
     * @return string
     */
    public function getUrl($arr, $encode = true)
    {
        return true == $encode ? http_build_query($arr) : urldecode(http_build_query($arr));
    }

    /**
     * 获取 md5、rsa、rsa2 中指定方式的签名
     *
     * @param $arr
     * @param string $type
     * @return string
     */
    public function getSign($arr, $type = 'RSA2')
    {
        $sign = '';
        switch ($type) {
            case 'MD5' :
                $sign = md5($this->getHandledUrI($arr, 'MD5') . ALI_MD5_KEY);
                break;
            case 'RSA' :
                $sign = $this->rsaSign($this->getHandledUrI($arr, 'RSA'), APP_PRIVATE_KEY) ;
                break;
            case 'RSA2' :
                $sign = $this->rsaSign($this->getHandledUrI($arr,'RSA2'),
                    APP_PRIVATE_KEY,'RSA2') ;
                break;
            default :
                break;
        }
        return $sign;
    }

    /**
     * 将处理后得到的签名存入数组，并返回 (MD5、RSA、RSA2)
     *
     * @param $arr
     * @param string $type
     * @return string
     */
    public function setSign($arr, $type = 'RSA2')
    {
        switch ($type) {
            case 'MD5' :
                $arr['sign'] = $this->getSign($arr, 'MD5');
                break;
            case 'RSA' :
                $arr['sign'] = $this->getSign($arr, 'RSA');
                break;
            case 'RSA2' :
                $arr['sign'] = $this->getSign($arr, 'RSA2');
                break;
            default :
                break;
        }
        return $arr;
    }

    /**
     * 验证 md5 方式的签名
     *
     * @param $arr
     * @return bool
     */
    public function checkMd5Sign($arr)
    {
        $sign = $this->getSign($arr, 'MD5');
        return $sign == $arr['sign'] ? true : false;
    }

    /**
     * 验证是否来自支付宝的通知
     *
     * @param $arr
     * @return bool
     */
    public function isAliPay($arr)
    {
        $checkUrl = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . ALI_PID . '&notify_id=';
        $checkUrl .= $arr['notify_id'];
        $str = file_get_contents($checkUrl);
        return $str == 'true' ? true : false;
    }

    /**
     * 验证交易状态
     *
     * @param $arr
     * @return bool
     */
    public function checkOrderStatus($arr)
    {
        return $arr['trade_status'] == 'TRADE_SUCCESS' || $arr['trade_status'] == 'TRADE_FINISHED' ? true : false;
    }

    /**
     * 验证订单金额 TODO
     *
     * @param $postData
     * @return bool
     */
    public function checkOrderFee($postData)
    {
        return true;
        /*
        // 这里假设订单金额为0.03，订单号为54121548845，使用时数据库中查询验证
        $orderNumber = '54121548845';
        $totalFee = '0.03';
        if ($postData['out_trade_no'] != $orderNumber || $postData['total_fee'] != $totalFee) {
            return false;
        } else {
            return true;
        }
        */
    }

    /**
     * 更改数据库中订单状态 TODO
     *
     * @param $postData
     * @return bool
     */
    public function changeOrderStatus($postData)
    {
        // 去数据库中更改订单状态
        return true;
    }

    /**
     * 记录日志(使用时建议改为存储在数据库上)
     *
     * @param $filename
     * @param $data
     */
    public function logs($filename,$data)
    {
        file_put_contents('./logs/' . $filename, $data . PHP_EOL, FILE_APPEND);
    }

}