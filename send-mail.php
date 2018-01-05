<?php 
function sendmail($html_body,$subject,$to,$from_name,$from_mail,$plain_text_only,$attached_files) {   
  mb_language ("Japanese");
  mb_internal_encoding ("UTF-8");


  // email fields: to, from, subject, and so on
  $body = $html_body;

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "From: =?utf-8?b?".base64_encode($from_name)."?= <".$from_mail.">\n";

  // boundary 
  $semi_rand = md5(time()); 
  $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

  // headers for attachment 
  $headers .= $subpart = "" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

  // multipart boundary 
  $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" ;

  if($plain_text_only){   
    $message .= "Content-Type: text/plain; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . strip_tags(utf8_decode($html_body)) . "\n\n"; 
    $message .= "--{$mime_boundary}\n";
  }else {
    $message .= "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . utf8_decode($html_body) . "\n\n"; 
    $message .= "--{$mime_boundary}\n";
  }

  // preparing attachments
  if(!empty($attached_files)){
    $files = $attached_files;
    $uploaddir = dirname(__FILE__).'/../uploads_dir/';
    for($x=0;$x<count($files);$x++){
      $file = fopen($uploaddir.$files[$x],"rb");
      $data = fread($file,filesize($uploaddir.$files[$x]));
      fclose($file);
      $data = chunk_split(base64_encode($data));
      $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . 
      "Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . 
      "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
      $message .= "--{$mime_boundary}\n";
    }
  }

  $decoded_subject = utf8_decode($subject);
  $utf_subject = "=?utf-8?b?".base64_encode($decoded_subject)."?=";
  $send_status = @mail($to, $utf_subject, $message, $headers);
  return $send_status;
}