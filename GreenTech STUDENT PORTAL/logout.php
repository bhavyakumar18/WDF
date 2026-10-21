<?php
require_once 'inc/bootstrap.php';
if (logged_in()) {
 try { require_once 'config/database.php'; $pdo=db(); $pdo->prepare('DELETE FROM remember_tokens WHERE user_id=?')->execute([$_SESSION['user_id']]); } catch(Throwable $e) {}
}
setcookie('gt_remember','',['expires'=>time()-3600,'httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax','path'=>'/']);
$_SESSION=[]; if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',['expires'=>time()-42000,'path'=>$p['path'],'domain'=>$p['domain'],'secure'=>$p['secure'],'httponly'=>$p['httponly'],'samesite'=>'Lax']); }
session_destroy(); session_start(); flash('You have been signed out safely.'); header('Location: welcome.php'); exit;
