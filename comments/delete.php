<?php
include "../db.php";

if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

if(!verify_csrf_token($_POST['csrf_token']??'')){
    die("잘못된 요청입니다.");
}

$comment_id=$_POST['id']; // 댓글 id
$post_id=$_POST['post_id']; // 삭제 후 돌아갈 게시글 id
$board_id=$_POST['board_id']; // detail.php에서 넘어온 게시판 id4

// 댓글 작성자 확인
$stmt=mysqli_prepare($conn,"SELECT * FROM comments WHERE id=?");
mysqli_stmt_bind_param($stmt,"i",$comment_id);
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);
$row=mysqli_fetch_assoc($result);

if(!$row){
    die("존재하지 않는 댓글입니다.");
}

if($row['author_id']!=$_SESSION['user_id']){
    header("Location:../posts/detail.php?id=$post_id&board_id=$board_id");
    exit;
}

// 댓글 삭제 
$stmt=mysqli_prepare($conn,"DELETE FROM comments WHERE id=?");
mysqli_stmt_bind_param($stmt,"i",$comment_id);
mysqli_stmt_execute($stmt);

// 삭제 후 해당 게시글의 상세페이지로
header("Location:../posts/detail.php?id=$post_id&board_id=$board_id");
exit;

?>