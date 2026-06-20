<?php

namespace App\Service;

use App\Repository\FeedbackRepository;

class FeedbackService{
	public function __construct(
		private FeedbackRepository $feedbackRepository
	){}

	public function addFeedback(array $data): int{
		return $this->feedbackRepository->save($data);
	}
}