<?php

namespace App\Controller;

use App\Action\HotelCandidatesFromChatSearchAction;
use App\Action\HotelSearchFromChatAction;
use App\Service\HotelContentService;
use App\Service\HotelRankerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HotelChatSearchController extends AbstractController
{
    #[Route('/hotel/chat/search', name: 'hotel_chat_search', methods: ['POST'])]
    public function search(
        Request $request,
        HotelSearchFromChatAction $hotelSearchFromChatAction,
        HotelCandidatesFromChatSearchAction $hotelCandidatesFromChatSearchAction,
        HotelContentService $hotelContentService,
        HotelRankerService $hotelRankerService,
    ): Response {
        try {
            $message = $request->request->get('message');

            if (!is_string($message) || trim($message) === '') {
                throw new \InvalidArgumentException('Message is required');
            }

            $search = $hotelSearchFromChatAction->getHotelSearch($message);
            $candidates = $hotelCandidatesFromChatSearchAction->getHotelCandidates($search);
            $hotels = $hotelContentService->getHotelsContent($search, $candidates);
            $rankedHotels = $hotelRankerService->rank($search, $hotels);
        } catch (\Throwable $e) {
            return $this->render('hotel_chat/alert.html.twig', [
                'alert' => $e->getMessage(),
            ]);
        }

        return $this->render('hotel_chat/response.html.twig', [
            'message' => $message,
            'search' => $search,
            'hotels' => $rankedHotels,
        ]);
    }
}
