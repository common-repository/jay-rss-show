<?php
/*
Plugin Name: Jay RSS Show 
Plugin URI: http://www.jaylee.cn/jay_rss_show
Description: List a number of recent posts from the RSS you choose.
Version: 1.2
Author: jayleecn
Author URI: http://www.jaylee.cn
*/

//$sourcestr 是要处理的字符串 
//$cutlength 为截取的长度(即字数) 
function cut_str($sourcestr,$cutlength) 
{ 
   $returnstr=''; 
   $i=0; 
   $n=0; 
   $str_length=strlen($sourcestr);//字符串的字节数 
   while (($n<$cutlength) and ($i<=$str_length)) 
   { 
      $temp_str=substr($sourcestr,$i,1); 
      $ascnum=Ord($temp_str);//得到字符串中第$i位字符的ASCII码 
      if ($ascnum>=224)    //如果ASCII位高与224，
      { 
         $returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符         
         $i=$i+3;            //实际Byte计为3
         $n++;            //字串长度计1
      }
      elseif ($ascnum>=192) //如果ASCII位高与192，
      { 
         $returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符 
         $i=$i+2;            //实际Byte计为2
         $n++;            //字串长度计1
      }
      elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
      { 
         $returnstr=$returnstr.substr($sourcestr,$i,1); 
         $i=$i+1;            //实际的Byte数仍计1个
         $n++;            //但考虑整体美观，大写字母计成一个高位字符
      }
      else                //其他情况下，包括小写字母和半角标点符号，
      { 
         $returnstr=$returnstr.substr($sourcestr,$i,1); 
         $i=$i+1;            //实际的Byte数计1个
         $n=$n+0.5;        //小写字母和半角标点等于半个高位字符宽...
      } 
   } 
         if ($str_length/3>$cutlength){
          $returnstr = $returnstr . "...";//超过长度时在尾处加上省略号
      }
    return $returnstr; 
}


function jay_rss_show ($url='http://feed.jaylee.cn', $num=5, $length=23) {
	//ini_set("display_errors", false); uncomment to suppress php errors thrown if the feed is not returned.
	if ( file_exists(ABSPATH . WPINC . '/rss.php') )
		require_once(ABSPATH . WPINC . '/rss.php');
	else
		require_once(ABSPATH . WPINC . '/rss-functions.php');
	
	if($_GET['url'])
	{
		$url = $_GET['url'];
	}
	if($_GET['num'])
	{
		$num = $_GET['num'];
	}
	if($_GET['length'])
	{
		$length = $_GET['length'];
	}
	$rss = fetch_rss($url);
	$num_items = $num;
	$length_title=$length;
	
if ( $rss ) {
			echo "<ul>";
			$rss->items = array_slice($rss->items, 0, $num_items);
				foreach ($rss->items as $item ) {
	$item_description = strip_tags($item['description']); 
	$item_description = stripslashes($item_description); 
	$item_description = substr($item_description,0,200);
    $item_description = utf8_trim($item_description);
	$item_description = str_replace('"', '', $item_description);
	$item_title= wp_specialchars($item['title']);
	$item_title= cut_str($item_title,$length_title) ;
					echo "<li>\n";
					echo "<a target='_blank' href='$item[link]' title='$item_description";
					echo "...'>";
					echo $item_title;
					echo "</a>\n";
					echo "</li>\n";
				}		
			echo "</ul>";
	}
		else {
			echo "an error has occured, you can ask <a href=http://www.jaylee.cn/jay_rss_show>the author</a> for help";
	}
}
?>