<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
        function index()
        {
            $this->sessionService->current();
            if ($user == null){
                View::render('Home/index', [
                    "title" => "PHP Login Management"
                ]);
            }else{
                View::render('Home/dasboard', [
                    "title" => "Dashboard"
                    "user" => [
                        "name" => $user->name
                    ]
                ]);
            }
        }
    }