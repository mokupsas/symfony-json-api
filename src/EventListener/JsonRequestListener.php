<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JsonRequestListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->getContent()) return false;
        if (!$this->isJsonRequest($request)) return false;

        try 
        {
            $data = \json_decode((string) $request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
            if (\is_array($data)) $request->request->replace($data);
        } 
        catch (\JsonException $exception)
        {
            $event->setResponse(new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST));
        }
    }

    private function isJsonRequest(Request $request)
    {
        return in_array($request->getContentTypeFormat(), ['json', 'jsonld'], true);
    }
}