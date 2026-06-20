<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService{
	public function __construct(
        private MailerInterface $mailer,
        private string $from,
        private string $to,
    ){}

	    public function sendNotification(string $userName,string $userEmail, string $userPhone, string $comment, ?string $aiReply = null){
        $ownerEmail = (new Email())
        	->from($this->from)
        	->to($this->to)
        	->subject('Новое обращение с сайта')
        	->text("Имя: {$userName}\nEmail: {$userEmail}\nТелефон: {$userPhone}\nСообщение: {$comment}")
        	->html("<h3>Новое обращение</h3><p><b>Имя:</b> {$userName}</p><p><b>Email:</b> {$userEmail}</p><p><b>Телефон:</b> {$userPhone}</p><p><b>Сообщение:</b> {$comment}</p>");

    	$this->mailer->send($ownerEmail);

		sleep(10);
        
		$replyText = $aiReply 
        ? "{$aiReply}\n\nВаше сообщение: {$comment}"
        : "Здравствуйте, {$userName}!\n\nСпасибо за ваше обращение. Мы получили его и скоро ответим.\n\nВаше сообщение: {$comment}";

    	$replyHtml = $aiReply
    	    ? "<p>{$aiReply}</p><p><b>Ваше сообщение:</b> {$comment}</p>"
    	    : "<h3>Здравствуйте, {$userName}!</h3><p>Спасибо за ваше обращение. Мы получили его и скоро ответим.</p><p><b>Ваше сообщение:</b> {$comment}</p>";

    	// Копия пользователю
    	$userEmailMessage = (new Email())
    	    ->from($this->from)
    	    ->to($userEmail)
    	    ->subject($aiReply ? 'Ответ на ваше обращение' : 'Спасибо за обращение')
    	    ->text($replyText)
    	    ->html($replyHtml);

    	$this->mailer->send($userEmailMessage);
    }
}