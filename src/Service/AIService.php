<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EvaluateSettings{
	public function __construct(
		public bool $analyzeSentiment,
		public bool $reply,
		public bool $classifyType
	){}
}

class AIService{
	
	public function __construct(
		private HttpClientInterface $httpClient,
		private string $endpoint = "http://192.168.0.107:8000",
		private string $token = "sk-lm-t2aVmmpi:o48VdYh3WByR6fT4PEeH",
		private string $model = "google/gemma-4-e4b"
	){}

	public function evaluate(string $comment, string $name, EvaluateSettings $settings){
		$message = "Ты - часть системы обратной связи лендинг-презентации разработчика. Твоя задача - помогать сервису выполнять свои задачи. ".
		"Отвечай ТОЛЬКО чистым JSON, без markdown-форматирования, без обрамляющих ```. Поля:\n";
		if($settings->analyzeSentiment){$message .= "sentiment - здесь тебе нужно одним словом описать тон сообщения обратной связи из списка: ['нейтрально', 'позитивно', 'негативно']\n";}
		if($settings->classifyType){$message .= "type - здесь тебе нужно одним русским словом назвать тип(категорию) обращения для сохранения в бд из списка: ['сотрудничество', 'вопрос', 'жалоба', 'отзыв', 'другое']";}
		if($settings->reply){$message .= "reply - ответь как ИИ-помощник разработчика сайта. Поздоровайся по имени, представься ИИ-помощником, поблагодари за обращение и скажи, что разработчик скоро ответит лично и кратко ответь по комментарию";}

		try{
			$response = $this->httpClient->request(
				'POST',
				$this->endpoint . '/v1/chat/completions',
				[
					'headers' => [
						'Authorization' => 'Bearer ' . $this->token,
						'Content-Type' => 'application/json'
					],
					'json' => [
						'model' => $this->model,
						'messages' => [
							['role' => 'system', 'content' => $message],
							['role' => 'user', 'content' => "Посетитель сайта, подписавшийся . $name . оставил сообщение: " . $comment]
						],
						'temperature' => 0.7,
					]
				]
			);
			$data = $response->toArray();
			$content = $data['choices'][0]['message']['content'] ?? null;

			return json_decode($content, true);
		}
		catch(\Throwable $err){
			error_log('AI Service Error: ' . $err->getMessage());
			return null;
		}

	}
}