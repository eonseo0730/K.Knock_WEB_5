<?php
include "../db.php";

session_start(); // 세션 시작
if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$id=$_GET['id']; // URL에서 글 번호 받음

$sql="DELETE FROM posts WHERE id=$id";
mysqli_query($conn, $sql);
header("Location:list.php");
exit;
?>