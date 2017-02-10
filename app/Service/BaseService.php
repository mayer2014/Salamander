<?php
/**
 * User: salamander
 * Date: 2016/10/26
 * Time: 9:51
 */

namespace App\Service;


class BaseService
{
    // 用户未登录状态码
    const USER_NOT_LOGIN = 555;
    // 查询超过每天次数状态码
    const QUERY_EXCEED_LIMIT = 203;


    protected $db;

    public function __construct()
    {
        $this->db = singleton('db');
    }

    /**
     * 根据域名名字部分或者后缀进行不同的模糊查询
     * @param string $type
     * @param string $ext
     * @return string
     */
    protected static function solveDomainType($type, $ext) {
        // 后缀查询
        if(empty($type)) {
            return "^[a-z0-9]+{$ext}$";
        }
        if($ext) {
            $ext = str_replace('.', '\.', $ext);
        }

        // mysql 正则不支持\W \D
        // 业务逻辑部分，分判断是否为四声，四字母（去除四声），
        // 四数字，四数字（无0,4），五数字，五数字（无0,4），六数字，六数字（无0,4）
        // 三字母，2字母，2数，3数，三杂，2杂
        // ZF 表示没有zero four
        $len = 0;
        $regexp = '';
        if(strpos($type, 'two') === 0) {
            $len = 2;
        }
        if(strpos($type, 'three') === 0) {
            $len = 3;
        }
        if(strpos($type, 'four') === 0) {
            $len = 4;
        }
        if(strpos($type, 'five') === 0) {
            $len = 5;
        }
        if(strpos($type, 'six') === 0) {
            $len = 6;
        }
        $P = '[aeiouv]'; // positive
        $N = '[b-df-hj-np-tw-z]'; // negative
        // 后缀为空
        if(empty($ext)) {
            $ext = '\.[a-z]+';
        }
        switch($type) {
            case 'twoWord':
                $regexp = "^[a-z]{{$len}}{$ext}$";
                break;
            // 三字母除去三声
            case 'threeWord':
                $regexp = "^({$P}{$N}{2}|{$N}{$P}{$N}|{$N}{2}{$P}|{$P}{2}{$N}|{$P}{$N}{$P}|{$N}{$P}{2}|{$P}{3}){$ext}$";
                break;
            // 四字母除去四声
            case 'fourWord':
                $regexp = "^({$P}{4}|{$P}{$N}{3}|{$N}{$P}{$N}{2}|{$N}{2}{$P}{$N}|{$N}{3}{$P}|{$N}{2}{$P}{2}|{$P}{$N}{$P}{$N}|{$P}{$N}{2}{$P}|{$P}{$N}{2}{$P}|{$N}{$P}{2}{$N}|{$N}{$P}{$N}{$P}|{$N}{2}{$P}{2}|{$P}{$N}{3}|{$P}{$N}{$P}{2}|{$P}{2}{$N}{$P}|{$P}{3}{$N}){$ext}$";
                break;
            case 'threeVoice':
            case 'fourVoice':
                $regexp = "^[b-df-hj-np-tw-z]{{$len}}{$ext}$";
                break;
            case 'twoNum':
            case 'threeNum':
            case 'fourNum':
            case 'fiveNum':
            case 'sixNum':
                $regexp = "^[0-9]{{$len}}{$ext}$";
                break;
            case 'fourNumZF':
            case 'fiveNumZF':
            case 'sixNumZF':
                $regexp = "^[1-35-9]{{$len}}{$ext}$";
                break;
            case 'twoMix':
                $regexp = "^([0-9][a-z]|[a-z][0-9]){$ext}$";
                break;
            case 'threeMix':
                $W = '[a-z]';
                $N = '[0-9]';
                $regexp = "^({$W}{$W}{$N}|{$W}{$N}{$W}|{$W}{$N}{$N}|{$N}{$W}{$W}|{$N}{$W}{$N}|{$N}{$N}{$W}){$ext}$";
                break;
        }
        return $regexp;
    }

    /**
     * 判断字符串是否为合法域名
     * @param $str
     * @return boolean
     */
    protected function isLegalDomain($str) {
        return preg_match('/^[a-z0-9]+\.[a-z\.]+$/', $str);
    }

    /**
     * 获取域名的类型，四声，三字母，三数，四数，随便截取域名后缀
     * @param $domain
     * @param $ext
     * @return string
     */
    protected function getDoaminKind($domain, &$ext) {
        $index = strpos($domain, '.');
        if($index !== false) {
            $name = substr($domain, 0, $index);
            $ext = substr($domain, $index);
            $len = strlen($name);
            // 纯数字，纯字母，杂合
            if(ctype_digit($name)) {
                if((strpos($name, '0') !== false || strpos($name, '4') !== false) && $len >= 4) {
                    return $this->lengthToWord($len) . 'NumZF';
                } else {
                    return $this->lengthToWord($len) . 'Num';
                }
            } else if(ctype_alpha($name)) {
                if($len >= 4 && !$this->hasExcludeWord($name) ) {
                    return $this->lengthToWord($len) . 'Voice';
                } else {
                    return $this->lengthToWord($len) . 'Word';
                }
            } else {
                return $this->lengthToWord($len) . 'Mix';
            }
        }
        return '';
    }


    /**
     * 长度对应的单词
     * @param int $len 长度
     * @return string
     */
    private function lengthToWord($len) {
        $wordsArr = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'];
        return $wordsArr[$len];
    }

    /**
     * 是否含有四声不需要的字母
     * @param $name
     * @return bool
     */
    private function hasExcludeWord($name) {
        // 四声排除的字母
        $excludeWords = ['a', 'e', 'i', 'o', 'u', 'v'];
        foreach ($excludeWords as $word) {
            if(strpos($name, $word) !== false)
                return true;
        }
        return false;
    }

    /**
     * 获取域名名字部分（不带后缀）
     * @param $domain
     * @return string
     */
    protected function getDomainName($domain) {
        if(empty($domain))
            return '';
        $index = strpos($domain, '.');
        if( $index !== false) {
            return substr($domain, 0, $index);
        }
        return $domain;
    }


}