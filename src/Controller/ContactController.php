<?php

namespace App\Controller;

use App\Dto\ContactRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController{
	#[Route('api/contact', name: 'api_contact', methods: ['POST'])]
	public function submit(#[MapRequestPayload] ContactRequest $request): JsonResponse{
		return $this->json([
			'status' => 'ok',
			'data' => [
				'name' => $request->name,
				'email' => $request->email,
				'phone' => $request->phone,
				'comment' => $request->comment
			]
		]);
	}
}