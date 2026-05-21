<?php
include "../db.php";

session_start(); // 세션 시작
if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$id=$_GET['id']; // GET으로 전달된 글 번호 받기

// id가 일치하는 데이터 찾기
$result=mysqli_query($conn, "SELECT * FROM posts WHERE id=$id");

$row=mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?php echo $row['title']; ?></title>
</head>
<body>
    <h1><?php echo $row['title']; ?></h1> <!--글 제목 출력-->
    
    <a href="../auth/logout.php">로그아웃</a>
    
    <p>작성자 ID : <?php echo $row['author_id']; ?></p>
    <p>작성일 : <?php echo $row['created_at']; ?></p>
    <hr>
    <p><?php echo $row['content']; ?></p> <!--글 내용 출력-->
    <hr>
    <a href = "edit.php?id=<?php echo $row['id']; ?>">수정</a> <!--수정 페이지로-->
    <a href="delete.php?id=<?php echo $row['id']?>">삭제</a> <!--삭제 페이지로-->
    <a href="list.php">목록</a> <!--목록 페이지로-->

    <hr>

<!-- 댓글 -->
    <?php
    // comments 테이블에서 현재 게시글의 댓글을 가져옴
    $comment_result=mysqli_query($conn, "SELECT * FROM comments WHERE post_id=$id");
    while($comment=mysqli_fetch_assoc($comment_result)):
    // 댓글이 없을 때까지 한 줄씩 꺼내서 반복
    ?>

    <div>
        <p><?php echo $comment['content'];?></p> <!--댓글 내용 출력-->
        <p>작성자 ID : <?php echo $comment['author_id'];?></p>

        <a href="../comments/delete.php?id=<?php echo $comment['id'];?>&post_id=<?php echo $id;?>" >삭제</a>
        <!--댓글 삭제 링크 -> 삭제할 댓글 id 전달-->
    </div>
    <?php endwhile;?>

    <!--댓글 작성 폼-->
    <form method="POST" action="../comments/write.php">
        <!--화면에 보이지는 않지만 POST로 전달됨 (현재 게시글 ID를 댓글 작성 시 같이 전달) -->
        <input type="hidden" name="post_id" value="<?php echo $id;?>">
        <textarea name="content"></textarea> <!--댓글 내용 입력-->
        <button type="submit">댓글 작성</button>
 
    </form>
</body>
</html>