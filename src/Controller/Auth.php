<?php
namespace App\Controller;

use App\Core\BaseController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Auth extends BaseController
{
    private Request $request;

    public function __construct(RequestStack $requestStack)
    {
        // RequestStack used instead Request::createFromGlobals(),
        // to get json request data
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Route('/api/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setPassword('testPassword094');

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        }
        catch(Exception $e) {
            return $this->ResponseError($e->getMessage());
        }

        return $this->ResponseOK();
    }

    #[Route('/api/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        if(!$user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'test@test.com']))
            return $this->ResponseFail('User not found');

        $security->login($user);

        return $this->ResponseOK();
    }

    #[Route('/api/auth/logout', name: 'api_auth_logout', methods: ['POST'])]
    public function logout(Security $security): JsonResponse
    {
        try {
            $security->logout(false);
        }
        catch(Exception $e) {
            return $this->ResponseError($e->getMessage());
        }
    }

    #[Route('/api/auth/get_user', name: 'api_auth_get_user', methods: ['GET'])]
    public function get_user(): JsonResponse
    {
        if(!$user = $this->getUser()) return $this->ResponseFail('Unable to logout as there is no logged-in user.');


        return $this->ResponseOK([
            'email' => $user->getEmail()
        ]);
    }

    #[Route('/api/auth/test', name: 'api_auth_test', methods: ['GET', 'POST'])]
    public function test(): JsonResponse
    {
        // Test if JSON request
        return $this->ResponseOK([
            $this->request->request->get('id')
        ]);
    }
}