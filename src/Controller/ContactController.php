<?php

namespace App\Controller;

use App\Dto\ContactRequest;
use App\Service\AIService;
use App\Service\EvaluateSettings;
use App\Service\FeedbackService;
use App\Service\RateLimiterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController{
	#[Route('api/contact', name: 'api_contact', methods: ['POST'])]
	public function submit(
		#[MapRequestPayload] ContactRequest $request,
		Request $httpRequest,
		RateLimiterService $rateLimiter,
		AIService $aiService,
		FeedbackService $feedbackService
		): JsonResponse{
		
		$ip = $httpRequest->getClientIp() ?? '127.0.0.1';

		if(!$rateLimiter->check($ip)){
			return $this->json([
				'status' => 'error',
				'message' => 'Слишком много запросов, попробуйте позже'
			], Response::HTTP_TOO_MANY_REQUESTS);
		}

		$settings = new EvaluateSettings(true, true, true);
		$aiResponse = $aiService->evaluate($request->comment, $request->name, $settings);

		$feedbackService->addFeedback([
			'name' => $request->name,
			'email' => $request->email,
			'phone' => $request->phone,
			'comment' => $request->comment,
			'sentiment' => $aiResponse['sentiment'],
			'type' => $aiResponse['type']
		]);

		return $this->json([
			'status' => 'ok',
			'data' => [
				'name' => $request->name,
				'email' => $request->email,
				'phone' => $request->phone,
				'comment' => $request->comment
			],
			'sentiment' => $aiResponse['sentiment'] ?? null,
			'type' => $aiResponse['type'] ?? null,
			'reply' => $aiResponse['reply'] ?? null,
		]);
	}
}