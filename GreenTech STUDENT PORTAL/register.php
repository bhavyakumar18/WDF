<?php
require_once 'inc/bootstrap.php';
$errors=[]; $name=''; $email='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 csrf_check(); $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??'')); $mobile=trim($_POST['mobile']??'');
 $password=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
 if (!preg_match("/^[\p{L}][\p{L} .'-]{1,59}$/u",$name)) $errors[]='Enter a name with at least 2 letters.';
 if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid email address.';
 if (!preg_match('/^[0-9]{10}$/',$mobile)) $errors[]='Enter a 10-digit mobile number.';
 if (strlen($password)<8 || !preg_match('/[A-Za-z]/',$password) || !preg_match('/[0-9]/',$password)) $errors[]='Password must be at least 8 characters and include a letter and number.';
 if ($password!==$confirm) $errors[]='Passwords do not match.';
 if (empty($_POST['course'])) $errors[]='Choose your course.';
 if (empty($_POST['year'])) $errors[]='Choose your academic year.';
 if (empty($_POST['gender'])) $errors[]='Choose an option for gender.';
 if (empty($_POST['terms'])) $errors[]='Please accept the terms to continue.';
 if (!$errors) {
  try {
   require_once 'config/database.php'; $pdo=db();
   $check=$pdo->prepare('SELECT id FROM users WHERE email = ?'); $check->execute([$email]);
   if ($check->fetch()) $errors[]='An account with this email already exists.';
   else {
    $stmt=$pdo->prepare('INSERT INTO users (name,email,mobile,password_hash,course,academic_year,gender,role) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([$name,$email,$mobile,password_hash($password,PASSWORD_DEFAULT),$_POST['course'],$_POST['year'],$_POST['gender'],'student']);
    $id=(int)$pdo->lastInsertId(); session_regenerate_id(true);
    $_SESSION['user_id']=$id; $_SESSION['name']=$name; $_SESSION['email']=$email; $_SESSION['role']='student'; $_SESSION['last_activity']=time();
    flash('Your account is ready. Welcome to GreenTech!'); header('Location: dashboard.php'); exit;
   }
  } catch (Throwable $ex) { $errors[]='Could not save your account. Check that MySQL is running and greentech.sql has been imported.'; }
 }
}
$pageTitle='Create account'; require 'inc/header.php';
?>
<div class="form-page"><div class="form-intro"><div class="eyebrow">START HERE</div><h1>Create your account</h1><p>One quick form to set up your student space.</p><div class="form-aside"><span>✓</span><div><strong>Your details stay protected</strong><p>Passwords are hashed before they are stored.</p></div></div></div>
<form class="panel form-card" id="registerForm" method="post" action="register.php" novalidate>
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<?php if ($errors): ?><div class="notice error" role="alert"><strong>Please check these details:</strong><ul><?php foreach($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="form-grid"><div class="field full"><label for="name">Full name</label><input id="name" name="name" autocomplete="name" required minlength="2" value="<?= e($name) ?>" placeholder="e.g. Asha Patel"><small class="field-error" id="nameError"></small></div>
<div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" required value="<?= e($email) ?>" placeholder="you@example.com"><small class="field-error" id="emailError"></small></div>
<div class="field"><label for="mobile">Mobile number</label><input id="mobile" name="mobile" type="tel" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" required placeholder="10 digit number"><small class="field-error" id="mobileError"></small></div>
<div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" required minlength="8" placeholder="At least 8 characters"><div class="strength-track"><span id="strengthBar"></span></div><small id="strengthText" class="muted">Use 8+ characters with letters and numbers.</small><small class="field-error" id="passwordError"></small></div>
<div class="field"><label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required placeholder="Type it again"><small class="field-error" id="confirmError"></small></div>
<div class="field"><label for="course">Course</label><select id="course" name="course" required><option value="">Choose course</option><option>Information Technology</option><option>Computer Engineering</option><option>Computer Science</option><option>Data Science</option></select></div>
<div class="field"><label for="year">Academic year</label><select id="year" name="year" required><option value="">Choose year</option><option>First Year</option><option>Second Year</option><option>Third Year</option><option>Fourth Year</option></select></div>
<fieldset class="field full"><legend>Gender</legend><div class="radio-row"><label><input type="radio" name="gender" value="Female" required> Female</label><label><input type="radio" name="gender" value="Male"> Male</label><label><input type="radio" name="gender" value="Prefer not to say"> Prefer not to say</label></div></fieldset>
<div class="field full"><label class="check-row"><input type="checkbox" name="terms" required> I agree to the portal terms and privacy notice.</label></div></div>
<button class="btn primary full-btn" type="submit">Create account →</button><p class="form-foot">Already registered? <a href="login.php">Sign in</a></p>
</form></div>
<?php require 'inc/footer.php'; ?>
