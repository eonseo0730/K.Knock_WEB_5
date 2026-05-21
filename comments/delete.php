<?php
include "../db.php";

session_start(); // 세션 시작
if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$comment_id=$_GET['id']; // 댓글 id
$post_id=$_GET['post_id']; // 삭제 후 돌아갈 게시글 id

$sql="DELETE FROM comments WHERE id=$comment_id";
mysqli_query($conn, $sql);

// 삭제 후 해당 게시글의 상세페이지로
header("Location:../posts/detail.php?id=$post_id");
exit;

?>