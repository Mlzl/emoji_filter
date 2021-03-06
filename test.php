<?php
/**
 * @author ambi
 * @date 2019-09-24
 */

require "src/EmojiFilter.php";

//爬取emoji资料库
//\Cleaner\EmojiFilter::spiderEmojiUnicodeList();

//过滤emoji表情
$str = '8™1🌘↗🇬🇧2🔡🕙⌚3😭4🔶🙌5✅6➖7🚀🚒🚫🚾🆒🈂🈹‼⏪▶♈♣⚪🍮🍺🐱📌📟📫鸶钰🇫🇯🖊️';
echo \Cleaner\EmojiFilter::filterEmoji($str);
//expect:81234567鸶钰

//$config = \Cleaner\EmojiFilter::getConfig();
//var_dump(count($config));
//var_dump(count(\Cleaner\EmojiFilter::duplicate($config)));
//$config = \Cleaner\EmojiFilter::mergeEmojiList($config);
//var_dump(count(\Cleaner\EmojiFilter::duplicate($config)));
//var_dump(count($config));