<?php
$to = "lettman1@umbc.edu";
$from = "datrumpetplaya1@aol.com";
$subject = "HTML email";

$headers = "From: $from/r/n";
// $headers .= "Content-type: text/html/r/n";

$message = 'Hello There';
// $message = <<<EOF
// <html>
// <head>
// <title>HTML email</title>
// </head>
// <body>
// <p>This email contains HTML Tags!</p>
// <table>
// <tr>
// <th>Firstname</th>
// <th>Lastname</th>
// </tr>
// <tr>
// <td>John</td>
// <td>Doe</td>
// </tr>
// </table>
// </body>
// </html>
// EOF;

if(mail($to,$subject,$message))
{
	echo 'The message should have sent #12 (no header 12:53)';
}

?>				