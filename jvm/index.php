<?php
/**
 * Created by IntelliJ IDEA.
 * User: yefei
 * Date: 2020/6/9
 * Time: 6:23 PM
 */
$dir = './data/';

$act = $_REQUEST['act'];

if (!$act) {
    $act = 'index';
}

switch ($act) {
    case 'index':
        showIndex($dir);
        break;

    case 'detail':
        getContent($dir, $_REQUEST['id']);
        break;
    default:
        header("HTTP/1.1 400 Bad Request");
}



exit;

function _getArticleList($dir) {
    $articleList = array();
    if (is_dir($dir)){
        if ($dh = opendir($dir)){
            while (($file = readdir($dh)) !== false){
                if ($file != '.' && $file != '..')
                    $articleList[] = array('title' => $file, 'id' => substr($file,0,2));
            }
            closedir($dh);
        }
    }
    sort($articleList);
    return $articleList;
}

/**
 * 展示列表
 * @param $dir
 */
function showIndex ($dir) {
    $articleList = _getArticleList($dir);

    $page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;
    $limit = $_REQUEST['limit'] ? $_REQUEST['limit'] : 5;

    $offSet = ($page-1) * $limit;
    $resultList = array();
    if ($offSet < count($articleList)) {
        $resultList = array_slice($articleList, $offSet, $limit);
    }

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    echo json_encode(array('data' => array('items'=>$resultList, 'total'=>count($articleList)), 'code' => 20000));
}

/**
 * 获取文章内容
 * @param $dir
 * @param $fineName
 */
function getContent($dir, $articleID) {
    $articleList = _getArticleList($dir);
    foreach ($articleList as $article) {
        if ($article['id'] == $articleID) {
            $content = file_get_contents($dir.'/'.$article['title']);
            header("Access-Control-Allow-Headers: *");
            header("Access-Control-Allow-Origin: *");

            $respObj = json_decode($content);
//            $respObj['code'] = 20000;
            $respObj->code = 20000;
            $respObj->title = $article['title'];
            echo json_encode($respObj);

//            echo $content;
            exit;
        }
    }

    echo '{"code": 9527}';
}

