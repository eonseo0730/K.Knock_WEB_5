<?php
include "../db.php";

if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$id=$_GET['id']; // GET으로 전달된 글 번호 받기
$board_id=$_GET['board_id'] ?? 1; // board_id 받음

// id가 일치하는 데이터 찾기
$stmt=mysqli_prepare($conn,"SELECT posts.*, users.username FROM posts JOIN users ON posts.author_id=users.id WHERE posts.id=?");
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt); // 실행
$result=mysqli_stmt_get_result($stmt); // 실행 결과를 결과셋으로 변환
$row=mysqli_fetch_assoc($result); // 결과셋에서 한 줄을 배열로 꺼냄

if(!$row){
    die("존재하지 않는 게시글입니다.");
}

// 첨부파일 목록 가져오기
$stmt=mysqli_prepare($conn,"SELECT * FROM attachments WHERE post_id=?");
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt); // 실행
$file_result=mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['title']); ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($row['title']); ?></h1>
        <p class="post-meta">작성자: <?php echo htmlspecialchars($row['username']); ?> &nbsp;|&nbsp; <?php echo $row['created_at']; ?></p>
        <div class="post-content">
            <?php echo htmlspecialchars($row['content']); ?>
        </div>
        <?php if(mysqli_num_rows($file_result)>0): ?> <!--첨부파일이 있으면 첨부파일 섹션 표시-->
            <div class="attachment-section">
                <h3>첨부파일</h3>
                <?php while($file=mysqli_fetch_assoc($file_result)): ?> <!--첨부파일 없을때까지 한 줄씩 꺼내서 반복-->
                    <div class="attachment-item">
                        <a href="../download.php?id=<?php echo $file['id'];?>"> <!--download.php에 파일 id 전달-->
                            <?php echo htmlspecialchars($file['original_name']);?> <!--원본 파일명 표시-->                            </a>
                        <span>(<?php echo round($file['size_bytes']/1024, 1);?>KB)</span> <!--반올림하고 KB로 변환-->
                    </div>
                    <?php endwhile;?>
            </div>
        <?php endif;?>
        <div class="post-actions">
            <?php if($row['author_id']==$_SESSION['user_id']):?>
            <a href="edit.php?id=<?php echo $row['id']; ?>&board_id=<?php echo $board_id;?>" class="btn btn-secondary">수정</a>
            <form method="POST" action="delete.php" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $row['id'];?>">
                <input type="hidden" name="board_id" value="<?php echo $board_id;?>">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token();?>">
                <button type="submit" class="btn btn-danger" onclick="return confirm('삭제하시겠습니까?')">삭제</button>
            </form>
            <?php endif;?>
            <a href="list.php?board_id=<?php echo $board_id;?>" class="btn btn-secondary">목록</a>
        </div>

    

        <!-- 댓글 -->
        <div class="comment-section">
            <h2>댓글</h2>
            <?php
            $stmt=mysqli_prepare($conn,"SELECT comments.*,users.username FROM comments JOIN users ON comments.author_id=users.id  WHERE comments.post_id=?");
            mysqli_stmt_bind_param($stmt,"i",$id);
            mysqli_stmt_execute($stmt);

            $comment_result = mysqli_stmt_get_result($stmt);
            while($comment = mysqli_fetch_assoc($comment_result)):
            ?>
            <div class="comment-item">
                <p><?php echo htmlspecialchars($comment['content']); ?></p>
                <span class="comment-author">작성자: <?php echo htmlspecialchars($comment['username']); ?></span>
                <div class="comment-actions">
                    <?php if($comment['author_id']==$_SESSION['user_id']):?> <!--로그인한 유저 id와 댓글 작성자 id가 같을 때만 수정, 삭제 버튼 표시-->
                        <a href="../comments/edit.php?id=<?php echo $comment['id']; ?>&post_id=<?php echo $id; ?>&board_id=<?php echo $board_id;?>">수정</a>
                        <form method="POST" action="../comments/delete.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                            <input type="hidden" name="post_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="board_id" value="<?php echo $board_id;?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <button type="submit" onclick="return confirm('삭제하시겠습니까?')">삭제</button>
                        </form>
                    <?php endif;?>
                </div>
            </div>
            <?php endwhile; ?>

            <form class="comment-form" method="POST" action="../comments/write.php">
                <input type="hidden" name="post_id" value="<?php echo $id; ?>">
                <input type="hidden" name="board_id" value="<?php echo $board_id;?>">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token();?>">
                <textarea name="content" placeholder="댓글을 입력하세요"></textarea>
                <button type="submit">댓글 작성</button>
            </form>
        </div>
    </div>
</body>
</html>