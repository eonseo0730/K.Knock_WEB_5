<?php
include "../db.php";

if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

if(!verify_csrf_token($_POST['csrf_token'] ?? '')){ 
    die("잘못된 요청입니다.");
}

$post_id=$_POST['post_id'];
$content=$_POST['content'];
$author_id=$_SESSION['user_id'];
$board_id=$_POST['board_id']; // detail.php에서 hidden으로 넘어온 것

// 작성한 댓글 저장
$stmt=mysqli_prepare($conn,"INSERT INTO comments (post_id, content, author_id) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt,"isi",$post_id,$content,$author_id);
mysqli_stmt_execute($stmt);

// 글 상세페이지로 이동
header("Location: ../posts/detail.php?id=$post_id&board_id=$board_id");
exit;
?>