<?php


#Funzione che restituisce i link della pagina dell' url di partenza
#input:url della pagina di partenza e generico $link delle ancore dei links
# restituisce l' array di links 
function get_links($link,$url)
    {
$ret = array();
      if((parse_url($link, PHP_URL_HOST))==(parse_url($url, PHP_URL_HOST))){
        $dom = new DOMDocument();
        $html=@file_get_contents($link);
        if($html!=''){
        #Use Dom Object
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $tag){
            $ret[$tag->getAttribute('href')] =$tag->getAttribute('href');
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
if(($hosturl_=='')&&($path!='')){$newurl=$scheme.'://'.parse_url($url, PHP_URL_HOST).'/'.$path.'?'.$queryurl_;
if(!in_array($newurl,$link)){
       array_push($link,$newurl);
$link=get_links($newurl,$url);
$arr_link=array_merge($arr_link,$link);
}
else  get_urls_from($url_);
}
else if(($hosturl_=='')&&($pathurl_!='') )
{$newurl=$scheme.'://'.parse_url($url, PHP_URL_HOST).'/'.$pathurl_.'?'.$queryurl_;
if(!in_array($newurl,$link)){
       array_push($link,$newurl);
$link=get_links($newurl,$url);
$arr_link=array_merge($arr_link,$link);

}
else  get_urls_from($url_);
}





 if(!in_array($url_,$link)){
       array_push($link,$url_);
$link=get_links($url_,$url);
$arr_link=array_merge($arr_link,$link);
}
else  get_urls_from($url_);


  

   }








return $arr_link;

}



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
    


#url site from post form of file input.html
$start_url =$argv[1];
$newurl='';
$scheme=parse_url($start_url, PHP_URL_SCHEME);
$path=parse_url($start_url, PHP_URL_PATH);
$host=parse_url($start_url, PHP_URL_HOST);
$query=parse_url($start_url, PHP_URL_QUERY);
$ip = gethostbyname($host);

if((filter_var($ip, FILTER_VALIDATE_IP)==true))
{
$arr_links=get_urls_from($start_url);
//var_dump($arr_links);
$str_finale="";$str_dump="";//Variable string of output of function  dump_url

foreach($arr_links as $link) {

   
$str_dump.=urlElement($link);  

}



$str_finale= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 
 $str_finale= $str_finale.'<urlset>'.PHP_EOL ; 
$str_dump=$str_finale.$str_dump;
$str_dump =$str_dump.'</urlset>';echo $str_dump ;
#put html content into $url file xml from folder 

$fp=file_put_contents("sitemap.xml",$str_dump);
}





?>



