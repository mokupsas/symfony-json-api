<?php
namespace App\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    public function ResponseOK(array $data = [])
    {
        return $this->ResponseJSON(true, $data);
    }

    public function ResponseFail(string $msg)
    {
        return $this->ResponseJSON(false, ['message' => $msg]);
    }

    public function ResponseError(string $error_msg = 'An error has occurred')
    {
        return $this->ResponseJSON(false, ['error' => $error_msg]);
    }

    private function ResponseJSON(bool $success, $data)
    {
        return new JsonResponse([
            'success'   => $success,
            'data'      => $data
        ]);
    }
}