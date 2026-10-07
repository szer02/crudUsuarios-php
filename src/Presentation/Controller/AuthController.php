<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\User\DTO\CreateUserDTO;
use App\Application\User\DTO\LoginDTO;
use App\Application\User\UseCase\CreateUserUseCase;
use App\Application\User\UseCase\LoginUseCase;
use DomainException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final class AuthController extends AbstractController
{
    public function __construct(
        private readonly LoginUseCase $loginUseCase,
        private readonly CreateUserUseCase $createUserUseCase
    ) {}

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        // Se o usuário já estiver logado, redireciona para a home
        if ($request->getSession()->has('user_auth')) {
            return $this->redirectToRoute('home');
        }

        // Se a requisição for POST, o formulário foi enviado
        if ($request->isMethod('POST')) {
            $email = (string) $request->request->get('email', '');
            $password = (string) $request->request->get('password', '');

            $dto = new LoginDTO($email, $password);
            $user = $this->loginUseCase->execute($dto);

            if ($user !== null) {
                // Sucesso: Guarda os dados mínimos do usuário na sessão
                $request->getSession()->set('user_auth', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]);

                $this->addFlash('success', 'Bem-vindo(a), ' . $user->name . '!');
                return $this->redirectToRoute('home');
            }

            // Falha: Credenciais inválidas
            $this->addFlash('danger', 'E-mail ou senha incorretos.');
            return $this->redirectToRoute('app_login');
        }

        // Se for GET, apenas renderiza a tela de login
        return $this->render('auth/login.html.twig');
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        // Se o usuário já estiver autenticado, redireciona para a home
        if ($request->getSession()->has('user_auth')) {
            return $this->redirectToRoute('home');
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $name = (string) $request->request->get('name', '');
            $email = (string) $request->request->get('email', '');
            $password = (string) $request->request->get('password', '');
            $confirmPassword = (string) $request->request->get('confirm_password', '');

            if ($password !== $confirmPassword) {
                $error = 'As senhas informadas não coincidem.';
            } else {
                try {
                    $dto = new CreateUserDTO(
                        name: $name,
                        email: $email,
                        password: $password
                    );

                    $this->createUserUseCase->execute($dto);

                    $this->addFlash('success', 'Conta criada com sucesso! Faça login para acessar.');
                    return $this->redirectToRoute('app_login');
                } catch (InvalidArgumentException|DomainException $e) {
                    $error = $e->getMessage();
                } catch (Throwable) {
                    $error = 'Ocorreu um erro inesperado ao cadastrar o usuário. Tente novamente.';
                }
            }
        }

        return $this->render('auth/register.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(Request $request): Response
    {
        // Limpa a sessão completamente
        $request->getSession()->invalidate();
        $this->addFlash('info', 'Sessão encerrada com sucesso.');
        
        return $this->redirectToRoute('app_login');
    }
}