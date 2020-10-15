<?php

namespace DQ\Traits;

trait Encoding
{
    /**
     * 支持对多维数组，对象，... 进行编码转换
     * 在不指定来源编码时，由系统自动检测编码类型
     *
     * @link   http://cn2.php.net/manual/zh/function.mb-detect-encoding.php
     * @link   http://cn2.php.net/manual/zh/function.mb-detect-order.php
     * @param  string|string[]|object  $content  需要转换的数据
     * @param  string  $to_encoding  目标编码类型
     * @param  string  $from_encoding  来源编码类型，默认自动检测类型
     * @param  mixed  $encoding_list  编码检测类型及顺序
     * @return string|string[]|object
     */
    protected function to_encoding($content, $to_encoding = "UTF-8", $from_encoding = null, $encoding_list = 'UTF-8,GBK,CP936,ISO-8859-1,ASCII')
    {
        if (is_string($content)) {
            if ($from_encoding === null) {
                $from_encoding = mb_detect_encoding($content, $encoding_list);
            }

            if (strtoupper($to_encoding) !== strtoupper($from_encoding)) {
                $content = mb_convert_encoding($content, $to_encoding, $from_encoding);
            }

        } elseif (is_array($content) || is_object($content)) {
            foreach ($content as $key => & $val) {
                $val = call_user_func_array(__FUNCTION__, [$val, $to_encoding, $from_encoding, $encoding_list]);
            }
        }

        return $content;
    }
}
