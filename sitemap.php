<?php
ini_set('memory_limit','-1');

#Copyright (C) <2025>  <Piermarcello Piazza>
#Funzione che restituisce i link della pagina dell' url di partenza sia https che http
#input:url della pagina di partenza e generico $link delle ancore dei links
# restituisce l' array di links 
function get_links($link,$url)
    {
global $start_url;
$ret = array();
$links=array();
    if(parse_url($link, PHP_URL_HOST)==parse_url($start_url, PHP_URL_HOST)){
        $dom = new DOMDocument();
      //  $html=@file_get_contents($link);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$html = curl_exec($ch);
curl_close($ch);
        if($html!=''){
        #Use Dom Object
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $tag){
$schemeurl_=parse_url($url, PHP_URL_SCHEME);
$hosturl_=parse_url($url, PHP_URL_HOST);

//$ipurl_ = gethostbyname($hosturl_);
$pathurl_=parse_url($url, PHP_URL_PATH);
$queryurl_=parse_url($url, PHP_URL_QUERY);
$scheme=parse_url($tag->getAttribute('href'), PHP_URL_SCHEME);
$path=parse_url($tag->getAttribute('href'), PHP_URL_PATH);
$host=parse_url($tag->getAttribute('href'), PHP_URL_HOST);
$query=parse_url($tag->getAttribute('href'), PHP_URL_QUERY);
if(($tag->getAttribute('href')=="#"))
 $ret[$tag->getAttribute('href')]=$url;
else if($host=="#")
$ret[$tag->getAttribute('href')]=$scheme.'//'.$hosturl_;

else if($path=="#")
$ret[$tag->getAttribute('href')]=$scheme.'//'.$host.'/'.$pathurl_;

else if($queryurl_=="#")
$ret[$tag->getAttribute('href')]=$scheme.'//'.$host.'/'.'/'.$path.'/'.$queryurl_;

 else if(!in_array($tag->getAttribute('href'),$ret)) $ret[$tag->getAttribute('href')]=$tag->getAttribute('href');
}
  $dom->loadHTML($html);

// Clear the errors
libxml_clear_errors();

// Get the form element
$form = $dom->getElementsByTagName('form')->item(0);

// Check if the form element exists
if ($form) {
    // Get the action attribute
    $action = $form->getAttribute('action');
   // echo "Form action: " . $action . "\n";

    // Get the method attribute
   // $method = $form->getAttribute('method');
  //  echo "Form method: " . $method . "\n";

    // Get all input elements within the form
    $inputs = $form->getElementsByTagName('action');
    foreach ($inputs as $input) {
        $type = $input->getAttribute('type');
        $name = $input->getAttribute('name');
        $value = $input->getAttribute('value');
      //  echo "Input type: $type, name: $name, value: $value\n";
$ret[$input->getAttribute('value')]=$input->getAttribute('value');
    }

}       
     
}
 
}
      return $ret;
       
    
}

#Funzione che restituisce i link della pagina dell' url di partenza
#input:url della pagina di partenza e generico $link delle ancore dei links
# restituisce l' array di links per ogni pagina associata al link sempre in profondita sino a quando trova l' eccezione di get_links, cioe'
# un link con un nome host diverso da quello di partenza

function get_urls_from($url)

{
global $host,$base,$scheme,$path;$newurl;  // vriabili globali che vengono richiamate nel programma 
$link=array();//definizione dell' array contenente i link
$arr_link=array();//definizione dell' array contenente i link dei link

$link=get_links($url,$url);
$get_url='';

array_push($link,$url);
$arr_link=$link;
   foreach($link as $url_)

  {

$schemeurl_=parse_url($url_, PHP_URL_SCHEME);
$hosturl_=parse_url($url_, PHP_URL_HOST);
//$ipurl_ = gethostbyname($hosturl_);
$pathurl_=parse_url($url_, PHP_URL_PATH);
$queryurl_=parse_url($url_, PHP_URL_QUERY);


//$ipurl_ = gethostbyname($url_);





#Se il generico link ha come host di riferimento lo stesso host dell' url di partenza, va sempre più  in profondita altrimenti si ferma
if(($hosturl_=='')&&($path!='')){$newurl=$scheme.'://'.parse_url($url, PHP_URL_HOST).$path.'?'.$queryurl_;
if(!in_array($newurl,$link)){
       array_push($link,$newurl);
$link=get_links($newurl,$url);
$arr_link=array_merge($arr_link,$link);

}
//else  get_urls_from($newurl);
}
else if(($hosturl_=='')&&($pathurl_!='') )
{$newurl=$scheme.'://'.parse_url($url, PHP_URL_HOST).$pathurl_.'?'.$queryurl_;
if(!in_array($newurl,$link)){
       array_push($link,$newurl);
$link=get_links($newurl,$url);
$arr_link=array_merge($arr_link,$link);

}
//else  get_urls_from($newurl);
}





 if(!in_array($url_,$link)){
       array_push($link,$url_);
$link=get_links($url_,$url);
$arr_link=array_merge($arr_link,$link);

}
//else 
// get_urls_from($url_);


  }

   








return $arr_link;

}
#function to realize the links in appropriated form 
# so that :if in the link is missed the suffix http(or https)://hostname/pathhostname/ in the link
# this function complete the full url or, delete it if the linkname of map is different from hostname.
function get_final_urls($new_Arr)
{
global $start_url;
foreach($new_Arr as $key=>$fnew_url)
{
#if in the link is missed the suffix http(or https)://hostname/pathhostname/ in the link
# this function complete the full url
if(((parse_url($fnew_url, PHP_URL_HOST)=="")||(parse_url($fnew_url, PHP_URL_HOST)=="#"))&&(parse_url($start_url, PHP_URL_HOST)!=parse_url($fnew_url, PHP_URL_HOST)))
{
#modify $fnew_url

$new_url=parse_url($start_url, PHP_URL_SCHEME)."://".parse_url($start_url, PHP_URL_HOST).parse_url($start_url, PHP_URL_PATH).$fnew_url;
$new_Arr[$key] = $new_url;
}

   if((parse_url($fnew_url, PHP_URL_HOST)!="")&&(parse_url($start_url, PHP_URL_HOST)!=parse_url($fnew_url, PHP_URL_HOST)))
   #delete $fnew_url;
    #delete it if the linkname of map is different from hostname.
    unset( $new_Arr[$key]);
}
$amp=Array();
$nsbp=Array();
foreach($new_Arr as $key=>$fnew_url){


$string_n=$fnew_url;
$amp=explode("&",$string_n);
$amp_=implode("&amp;",$amp);

//echo $amp_;
$nsbp=explode(" ",$amp_);

$nsbp_=implode("&nsbp;",$nsbp);
//echo $nsbp_;
$new_Arr[$key]=$nsbp_;
}
return $new_Arr;
}

# array $urls conte

# array $urls contenente link  delle pagine
    $arr_links = array( );



#function of output printing of file sitemap.xml
    function urlElement($url) {
        $aptr= '<url>'.PHP_EOL. 
         '<loc>'.$url.'</loc>'. PHP_EOL. 
        '<lastmod>'.date('c',time()).'</lastmod>'.PHP_EOL. 
         '</url>'.PHP_EOL;
return $aptr;
    }
    




# array $arr_links contenente link  delle pagine
    $arr_links = array( );
   $final_array=array();




$start_url =$_REQUEST['site_http_or_https'];
$scheme=parse_url($start_url, PHP_URL_SCHEME);
$path=parse_url($start_url, PHP_URL_PATH);
$host=parse_url($start_url, PHP_URL_HOST);
$query=parse_url($start_url, PHP_URL_QUERY);
$ip = gethostbyname($host);
$urlp=getOriginalURL($start_url);
$newpath=parse_url($urlp, PHP_URL_PATH);
$newstr= str_replace('index.php','',$newpath);

$start_url =$scheme.'://'.$host.'/'.$newstr;

$arr_links=get_urls_from($start_url);
$final_array=get_final_urls($arr_links);
/*foreach($final_array as $key=>$f_url)
{
  if((parse_url($f_url, PHP_URL_PATH)=="#")||(parse_url($f_url, PHP_URL_QUERY)=="#"))
 #delete $fnew_url;
    #delete it if the linkname of map is different from hostname.
  unset( $final_array[$key]);

}*/
//var_dump($arr_links);
$str_finale="";$str_dump="";//Variable string of output of function  dump_url

foreach($final_array as $link) {

   
$str_dump.=urlElement($link);  

}



$str_finale = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'.PHP_EOL; 

$str_f=$str_finale.PHP_EOL.$str_dump.PHP_EOL.'</urlset>';//echo $str_f;
//$str_finale=$str_finale.'</urlset>';echo $str_dump ;
#put html content into $url file xml from folder 

//$fp=file_put_contents("sitemap.xml",$str_finale);












$name_file=str_shuffle('0123456789');
//echo "<h1>your sitemap file .xml:".$name_file.".xml"."</h1>";
$fp=file_put_contents($name_file.".xml",$str_f );


header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=".$name_file.".xml");
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: '. filesize($name_file.".xml")."'" ); //Absolute URL //
ob_clean();
flush();

readfile($name_file.".xml"); //Absolute URL




//exit();









?>







