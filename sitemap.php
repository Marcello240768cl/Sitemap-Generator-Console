<?php

# The function get_links($link) that return array of links anchor of Dom object of html page of url link
# was copied from Documento https://stackoverflow.com/questions/52257829/getting-link-tag-via-domdocument
#While the function get_urls_from($url) that uses Dom Objects to return the list links of an url of site web
#and help to get sitemap xml file with the code of this file :sitemap.php
# is entirely written by the author :Piermarcello Piazza 
#The code is used by https://trenetcl.altervista.org to download sitemap.xml on the fly
require 'relpath.php';
function get_links($link)
    {
$ret = array();
    
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
      else return;
     
        return $ret;
    }




function get_urls_from($url)
{#begin function
$link=array();
$arr_link=array();
#processes the prefix,host,and validity of ip address of generic url
$scheme=parse_url($url, PHP_URL_SCHEME);
$host=parse_url($url, PHP_URL_HOST);
$ip = gethostbyname($host);
#begin if clausola of url (if an url can be taken in consideration to push into quee stack to recursively function get_urls_from($url)
if((($scheme=='http')||($scheme=='https')||($scheme=='ftp')||(($scheme=='mailto'))&&(filter_var($ip, FILTER_VALIDATE_IP)==true)))
{
$link=get_links($url);#call the function get_links to return array containing the processed links of an url
$get_url='';
#push into quee stack

array_push($arr_link,$url);
   foreach($link as $url_)
   {

$schemeurl_=parse_url($url_, PHP_URL_SCHEME);
$hosturl_=parse_url($url_, PHP_URL_HOST);
//$ipurl_ = gethostbyname($hosturl_);
$pathurl_=parse_url($url_, PHP_URL_PATH);
$queryurl_=parse_url($url_, PHP_URL_QUERY);

     if($pathurl_!='')//if path of link child url_ exists
    {
     /*#but is empty url of the same page 
     if($schemeurl_=='') $get_url=$scheme.'://';
     if($hosturl_!='') $get_url=$get_url.$hosturl_;
     if($pathurl_!='')  $get_url=$get_url.$host.'/'.$url_;*/
     #if the link  stack insert into stack not match any other link 
    // if(!in_array($url_,$arr_link))
$get_url=rel2abs($url_, $url); 
       array_push($arr_link,$get_url);

////********Apoteosi apocalittica************//////

     }//end if path of link child url_
#else pop the last link and run recursively the function get_urls_from($url)
else 
     {
     
      $newurl=array_pop($arr_link);
return get_urls_from($newurl);
     }
   }
 
}




return $arr_link;

}


//////************fine apocalisse*************/////////
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
$start_url =$argv[1];//$_REQUEST['url_site'];
 $newurl='';
$arr_links=get_urls_from($start_url);
var_dump($arr_links);
$str_finale="";$str_dump="";//Variable string of output of function  dump_url

foreach($arr_links as $link) {

    //  $urls[$n]=$link->getAttribute('href');
$str_dump.=urlElement($link);  

}



$str_finale= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 
  $str_finale= $str_finale.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL ; 
$str_dump=$str_finale.$str_dump;
$str_dump =$str_dump.'</urlset>';echo $str_dump;
#put html content into $url file xml from folder 
unlink("sitemap.xml");
$fp=file_put_contents("sitemap.xml",$str_dump);



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



