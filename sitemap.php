<?php



function get_links($link,$url)
    {
$ret = array();
      if((parse_url($link, PHP_URL_HOST))==(parse_url($url, PHP_URL_HOST))){
        $dom = new DOMDocument();
        $html=file_get_contents($link);
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




function get_urls_from($url)

{
global $host,$base,$scheme,$path;
$link=array();
$arr_link=array();


#call the function get_links to return array containing the processed links of an url
$link=get_links($url,$url);
$get_url='';
#push into quee stack

array_push($link,$url);
$arr_link=$link;
   foreach($link as $url_)

  {

$schemeurl_=parse_url($url_, PHP_URL_SCHEME);
$hosturl_=parse_url($url_, PHP_URL_HOST);
$ipurl_ = gethostbyname($hosturl_);
$pathurl_=parse_url($url_, PHP_URL_PATH);
$queryurl_=parse_url($url_, PHP_URL_QUERY);


$ipurl_ = gethostbyname($url_);




if($schemeurl_=='') $schemeurl_=$scheme;
if($pathurl_=='') $pathurl_='/';

if($hosturl_=='') $hosturl_=$host;
 $newurl=$schemeurl_.'://'.$hosturl_.'/'.$pathurl_.'?'.$queryurl_;

 if(!in_array($newurl,$link)){
       array_push($link,$newurl);
$link=get_links($newurl,$url);
$arr_link=array_merge($arr_link,$link);


}


  

   }








return $arr_link;

}



# array $urls containing link  of pages
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
$start_url =$_REQUEST['url_site'];
 $newurl='';
$scheme=parse_url($start_url, PHP_URL_SCHEME);
$path=parse_url($start_url, PHP_URL_PATH);
$host=parse_url($start_url, PHP_URL_HOST);
$query=parse_url($start_url, PHP_URL_QUERY);
$ip = gethostbyname($host);
#begin if clausola of url (if an url can be taken in consideration to push into quee stack to recursively function get_urls_from($url)

if((filter_var($ip, FILTER_VALIDATE_IP)==true))
{
$arr_links=get_urls_from($start_url);
//var_dump($arr_links);
$str_finale="";$str_dump="";//Variable string of output of function  dump_url

foreach($arr_links as $link) {

   
$str_dump.=urlElement($link);  

}



$str_finale= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 
 $str_finale= $str_finale.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL ; 
$str_dump=$str_finale.$str_dump;
$str_dump =$str_dump.'</urlset>';
#put html content into $url file xml from folder 
//unlink("sitemap.xml");
$fp=file_put_contents("sitemap.xml",$str_dump);}



header("Content-Type: application/force-download; name=sitemap.xml");
header("Content-type: text/xml"); 
header("Content-Transfer-Encoding: binary");
header("Content-Disposition: attachment; filename=sitemap.xml");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
readfile("sitemap.xml");

        
         
#Destroy file after downloading it
//unlink("sitemap.xml");

#Exit page 
exit();

?>



