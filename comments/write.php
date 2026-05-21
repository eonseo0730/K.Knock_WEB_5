<?php
include "../db.php";

session_start(); // 세션 시작
if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$post_id=$_POST['post_id'];
$content=$_POST['content'];
$author_id=$_SESSION['user_id'];

// 작성한 댓글 저장
$sql="INSERT INTO comments (post_id, content, author_id) VALUES ($post_id, '$content', $author_id)";
mysqli_query($conn, $sql);

// 글 상세페이지로 이동
header("Location: ../posts/detail.php?id=$post_id");
exit;
?>