<?php
include "../db.php";

if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$comment_id=$_GET['id']; // URL에서 댓글 번호 받음
$post_id=$_GET['post_id']; // 수정 후 돌아갈 게시글 번호
$board_id=$_GET['board_id']; // board-id

//POST 요청으로 데이터가 넘어온 경우
if($_SERVER['REQUEST_METHOD']=='POST'){

    if(!verify_csrf_token($_POST['csrf_token'] ?? '')){ 
        die("잘못된 요청입니다.");
    }

    $content=$_POST['content']; // 수정할 댓글 내용 받기
    $board_id=$_POST['board_id']; // POST로 board_id 받기

    $stmt=mysqli_prepare($conn,"UPDATE comments SET content=? WHERE id=?");
    mysqli_stmt_bind_param($stmt,"si",$content,$comment_id);
    mysqli_stmt_execute($stmt);

    // 수정 완료 후 해당 게시글 상세 페이지로 이동
    header("Location:../posts/detail.php?id=$post_id&board_id=$board_id");

    exit;
}

// 기존 댓글 데이터 가져옴
$stmt=mysqli_prepare($conn,"SELECT * FROM comments WHERE id=?");
mysqli_stmt_bind_param($stmt,"i",$comment_id);
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);
$row=mysqli_fetch_assoc($result); // 결과에서 한 줄 꺼냄

// 작성자 검증
if($row['author_id']!=$_SESSION['user_id']){ // 작성자와 현재 유저의 아이디를 비교
    // 댓글 작성자가 아닌 경우 상세 페이지로 이동
    header("Location:../posts/detail.php?id=$post_id&board_id=$board_id");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>댓글 수정</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>댓글 수정</h1>
        <form class="comment-form" method="POST" action="edit.php?id=<?php echo $comment_id; ?>&post_id=<?php echo $post_id; ?>&board_id=<?php echo $board_id;?>">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <input type="hidden" name="board_id" value="<?php echo $board_id;?>">
            <textarea name="content"><?php echo htmlspecialchars($row['content']); ?></textarea>
            <div class="form-actions" style="margin-top:10px;">
                <button type="submit">수정 완료</button>
                <a href="../posts/detail.php?id=<?php echo $post_id; ?>&board_id=<?php echo $board_id;?>" class="btn btn-secondary">취소</a>
            </div>
        </form>
    </div>
</body>
</html>