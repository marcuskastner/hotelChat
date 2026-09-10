<?php

namespace App\Controller;

use App\Service\HotelSearchParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HotelChatSearchController extends AbstractController
{
    #[Route('/hotel/chat/search', name: 'hotel_chat_search', methods: ['POST'])]
    public function search(
        Request $request,
        HotelSearchParser $parser
    ): Response {
        try {
            $message = $request->request->get('message');

            if (!is_string($message) || trim($message) === '') {
                throw new \InvalidArgumentException('Message is required');
            }

            $search = $parser->parse($message);
        } catch (\Throwable $e) {
            return $this->render('hotel_chat/alert.html.twig', [
                'alert' => $e->getMessage(),
            ]);
        }

        return $this->render('hotel_chat/response.html.twig', [
            'message' => $message,
            'search' => $search,
        ]);
    }
}
