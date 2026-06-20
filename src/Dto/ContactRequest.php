<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ContactRequest{
	#[Assert\NotBlank(message: 'Имя обязательно')]
	#[Assert\Length(min: 2, max: 100, minMessage: 'Имя слишком короткое', maxMessage: 'Имя слишком длинное')]
	public string $name;

	#[Assert\NotBlank(message: 'Email обязателен')]
	#[Assert\Email(message: 'Некорректный email')]
	public string $email;

	#[Assert\NotBlank(message: 'Номер телефона обязателен')]
	#[Assert\Regex(
		pattern: '/^\+?[0-9\s\-\(\)]{7,20}$/',
		message: 'Некорректный номер телефона'
	)]
	public string $phone;

	#[Assert\NotBlank(message: 'Комментарий обязателен')]
	#[Assert\Length(min: 10, max: 1024, minMessage: 'Минимум 10 символов', maxMessage: 'Максимум 1024 символа')]
	public string $comment;
}