<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

$recipients[0] = array(
    'email'           => 'user1@tutoria.com',
    'user'            => 'username1',
    'something'       => 'YOU WIN!!!',
    'something_photo' => 'photo1.jpg',
    'templateDir'     => 'template1'
);
$recipients[1] = array(
    'email'           => 'user1@tutoria.com',
    'user'            => 'username2',
    'something'       => 'YOU WIN!!!',
    'something_photo' => 'photo1.jpg',
    'templateDir'     => 'template2'
);

        // path to template directories
        $templatesPath = '/';
		// template directory name
		$templateDirname = 'template1';
		// template file name
		$templateFilename = 'index.html';
		// path to images directory, relative to $templateDirname
		$imagesPath = 'images';
		// images extensions
		$imagesExtensions = array('png', 'jpg', 'gif');
		// email subject
		$subject = '';
		// recipient email
		$emailTo = '';
		// recipient name
		$toName = null;
		// sender email
		$emailFrom = '';
		// sender name
		$fromName = null;
		// email content-type
		$contentType = 'text/html';
		// email charset
		$charset = 'utf-8';
/*		// template variables array
		$varData = array();*/
        $message = '';
		$attachedFiles;
		$bodyTemplates = array();


SendMail();

function SendMail () {
    $sm = new PHPMailer(true);

    $count = 0;
    foreach($recipients as $rec) {
        $varData = array(
            '{user}' => $rec['user'],
            '{something}' => $rec['something'],
            '{something_photo}' => $rec['something_photo']
        );
        $emailTo = $rec['email'];
        $toName = $rec['user'];
        $stemplateDirname = $rec['templateDir'];
        if ($sm->send())
            $count++;
    }
    echo $count." email(-s) have been sent.";
};


// get body and attach images if exists
private function GetBody()
{
    $tpl = GetBodyTemplate();
    $body = SubstituteData($tpl);
    $imgPath = preg_quote(trim($imagesPath, '/').'/', '/');
    $ext = implode('|', $imagesExtensions);
    return preg_replace_callback('/'.$imgPath.'((.+)\.('.$ext.'))/i', 'self::AddImage', $body);
};
//----------
// get body template
private function GetBodyTemplate()
{
    $path = GetPath($templatesPath, $templateDirname, $templateFilename);
    $bt =& $bodyTemplates[$path];
    if (isset($bt))
        return $bt;
    $bt = file_get_contents($path);
    return $bt;
}
//----------
// embed images to message and replace src attribute of img tags
private function AddImage($matches)
{
    $path = GetPath($templatesPath, $templateDirname, $matches[0]);
    $af =& $attachedFiles[$path];
    if (isset($af))
        return $af;
    $af = $this->message->embed(Swift_Image::fromPath($path));
    return $af;
}
//----------
// substitute data into template
private function SubstituteData($str)
{
    if (empty($this->varData))
        return $str;
    foreach($this->varData as $k => $v)
        $str = str_replace($k, $v, $str);
    return $str;
}
//----------
private function GetPath()
{
    $args = func_get_args();
    return preg_replace('/\/+/', '/', implode('/', $args));
};
