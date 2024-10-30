<?php
/*
Plugin Name: Jay RSS Show 
Plugin URI: http://www.jaylee.cn/jay_rss_show
Description: List a number of recent posts from the RSS you choose.
Version: 1.2
Author: jayleecn
Author URI: http://www.jaylee.cn
*/

//$sourcestr ��Ҫ������ַ��� 
//$cutlength Ϊ��ȡ�ĳ���(������) 
function cut_str($sourcestr,$cutlength) 
{ 
   $returnstr=''; 
   $i=0; 
   $n=0; 
   $str_length=strlen($sourcestr);//�ַ������ֽ��� 
   while (($n<$cutlength) and ($i<=$str_length)) 
   { 
      $temp_str=substr($sourcestr,$i,1); 
      $ascnum=Ord($temp_str);//�õ��ַ����е�$iλ�ַ���ASCII�� 
      if ($ascnum>=224)    //���ASCIIλ����224��
      { 
         $returnstr=$returnstr.substr($sourcestr,$i,3); //����UTF-8����淶����3���������ַ���Ϊ�����ַ�         
         $i=$i+3;            //ʵ��Byte��Ϊ3
         $n++;            //�ִ����ȼ�1
      }
      elseif ($ascnum>=192) //���ASCIIλ����192��
      { 
         $returnstr=$returnstr.substr($sourcestr,$i,2); //����UTF-8����淶����2���������ַ���Ϊ�����ַ� 
         $i=$i+2;            //ʵ��Byte��Ϊ2
         $n++;            //�ִ����ȼ�1
      }
      elseif ($ascnum>=65 && $ascnum<=90) //����Ǵ�д��ĸ��
      { 
         $returnstr=$returnstr.substr($sourcestr,$i,1); 
         $i=$i+1;            //ʵ�ʵ�Byte���Լ�1��
         $n++;            //�������������ۣ���д��ĸ�Ƴ�һ����λ�ַ�
      }
      else                //��������£�����Сд��ĸ�Ͱ�Ǳ����ţ�
      { 
         $returnstr=$returnstr.substr($sourcestr,$i,1); 
         $i=$i+1;            //ʵ�ʵ�Byte����1��
         $n=$n+0.5;        //Сд��ĸ�Ͱ�Ǳ����ڰ����λ�ַ���...
      } 
   } 
         if ($str_length/3>$cutlength){
          $returnstr = $returnstr . "...";//��������ʱ��β������ʡ�Ժ�
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