<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationExeption;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }


    public function register(){
        View::render('User/register', [
            'title' => 'Register new User'
        ]);


    }

    public function postRegister(){
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        }catch (ValidationExeption $exeption) {
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $exeption->getMessage()

            ]);
        }
    }

    public function login()
    {
        View::render('user/login', [
            "title" => "Login user"
        ]);

    }

    public function postlogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);
            View::redirect('/');
        }catch (ValidationExeption $exeption){
            View::render('User/login', [
                'title' => 'Register new User',
                'error' => $exeption->getMessage()
            ]);
        }
    }

    public function logout(){
        $this->sessionService->destroy();
        View::redirect("/");
    }
}