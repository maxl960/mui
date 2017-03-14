<?php
/**
*配置文件
*/

//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
$config['partner']	= '2088521405241873';

//卖家支付宝账号
$config['seller_id']	= $config['partner'];

//商户的私钥,此处填写原始私钥去头去尾，RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1
$config['private_key']	= APPPATH.'third_party/alipay/key/rsa_private_key.pem'; 

//支付宝的公钥，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
$config['alipay_public_key'] = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';

//接口名称
$config['service']= 'mobile.securitypay.pay';

//签名方式 不需修改
$config['sign_type']    = strtoupper('RSA');

//字符编码格式 目前支持 gbk 或 utf-8
$config['input_charset']= strtolower('utf-8');

//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$config['cacert']    = APPPATH.'third_party/alipay/cacert.pem';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$config['transport']    = 'http';

// 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
$config['notify_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/payment/alipay_notify';

// 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
$config['return_url'] = '';

// 支付类型 ，无需修改
$config['payment_type'] = "1";