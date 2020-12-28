<?php
require_once './swiftmailer/lib/swift_required.php';
require_once 'SendEMail.php';

$sm = new SendEMail();
// letter details are inserted into the template
$recipients[0] = array(
    'email'           => 'akadex@mail.ru',
	'user'            => 'akadex',
	'something'       => 'special offer',
	'something_photo' => 'offer.jpg',
	'templateDir'     => 'template1'
);
$recipients[1] = array(
	'email'           => 'zubkoveu@mail.ru',
	'user'            => 'Евгений',
	'something'       => 'special offer',
	'something_photo' => 'offer.jpg',
	'templateDir'     => 'template2'
);

$sm->templatesPath = "./email_templates";
$sm->emailFrom = 'e.u.zubkov1@gmail.com';
$sm->fromName = 'Evgeniy Zubkov';

$count = 0;
foreach($recipients as $rec) {
	$sm->varData = array(
		'{user}' => $rec['user'],
		'{something}' => $rec['something'],
		'{something_photo}' => $rec['something_photo']
	);
	$sm->emailTo = $rec['email'];
	$sm->toName = $rec['user'];
	$sm->templateDirname = $rec['templateDir'];
	if ($sm->Send())
		$count++;
}

echo $count." email(-s) have been sent.";