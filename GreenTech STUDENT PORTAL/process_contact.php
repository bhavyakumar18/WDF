<?php
// Separate POST processor example for the server-side validation and file-storage practical.
require_once 'inc/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: contact.php');exit;}
csrf_check(); $name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$message=trim($_POST['message']??'');
$errors=[];if($name===''||mb_strlen($name)>80)$errors[]='Name is required (maximum 80 characters).';if(!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='A valid email is required.';if(mb_strlen($message)<5||mb_strlen($message)>2000)$errors[]='Message must be 5–2000 characters.';
if($errors){$_SESSION['form_errors']=$errors;header('Location: contact.php');exit;}
$record=['date'=>date('c'),'name'=>$name,'email'=>$email,'message'=>$message];
$json=__DIR__.'/data/submissions.json';$records=file_exists($json)?json_decode(file_get_contents($json),true):[];if(!is_array($records))$records=[];$records[]=$record;file_put_contents($json,json_encode($records,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),LOCK_EX);
$csv=__DIR__.'/data/submissions.csv';$fh=fopen($csv,'a');if($fh){if(filesize($csv)===0)fputcsv($fh,array_keys($record));fputcsv($fh,array_values($record));fclose($fh);}flash('Message validated and stored in CSV and JSON.');header('Location: contact.php');exit;
