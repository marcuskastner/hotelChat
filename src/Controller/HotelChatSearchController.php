<?php

namespace App\Controller;

use App\Action\HotelCandidatesFromChatSearchAction;
use App\Service\HotelContentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HotelChatSearchController extends AbstractController
{
    #[Route('/hotel/chat/search', name: 'hotel_chat_search', methods: ['POST'])]
    public function search(
        Request $request,
        HotelCandidatesFromChatSearchAction $hotelCandidatesFromChatSearchAction,
        HotelContentService $hotelContentService,
    ): Response {
        try {
            $message = $request->request->get('message');

            if (!is_string($message) || trim($message) === '') {
                throw new \InvalidArgumentException('Message is required');
            }

            $candidates = $hotelCandidatesFromChatSearchAction->getHotelCandidates($message);
            $hotels = $hotelContentService->getHotelsContent($candidates);

        } catch (\Throwable $e) {
            return $this->render('hotel_chat/alert.html.twig', [
                'alert' => $e->getMessage(),
            ]);
        }

        return $this->render('hotel_chat/response.html.twig', [
            'message' => $message,
            'search' => '',
        ]);
    }
}
