<!--h2>This is your code activation <?php //echo url::site('private/members/activation_by_mail/'.$code_activation); ?></h2-->
<!--Terima kasih untuk mendaftar, mohon bersabar dikarenakan account anda akan segera di aktivasi oleh Administrator.
Terima kasih.-->
<h2>Link Share</h2>
Teman anda membagi dan mengirimkan link ini via email, mohon klik link berikut <br/><br/><a href="<?php echo $referer; ?>" target="_blank"><?php echo $referer; ?></a> untuk melihat link ini.<br/><br/>Pesan dari teman anda : <br/><br/><?php echo $message;?></br><br/>Terima kasih.