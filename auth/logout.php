<?php
session_start(); // 세션 시작 (세션 조작 시 항상 start 먼저 호출)
 
session_destroy(); // 세션 전체 삭제

header("Location:login.php"); // 로그아웃 시 로그인 페이지로 이동
exit;

?>