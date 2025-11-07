<?php
namespace App\Services;
use CodeIgniter\Email\Email;

class EmailService 
{
    public static function send($from, $to, $senderCompany, $subject, $message)
    {
        $email = new Email();
		$config = ['mailType' => 'html'];
		$email->initialize($config);

		$email->setFrom($from, $senderCompany);
		$email->setTo($to); // --- SWAP
		$email->setSubject($subject);
		$email->setMessage($message);
		$email->send();
		$email->clear();
    }
}