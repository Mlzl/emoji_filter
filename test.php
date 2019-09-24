<?php
/**
 * @author ambi
 * @date 2019-09-24
 */

require "src/EmojiFilter.php";

//\Cleaner\EmojiFilter::spiderEmojiUnicodeList();

//var_dump(\Cleaner\EmojiFilter::getConfig());


$str = '8™1🌘↗2🔡🕙⌚3😭4🔶🙌5✅6➖7🚀🚒🚫🚾🆒🈂🈹‼⏪▶♈♣⚪🍮🍺🐱📌📟📫鸶钰';
echo \Cleaner\EmojiFilter::filterEmoji($str);