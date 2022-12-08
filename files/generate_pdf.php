<?php
session_start();
if(!isset($_SESSION["user_id"]))
  header("Location:../index.php");

include '../database/config.php';

require_once '../vendor/autoload.php';
use Dompdf\Dompdf ;
use Dompdf\Options;

$test_name = $_POST['test_name'];
$time = $_POST['time'];
$id_course = $_POST['id_course'];
$question_list = $_POST['question_list'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT * from course where id = $id_course";
$result = mysqli_query($conn,$sql);
$course_details = mysqli_fetch_assoc($result);
$course_id = $course_details["course_id"];
$course_name = $course_details["course_name"];

$options = new Options;
$options->setChroot('../');
$dompdf = new Dompdf($options);
$arr ;


$html = "    
<div '>
<div style='text-align:center;'>
    <h2> DAI HOC BACH KHOA TPHCM</h2>
    <hr>
    <h3>DE THI CHINH THUC</h3>
</div>
<div style='text-align:center;'>
    <h2> $test_name </h2>
    <h4>Bai thi: $course_name - $course_id </h4>
    <h5>Thoi gian lam bai: $time phut</h5>
    <hr>
</div>
</div>
<div style='margin: 20px;'>
<h4>Ho ten thi sinh:..........................................</h4>
<h4>So bao danh:..............................................</h4>
</div>
";

if(strlen($question_list) != 0){
    $string = substr($question_list, 1);
    echo "
        <script>
            console.log($string);
        </script>
        ";
    $arr = explode("(", $string);
    for($j = 0; $j <count($arr); $j++){
        $arr[$j] = substr($arr[$j], 0, -1);
    }
}


$i = 1;
foreach ($arr as $question_id){
    $sql = "select * from questions where id = $question_id";
    $result = mysqli_query($conn,$sql);
    
    $row = mysqli_fetch_assoc($result);
    $title = $row["title"];
    $opA = $row["optionA"];
    $opB = $row["optionB"];
    $opC = $row["optionC"];
    $opD = $row["optionD"];
    
    $tmp = "
    <div>
        <h4>$i. $title:</h4>
        <p><strong>A</strong>. $opA<p>
        <p><strong>B</strong>. $opB<p>
        <p><strong>C</strong>. $opC<p>
        <p><strong>D</strong>. $opD<p>
    </div>
    "; 
    echo "
        <script>
            console.log($tmp)
        </script>
        ";
    $html = $html . $tmp;
    $i += 1;
}










$dompdf->loadHtml($html);

$dompdf->render();
ob_end_clean();
$dompdf->stream('test.pdf', ['Attachment' => 0]);
?>