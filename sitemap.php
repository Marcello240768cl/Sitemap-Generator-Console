<?php
ini_set('memory_limit','-1');
#Copyright (C) <2023>  <Piermarcello Piazza>
#Funzione che restituisce i link della pagina dell' url di partenza sia https che http
#input:url della pagina di partenza e generico $link delle ancore dei links
# restituisce la mappa  di links del sito avente quell' url

function getOriginalURL($url) {//funzione che restituisce il folder di redirect se c'è un redirect altrimenti mantiene invariato url di origine
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = @curl_exec($ch);
    $httpStatus = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // if it's not a redirection (3XX), move along
    if ($httpStatus < 300 || $httpStatus >= 400)
        return $url;

    // look for a location: header to find the target URL
    if(@preg_match('/location: (.*)/i', $result, $r)) {
        $location = trim($r[1]);


        return @$location;
    }
    
}
#Copyright (C) <2023>  <Piermarcello Piazza>
#Funzione che restituisce i link della pagina dell' url di partenza sia https che http
#input:url della pagina di partenza e generico $link delle ancore dei links
# restituisce l' array di links 
function get_links($link,$url)
    {
global $start_url;
$ret = array();
     if((parse_url($link, PHP_URL_HOST))==(parse_url($start_url, PHP_URL_HOST))){
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
 $ret[$tag->getAttribute('href')]=$tag->getAttribute('href');
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
#Per ogni link dello stack array
   foreach($link as $url_)

  {

$schemeurl_=parse_url($url_, PHP_URL_SCHEME);
$hosturl_=parse_url($url_, PHP_URL_HOST);
//$ipurl_ = gethostbyname($hosturl_);
$pathurl_=parse_url($url_, PHP_URL_PATH);
$queryurl_=parse_url($url_, PHP_URL_QUERY);


//$ipurl_ = gethostbyname($url_);





#Se il generico link ha come host di riferimento lo stesso host dell' url di partenza
if(($hosturl_=='')&&($path!='')){$newurl=$scheme.'://'.parse_url($url, PHP_URL_HOST).'/'.$path.'?'.$queryurl_;
# e se e' diverso da quelli gia' esaminati ,va sempre più  in profondita nello stack array altrimenti si ferma
if(!in_array($newurl,$link)){
       array_push($link,$newurl);
$link=get_links($newurl,$url);
$arr_link=array_merge($arr_link,$link);
}
//else  get_urls_from($newurl);
}
#altrimenti se il link non ha un host uguale a quello dell' url di base(start_url)'
else if(($hosturl_=='')&&($pathurl_!='') )
{
//aggiungiamo il nome host dell' url di base e reiteriamo come sopra'
$newurl=$scheme.'://'.parse_url($url, PHP_URL_HOST).'/'.$pathurl_.'?'.$queryurl_;
if(!in_array($newurl,$link)){
       array_push($link,$newurl);
$link=get_links($newurl,$url);
$arr_link=array_merge($arr_link,$link);

}
//else  get_urls_from($newurl);
}




#Stessa operazione per tutti gli altri link dell
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

$new_url=parse_url($start_url, PHP_URL_SCHEME)."://".parse_url($start_url, PHP_URL_HOST)."/".parse_url($start_url, PHP_URL_PATH)."/".$fnew_url;
$new_Arr[$key] = $new_url;
}

   if((parse_url($fnew_url, PHP_URL_HOST)!="")&&(parse_url($start_url, PHP_URL_HOST)!=parse_url($fnew_url, PHP_URL_HOST)))
   #delete $fnew_url;
    #delete it if the linkname of map is different from hostname.
    unset( $new_Arr[$key]);
}
return $new_Arr;
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
    




# array $arr_links contenente link  delle pagine
    $arr_links = array( );
   $final_array=array();




$start_url =$_REQUEST['site_http_or_https'];
$scheme=parse_url($start_url, PHP_URL_SCHEME);
$path=parse_url($start_url, PHP_URL_PATH);
$host=parse_url($start_url, PHP_URL_HOST);
$query=parse_url($start_url, PHP_URL_QUERY);
$ip = gethostbyname($host);
$urlp=getOriginalURL($_REQUEST['site_http_or_https']);
$newpath=parse_url($urlp, PHP_URL_PATH);
$newstr= str_replace('index.php','',$newpath);

$start_url =$scheme.'://'.$host.'/'.$newstr;

$arr_links=get_urls_from($start_url);
$final_array=get_final_urls($arr_links);
foreach($final_array as $key=>$f_url)
{
  if((parse_url($f_url, PHP_URL_PATH)=="#")||(parse_url($f_url, PHP_URL_QUERY)=="#"))
 #delete $fnew_url;
    #delete it if the linkname of map is different from hostname.
  unset( $final_array[$key]);

}
//var_dump($arr_links);
$str_finale="";$str_dump="";//Variable string of output of function  dump_url

foreach($final_array as $link) {

   
$str_dump.=urlElement($link);  

}



$str_finale = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL; 

$str_f=$str_finale.PHP_EOL.$str_dump.PHP_EOL.'</urlset>';

echo $str_f ;

















?>






