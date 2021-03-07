<?

header('Content-Type: application/json');
include_once('/var/www/html/private/env.php');

if(isset($_GET['limit']) and is_numeric($_GET['limit'])){
    $limit = intval($_GET['limit']);
} else {
    $limit = 20;
}

if(isset($_GET['offset']) and is_numeric($_GET['offset'])){
    $offset = intval($_GET['offset']);
} else {
    $offset = 0;
}

if(isset($_GET['filter']) and is_array($_GET['filter'])){
    $filter = $_GET['filter'];
} else {
    $filter = array();
}

$sql = MySQL();

if(empty($filter)){
    $result = $sql->prepare("SELECT `ID`, `Title`, `Source`, `Difficulty`, `Submit_Times`, `AC_times` FROM Problems WHERE `isPublic` = 1 ORDER BY `ID` DESC LIMIT ? OFFSET ?");
    $result->bind_param("dd", $limit, $offset);
    $result->execute();
    if($result->errno) respond(500, $msg = "Query failed.\n".($result->error), $data = array());
    $result = $result->get_result();
    $res = array();
    $cnt = 0;
    while($row = $result->fetch_assoc()){
        
        $TagQuery = $sql->prepare("
        SELECT B.`KeyName` FROM Tags A
        LEFT JOIN TagsName B ON A.`KeyID` = B.`ID`
        WHERE A.`ProblemID` = ?");
        $TagQuery->bind_param("d", $row['ID']);
        $TagQuery->execute();

        if($TagQuery->errno) respond(500, $msg = $TagQuery->error);
        $TagResult = $TagQuery->get_result();
        $tags = array();

        while($tag = $TagResult->fetch_assoc()){
            $tags[] = $tag["KeyName"];
        }
        
        $row['tags'] = $tags;
        $res[] = $row;
        $cnt += 1;
    }
    respond(200, $msg = "Ok.", $data = array("num"=>$cnt, "problems"=>$res));
} 



?>
