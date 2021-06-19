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

if(isset($_GET['uid']) and is_numeric($_GET['uid'])){
    $uid = $_GET['uid'];
} else {
    $uid = -1;
}

if(isset($_GET['pid']) and is_numeric($_GET['pid'])){
    $pid = $_GET['pid'];
} else {
    $pid = -1;
}

$sql = MySQL();

$query = $sql->prepare("
SELECT Solution.`ID`, User.`Name`, Solution.`Submitter`, Solution.`ProblemID`, Problem.`Title`, Solution.`Language`, Solution.`Status`, Solution.`Upload_time`FROM Solutions Solution
LEFT JOIN Problems Problem ON Solution.`ProblemID` = Problem.`ID`
LEFT JOIN Accounts User On User.`ID` = Solution.`Submitter`
WHERE (Solution.`Submitter` = ? OR ? = -1) AND (Solution.`ProblemID` = ? OR ? = -1) 
ORDER BY `ID` DESC LIMIT ? OFFSET ?;
");
$query->bind_param("dddddd", $uid, $uid, $pid, $pid, $limit, $offset);

$query->execute();
if($query->errno) respond(500, $msg = "Query failed.\n".($query->error), $data = array());
$result = $query->get_result();
$res = array();
$cnt = 0;

while($row = $result->fetch_assoc()){
    $res[] = $row;
    $cnt += 1;
}

respond(200, $msg = "Ok.", $data = array("num"=>$cnt, "offset"=>$offset, "submissions"=>$res));

?>
