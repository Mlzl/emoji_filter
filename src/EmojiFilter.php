<?php
/**
 * @author ambi
 * @date 2019-09-24
 */

namespace Cleaner;


class EmojiFilter
{
    const SPIDER_URL = 'https://unicode.org/Public/emoji/12.0/emoji-data.txt';

    /**
     * 从SPIDER_URL爬取emoji库，并保存到CONFIG_PATH
     */
    public static function spiderEmojiUnicodeList()
    {
        $allLine = explode("\n", file_get_contents(self::SPIDER_URL));
        $list = [];
        foreach ($allLine as $line) {
            $line = trim($line);
            if ($line && strpos($line, '#') !== 0) {
                $items = explode(';', $line);
                $range = strtoupper(trim($items[0]));
                $range = explode('..', $range);
                if (count($range) == 1 || count($range) == 2) {
                    $list[] = array_map(function ($item) {
                        return hexdec($item);
                    }, $range);
                }
            }
        }
        $list = self::mergeEmojiList($list);
        $json = json_encode($list);
        file_put_contents(self::getConfigPath(), $json);
    }

    /**
     * 合并重复的配置
     * @param $list
     * @return array
     */
    public static function mergeEmojiList($list)
    {
        $aggList = self::duplicate($list);
        sort($aggList);
        $newList = [];
        for ($i = 0; $i < count($aggList); $i++) {
            $start = $aggList[$i];
            $next = $start + 1;
            $isFind = false;
            $item = [];
            for ($j = $i + 1; $j < count($aggList); $j++) {
                if ($next != $aggList[$j]) {
                    break;
                }
                $isFind = true;
                $next = $aggList[$j] + 1;
            }
            $item[] = $start;
            if ($isFind) {
                $item[] = $next - 1;
                $i = $j;
                $i--;
            }
            $newList[] = $item;
        }
        return $newList;
    }

    /**
     * 将字符转成Unicode
     * @param $c
     * @return bool|float|int
     */
    public static function unicodeOrd($c)
    {
        $ord0 = ord($c{0});
        if ($ord0 >= 0 && $ord0 <= 127) {
            return $ord0;
        }
        $ord1 = ord($c{1});
        if ($ord0 >= 192 && $ord0 <= 223) {
            return ($ord0 - 192) * 64 + ($ord1 - 128);
        }
        $ord2 = ord($c{2});
        if ($ord0 >= 224 && $ord0 <= 239) {
            return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
        }
        $ord3 = ord($c{3});
        if ($ord0 >= 240 && $ord0 <= 247) {
            return ($ord0 - 240) * 262144 + ($ord1 - 128) * 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
        }
        return false;
    }

    /**
     * 判断是否是emoji
     * @param $char
     * @return bool
     * @throws \Exception
     */
    public static function isEmoji($char)
    {
        //emoji小于4字节
        if (strlen($char) < 3) {
            return false;
        }
        $emojiList = self::getConfig();
        $unicode = self::unicodeOrd($char);
        foreach ($emojiList as $item) {
            $min = $item[0];
            $max = isset($item[1]) ? $item[1] : $item[0];
            if ($unicode >= $min && $unicode <= $max) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取emoji配置
     * @return false|mixed|string
     * @throws \Exception
     */
    public static function getConfig()
    {
        $config = file_get_contents(self::getConfigPath());
        $config = json_decode($config, true);
        if (!is_array($config)) {
            throw new \Exception('the config is empty');
        }
        return $config;
    }

    /**
     * 过滤emoji，并返回过滤后的emoji
     * @param $beforeStr
     * @return string
     * @throws \Exception
     */
    public static function filterEmoji($beforeStr)
    {
        $len = mb_strlen($beforeStr);
        $afterStr = "";
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($beforeStr, $i, 1);
            if (!self::isEmoji($char)) {
                $afterStr .= $char;
            }
        }
        return $afterStr;
    }

    public static function duplicate($list)
    {
        $c = [];
        foreach ($list as $range) {
            $f = $range[0];
            $s = isset($range[1]) ? $range[1] : $range[0];
            for (; $f <= $s; $f++) {
                $c[$f] = 1;
            }
        }
        return array_keys($c);
    }

    private static function getConfigPath()
    {
        return __DIR__ . '/config.json';
    }
}
