<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
require_once("simple_html_dom.php");
$comma_count;
class CurlWithPost
{
    public function curl_with_post($user_search)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.indianrail.gov.in/cgi_bin/inet_trnnum_cgi.cgi");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "lccp_trnname=$user_search&getIt=Please+Wait...");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
        $headers   = array();
        $headers[] = "Host: www.indianrail.gov.in";
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:55.0) Gecko/20100101 Firefox/55.0";
        $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $headers[] = "Accept-Language: en-US,en;q=0.5";
        $headers[] = "Referer: http://www.indianrail.gov.in/train_Schedule.html";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Cookie: _ga=GA1.3.109252826.1501758539; _gid=GA1.3.1030470029.1501758539; _gat=1";
        $headers[] = "Connection: keep-alive";
        $headers[] = "Upgrade-Insecure-Requests: 1";
        $headers[] = "Cache-Control: max-age=0";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $html = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        echo "<br>";
        $html = str_get_html($html);
        return $html;
    }
    public function get_all_rows($html, $element)
    {
        $links_array_neww = array();
        foreach ($html->find($element) as $tag) {
            $linksarray[] = array(
                $tag->plaintext
            );
        }
        return $linksarray;
    }
    public function get_json($formated_array)
    {
        $json_result = array();
        foreach ($formated_array as $key) {
            $key[0] = str_replace(" ", ",", $key[0]);
            preg_match_all("/,\w{5,9}(,\w{2,9})*/", $key[0], $station_name);
            preg_match_all("/,\w{3,4},/", $key[0], $stn_code);
            preg_match_all("/\w{1,5}:\w{1,5},/", $key[0], $arrival_time);
            $stn_code[0][0]     = str_replace(",", "", $stn_code[0][0]);
            $station_name[0][0] = str_replace(",", " ", $station_name[0][0]);
            $arrival_time[0][0] = str_replace(",", "", $arrival_time[0][0]);
            array_push($json_result, array(
                "station_code" => $stn_code[0][0],
                "station_name" => $station_name[0][0],
                "arrival_time" => $arrival_time[0][0]
            ));
        }
        $json_data = json_encode(array(
            "result" => $json_result
        ));
        return $json_data;
    }
    function prettyprint($json)
    {
        $result          = '';
        $level           = 0;
        $in_quotes       = false;
        $in_escape       = false;
        $ends_line_level = NULL;
        $json_length     = strlen($json);
        for ($i = 0; $i < $json_length; $i++) {
            $char           = $json[$i];
            $new_line_level = NULL;
            $post           = "";
            if ($ends_line_level !== NULL) {
                $new_line_level  = $ends_line_level;
                $ends_line_level = NULL;
            }
            if ($in_escape) {
                $in_escape = false;
            } else if ($char === '"') {
                $in_quotes = !$in_quotes;
            } else if (!$in_quotes) {
                switch ($char) {
                    case '}':
                    case ']':
                        $level--;
                        $ends_line_level = NULL;
                        $new_line_level  = $level;
                        $char .= "<br>";
                        for ($index = 0; $index < $level - 1; $index++) {
                            $char .= "-----";
                        }
                        break;
                    case '{':
                    case '[':
                        $level++;
                        $char .= "<br>";
                        for ($index = 0; $index < $level; $index++) {
                            $char .= "-----";
                        }
                        break;
                    case ',':
                        $ends_line_level = $level;
                        $char .= "<br>";
                        for ($index = 0; $index < $level; $index++) {
                            $char .= "-----";
                        }
                        break;
                    case ':':
                        $post = " ";
                        break;
                    case "\t":
                    case "\n":
                    case "\r":
                        $char            = "";
                        $ends_line_level = $new_line_level;
                        $new_line_level  = NULL;
                        break;
                }
            } else if ($char === '\\') {
                $in_escape = true;
            }
            if ($new_line_level !== NULL) {
                $result .= "\n" . str_repeat("\t", $new_line_level);
            }
            $result .= $char . $post;
        }
        echo "RESULTS ARE: <br><br>$result";
        return $result;
    }
}
?>

