<?php
class SendEMail
{
	static $transport = false;

	public  // path to template directories
		$templatesPath,
		// template directory name
		$templateDirname,
		// template file name
		$templateFilename = 'index.html',
		// path to images directory, relative to $templateDirname
		$imagesPath = 'images',
		// images extensions
		$imagesExtensions = array('png', 'jpg', 'gif'),
		// email subject
		$subject = '',
		// recipient email
		$emailTo = '',
		// recipient name
		$toName = null,
		// sender email
		$emailFrom = '',
		// sender name
		$fromName = null,
		// email content-type
		$contentType = 'text/html',
		// email charset
		$charset = 'utf-8',
		// template variables array
		$varData = array();

	private $message,
		$attachedFiles,
		$bodyTemplates = array();
	//----------
	// constructor
	public function __construct()
	{
		if (!self::$transport)
			self::$transport = Swift_SmtpTransport::newInstance();
	}
	//----------
	// send email
	public function Send()
	{
		$this->message = Swift_Message::newInstance();
		$mess =& $this->message;
		$attachedFiles = array();
		$this->subject = $this->SubstituteData($this->subject);
		$body = $this->GetBody();
		if (!$body)
			return false;
		$mess->setTo($this->emailTo, $this->toName);
		$mess->setFrom($this->emailFrom, $this->fromName);
		$mess->setSubject($this->subject);
		$mess->setContentType($this->contentType);
		$mess->setCharset($this->charset);
		$mess->setBody($body);
		$mailer = Swift_Mailer::newInstance(self::$transport);
		return $mailer->send($this->message);
	}
	//----------
	// get body and attach images if exists
	private function GetBody()
	{
		$tpl = $this->GetBodyTemplate();
		$body = $this->SubstituteData($tpl);
		$imgPath = preg_quote(trim($this->imagesPath, '/').'/', '/');
		$ext = implode('|', $this->imagesExtensions);
		return preg_replace_callback('/'.$imgPath.'((.+)\.('.$ext.'))/i', 'self::AddImage', $body);
	}
	//----------
	// get body template
	private function GetBodyTemplate()
	{
		$path = $this->GetPath($this->templatesPath, $this->templateDirname, $this->templateFilename);
		$bt =& $this->bodyTemplates[$path];
		if (isset($bt))
			return $bt;
		$bt = file_get_contents($path);
		return $bt;
	}
	//----------
	// embed images to message and replace src attribute of img tags 
	private function AddImage($matches)
	{
		$path = $this->GetPath($this->templatesPath, $this->templateDirname, $matches[0]);
		$af =& $this->attachedFiles[$path];
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
	}
}