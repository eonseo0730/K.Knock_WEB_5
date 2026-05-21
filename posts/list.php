<?php
include "../db.php"; // db.php 불러옴

session_start(); // 세션 시작
if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

// posts 테이블에서 전체 글 목록을 최신순으로 가져오기
$result=mysqli_query($conn, "SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOcTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
</head>
<body>
    <h1>게시판</h1>
    <p><?php echo $_SESSION['username']; ?>님 <a href="../auth/logout.php">로그아웃</a></p>
    <!-- 세션에 저장된 유저 id 출력 -->
    

    <a href="write.php">글쓰기</a> <!--글쓰기 페이지로 이동-->

    <br><br>

    <table border="1">
        <tr>
            <th>번호</th>
            <th>제목</th>
            <th>작성자</th>
            <th>작성일</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)): ?> <!--result에서 한 줄씩 배열로-->
        
        <tr>
            <td><?php echo $row['id']; ?></td> <!--현재 행의 id 값 출력-->
            <td>
                <a href="detail.php?id=<?php echo $row['id']; ?>"> <!--GET으로 id값 전달-->
                    <?php echo $row['title']; ?> <!--<a>태그로 제목 출력-->
                </a>
            </td>
            <td><?php echo $row['author_id']; ?></td> <!--작성자 출력-->
            <td><?php echo $row['created_at']; ?></td> <!--작성시각 출력-->
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>