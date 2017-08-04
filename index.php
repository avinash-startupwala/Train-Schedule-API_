<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
require_once("train_info.php");
if (preg_match('/\w/', htmlspecialchars($_POST["query"]))) {
?>

 Welcome,

     <?php
?>
!
<br>

 Train number: <br><h3><?php
    echo htmlspecialchars($_POST["query"]);
?></h3> <?php
    $user_search      = htmlspecialchars($_POST['query']);
    $curlwithpost     = new CurlWithPost();
    $html             = $curlwithpost->curl_with_post($user_search);
    $links_array_neww = $curlwithpost->get_all_rows($html, "tr");
    $k                = 55;
    for ($i = 0; $i <= 26; $i++) {
        $formated_array[$i] = $links_array_neww[$k++];
    }
    unset($formated_array[0]);
    unset($formated_array[1]);
    $json_data = $curlwithpost->get_json($formated_array);
    $result    = $curlwithpost->prettyprint($json_data);
} else {
?>

    <form action=

    <?php
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
?>

     method="post">
        query: <input type="text" name="query"><br>
      
        <input type="submit" value="search">
    </form>
<?php
}
?>

