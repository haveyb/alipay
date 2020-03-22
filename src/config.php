<?php
declare(strict_types = 1);
namespace haveyb\AliPay;

// 应用私钥
define('APP_PRIVATE_KEY', 'MIIEpgIBAAKCAQEArYOVd+sUkcfFVgHrPOqOagMKZON+eladnscZn2zuIePSPZ0Zfqa8i9iOD+zUxbJWfpi9rx0m6THIfDidwdTezIcV+f0wBj2VH0uRwsqZ3F1mtoTgo8+UuR2J3RflhvJQK/v2r3vHDMyqUhPVdaLecWnpmwV/DQzQbz2dph9mUQ6ssCCUERwCnOW15xDDGjbwVHYQ5y5PAo2sCQdUOvp6CnXfQN1orxMljAH9wURtyzLsIj2NmnTL89k+gWzKbZfq5mlTf0Eg9NZO8roCMB0c8W+H2ylcqpyhHvYAQEHDYgBR26V9XrriDcCsSsGMfULyTu+PhopItfV9TxRF0vxCWQIDAQABAoIBAQCDrq4b0TvOGydnS5OEqpokWGRmBsSxAdUjcATBGkFrVOP0wKpdlRrYoyPFcD5WPy8narHiGSSzq4V0yN7pAK2J2SdTjtQImGn40zgu9eVo+TV/ZT6680nyZzl+oqkeDf3MM6+WpEB/NUA5hDXmzhE1T2TrsnMmq0fwdHmVXpUfA9y6v6aV+v3fU3wUULF6TwTwj37VMkuG2azOfaIzYyKenk2SoUnQX18wwkINiRavGqhADWoHzHVrt77bib3DXPHTGlIviu0ix/napN9sQYBEiUDebLLdgUVZLDlXY0dMa5FmBdx3yw9ActSN715HXQc6FBDazga4snh/AaHRSD5VAoGBAN2b/BJZT6KcK8LskCB11zAQ2xAc3uyi+/U8p5vVlB1UKiQJupG0++MmcvWY4yWF+uMSuA71KdyArNrHZxqi5XRI+c9eJAsxM0M6b9R679nHSi1+mNGLSXF4Olprndok8qHsmU1yXTHiJZdleszjhayKUh536gvp7baklFczLfkfAoGBAMhw4sGa93Od3/6E8HjVF/qdDJDrkzF4YIZ2GK1EnHvNO0kRrnpCYBpeBBUai0AbK7IvRoZlZPkoB8knRqwN8fTokmQmun6V+1XdjQNQ1cJpD6FXZN/KabiFHqI2199WSk4lidB/kx/2UARFkseoN2t65vpsQxk2KCpjWzF27r2HAoGBANOoYc8coiTLSDi6PMXkBw0PbEstZ/VXjZIixmreSMLiWgVljV4RH+tKE85iocB7AK7UeBtBKSRDedTTUrRsFP9Fm+LlJUPTUATL0Xd9m4vC/iQJn+ezQwgvLnyiAHgThfIFyj3gFwbH2eOJtwnt3QR4cApNuap7WOFyu4O1jBk9AoGBAJGHnKvS9v0j0EE8hlhE568XHcuOrwVJEFQwJISQ/0jH3taTlum/jYU3Y6Fq71WxJI2v03W64pAgZ16+PIqpaVZXEgrTL66++IgEXuDcbQdFPor3KA9wKEhHptFCHoRSY4rPqHsQVWLsdHZViVebI/nOdyu1NRZ2Ar2b+9czMe27AoGBAI9tBZKM75MFgNhkFuvx6I/O/9Vw1tXo7SUcOctC2OCx/1SOju+PkcK8Yduay8u0UjpYiGvCehT/HkX7y+OeBjykgv1OktNZRTO0WJiUy2yk8JEy55Jsfbk4XrJFgvfJsOrGIWK/i6wlLRl3i9amfbsrqYifuKsq/X/PBxQQs5lT');

// 支付宝公钥(rsa方式)
define('ALI_RSA_PUBLIC_KEY', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4QuEL5/5PwF9ppj0xY9TVpPC9xldf6keB/UjDb+EdHVMk5oMDlgNYUljktujpr09M+GCl+QUlKWfUygQ/k4Hq0cGUxKeynL9dqnCncuONeNQIzDLjkaVA3lIo8o+kxhVv+GnGOVCvv6JetLDC8eFb0GWfvm0aKtuJ0cm1m1+NGbiisGkIEP8guncs0tKltVcIrwP/YAhScDH5qekUsalxZVjIPKm1nK2x2csCqUs44fMgPyzG8nv6Tm8ffV51qphhXDrn6EsN5j0ATRjBLjsE1di1EH/p6JJ5XLG68OZHwDIktRHMxwShFSIyfYztb4sVBAajbxrnzYGxiLZ9dhA7QIDAQAB');

// 支付宝公钥（rsa2方式）
define('ALI_RSA2_PUBLIC_KEY', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4QuEL5/5PwF9ppj0xY9TVpPC9xldf6keB/UjDb+EdHVMk5oMDlgNYUljktujpr09M+GCl+QUlKWfUygQ/k4Hq0cGUxKeynL9dqnCncuONeNQIzDLjkaVA3lIo8o+kxhVv+GnGOVCvv6JetLDC8eFb0GWfvm0aKtuJ0cm1m1+NGbiisGkIEP8guncs0tKltVcIrwP/YAhScDH5qekUsalxZVjIPKm1nK2x2csCqUs44fMgPyzG8nv6Tm8ffV51qphhXDrn6EsN5j0ATRjBLjsE1di1EH/p6JJ5XLG68OZHwDIktRHMxwShFSIyfYztb4sVBAajbxrnzYGxiLZ9dhA7QIDAQAB');

// 支付宝 app_id
define('ALI_PAY_APP_ID', '2016091200493156');

// 支付宝 pid
define('ALI_PID', '2016091200493156');

// 同步通知地址（根据自己的修改）
define('RETURN_URL', 'http://swoole.haveyb.com/alipay/return.php');

// 异步通知地址
define('NOTIFY_URL', 'http://swoole.haveyb.comalipay/notify.php');

// 设置是否是测试环境
define('IS_DEV', true);

// md5和rsa方式的支付网关
define('PAY_GATEWAY', 'https://mapi.alipay.com/gateway.do');

// rs2 方式下的支付网关
define('RSA2_PAY_GATEWAY',
    IS_DEV ? 'https://openapi.alipaydev.com/gateway.do' : 'https://openapi.alipay.com/gateway.do');

// md5方式下的key
define('ALI_MD5_KEY', '');







