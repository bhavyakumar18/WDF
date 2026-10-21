<?php
require_once 'inc/bootstrap.php'; $error='';
if (isset($_GET['timeout'])) $error='Your session expired because of inactivity. Please sign in again.';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 csrf_check(); $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
 if (!filter_var($email,FILTER_VALIDATE_EMAIL) || $password==='') $error='Enter a valid email and password.';
 else {
  try {
   require_once 'config/database.php'; $pdo=db(); $stmt=$pdo->prepare('SELECT id,name,email,password_hash,role FROM users WHERE email=? LIMIT 1'); $stmt->execute([$email]); $user=$stmt->fetch();
   if ($user && password_verify($password,$user['password_hash'])) {
    session_regenerate_id(true); $_SESSION['user_id']=(int)$user['id']; $_SESSION['name']=$user['name']; $_SESSION['email']=$user['email']; $_SESSION['role']=$user['role']; $_SESSION['last_activity']=time();
    if (!empty($_POST['remember'])) {
     $token=bin2hex(random_bytes(32)); $hash=hash('sha256',$token); $ins=$pdo->prepare('INSERT INTO remember_tokens (user_id,token_hash,expires_at) VALUES (?,?,DATE_ADD(NOW(), INTERVAL 30 DAY))'); $ins->execute([$user['id'],$hash]);
     setcookie('gt_remember',$user['id'].':'.$token,['expires'=>time()+2592000,'httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax','path'=>'/']);
    }
    flash('Welcome back, '.$user['name'].'!'); header('Location: '.($user['role']==='admin'?'admin.php':'dashboard.php')); exit;
   } else $error='Email or password is incorrect.';
  } catch(Throwable $ex) { $error='Database is not ready. Start MySQL and import database/greentech.sql.'; }
 }
}
$pageTitle='Sign in'; require 'inc/header.php';
?>
<div class="login-wrap"><div class="login-side"><div class="eyebrow">GOOD TO SEE YOU</div><h1>Your next step<br>starts here.</h1><p>Sign in to return to your classes, plans and progress.</p><div class="login-decoration">✦</div></div><form class="panel login-card" method="post" action="login.php"><h2>Welcome back</h2><p class="muted">Use your student account to continue.</p><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><?php if($error): ?><div class="notice error" role="alert"><?= e($error) ?></div><?php endif; ?>
<div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="username" required placeholder="you@example.com"></div><div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Your password"></div><label class="check-row remember-row"><input type="checkbox" name="remember"> Remember me for 30 days</label><button class="btn primary full-btn" type="submit">Sign in →</button><p class="form-foot">New here? <a href="register.php">Create an account</a></p></form></div>
<?php require 'inc/footer.php'; ?>
