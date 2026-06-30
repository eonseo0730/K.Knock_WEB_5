<?php
include "../db.php"; // db.php 불러옴

if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}
$board_id=$_GET['board_id'] ?? 1; // 게시판 아이디 없으면 기본값 1
//게시판 이름 조회
$stmt=mysqli_prepare($conn, "SELECT name FROM boards WHERE id=?");
mysqli_stmt_bind_param($stmt,"i",$board_id); // $stmt에 정수 형태로 board_id를 넣음
mysqli_stmt_execute($stmt); // DB에서 실행
$board_name_result=mysqli_stmt_get_result($stmt); // 실행 결과를 fetch 가능한 결과셋으로 변환
$board_name_row=mysqli_fetch_assoc($board_name_result); // 결과셋에서 게시판 이름 한 줄 배열로 꺼냄, 없으면 null

if(!$board_name_row){
    die("존재하지 않는 게시판입니다.");
}
$board_name=$board_name_row['name']; // 정상인 경우만 이름 꺼냄


//검색어
$search=$_GET['search'] ?? '';
$search_type=$_GET['search_type'] ?? 'title'; // 검색 조건 저장

//정렬조건
$order=$_GET['order']??'desc'; // 최신순을 기본값으로
$order_sql=$order=='desc'?'DESC':'ASC'; // 정렬 방향 결정 

if($search != ''){ // 검색어가 있을 때

    $like_search='%'.$search.'%'; // like 검색에 쓸 패턴 만듦
    if($search_type=='title'){
        // 제목 검색용 sql -> board_id, 검색패턴을 ?로
        $sql="SELECT posts.*, users.username FROM posts JOIN users ON posts.author_id=users.id WHERE posts.board_id=? AND posts.title LIKE ? ORDER BY posts.created_at $order_sql";
    }else{ // search_type이 author인 경우 (유저 검색)
        // posts테이블과 users 테이블 조인 -> 작성자 이름과 일치 및 포함하는 글만 출력
        $sql="SELECT posts.*, users.username FROM posts JOIN users ON posts.author_id=users.id WHERE posts.board_id=? AND users.username LIKE ? ORDER BY posts.created_at $order_sql";
    }

    $stmt=mysqli_prepare($conn,$sql); // 위의 sql을 준비
    mysqli_stmt_bind_param($stmt,"is",$board_id,$like_search); // $stmt에 $board_id는 정수, $like_search는 string으로
    mysqli_stmt_execute($stmt); // 실행
    $result=mysqli_stmt_get_result($stmt); // 결과셋으로 반환

}else{
    //검색어가 없으면 전체 목록 출력
    $stmt=mysqli_prepare($conn,"SELECT posts.*, users.username FROM posts JOIN users ON posts.author_id=users.id WHERE posts.board_id=? ORDER BY posts.created_at $order_sql");
    mysqli_stmt_bind_param($stmt,"i",$board_id); // $stmt의 ? 자리에 $board_id를 정수 형태로
    mysqli_stmt_execute($stmt);
    $result=mysqli_stmt_get_result($stmt);
    
    
}
?>

<!DOcTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div class="board-nav">
                <a href="list.php?board_id=1" 
                class="btn <?php echo $board_id==1 ? 'btn-primary':'btn-secondary'; ?>">자유게시판</a>
                <a href="list.php?board_id=2" 
                class="btn <?php echo $board_id==2 ? 'btn-primary':'btn-secondary'; ?>">질문게시판 </a>
            </div>
            <h1><?php echo htmlspecialchars($board_name);?></h1> <!--htmlspecialchars : 특수문자를 html 엔티티로 변환 -> 브라우저가 태그로 해석 X-->
            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['username']); ?>님
                <a href="../auth/logout.php">로그아웃</a>
            </div>
        </div>
        <form class="search-form" method="GET" action="list.php">
            <input type="hidden" name="board_id" value="<?php echo $board_id;?>">
            <select name="search_type">
                <option value="title" <?php echo $search_type=='title'?'selected':'';?>>제목</option>
                <option value="author" <?php echo $search_type=='author'?'selected':'';?>>작성자</option>
            </select>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search);?>" placeholder="검색어 입력">
            <button type="submit">검색</button>
            <select name="order" onchange="this.form.submit()"> <!--드롭다운 선택 시 자동으로 폼 제출-->
                <option value="desc"<?php echo $order=='desc'?'selected':'';?>>최신순</option>
                <option value="asc"<?php echo $order=='asc'?'selected':'';?>>오래된순</option>
            </select>
        </form>
        <div class="write-btn-wrap">
            <!--글쓰기를 누르면 board_id도 같이 넘김-->
            <a href="write.php?board_id=<?php echo $board_id;?>" class="btn btn-primary">글쓰기</a>
        </div>
        <table class="board-table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>작성일</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <a href="detail.php?id=<?php echo $row['id']; ?>&board_id=<?php echo $board_id;?>">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>