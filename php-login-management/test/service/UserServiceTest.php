<?php
namespace ProgrammerZamanNow\Belajar\PHP\MVC\App {
    function header(string $value){
        echo $value;
    }

}

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller{

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationExeption;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;

    class UserServiceTest extends TestCase
    {
        private UserService $userService;
        private UserRepository $userRepository;
        protected function setUp($userRepository):void
        {
            $Connection = Database::getConnection();
            $this->userRepository = new UserRepository($Connection);
            $this->userService = new UserService($this->$userRepository);

            $this->userRepository->deleteAll();
        }

        public function testRegisterSuccess()
        {
            $request = new UserRegisterRequest();
            $request->id = "eko";
            $request->name = "Eko";
            $request->password = "rahasia";

            $response = $this->userService->register($request);

            self::assertEquols($request->id, $response->user->id);
            self::assertEquols($request->name, $response->user->name);
            self::assertEquols($request->password, $response->user->password);

            self::assertEquols(password_verify($request->password, $response->user->password));


        }

        public function testRegisterFailed()
        {
            $this->expectException(ValidationExeption::class);

            $request = new UserRegisterRequest();
            $request->id = "";
            $request->name = "";
            $request->password = "";

            $this->userService->register($request);

        }

        public function testRegisterDuplicate()
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "Eko";
            $user->password = "rahasia";

            $this->userRepository->save($user);
            $this->expectException(ValidationExeption::class);

            $request = new UserRegisterRequest();
            $request->id = "eko";
            $request->name = "Eko";
            $request->password = "rahasia";

            $this->userService->register($request);
        }

        public function testLoginNotFound()
        {
            $this->expectException(ValidationExeption::class);
            $request = new UserLoginRequest();
            $request->id = "eko";
            $request->password = "eko";

            $this->userService->login($request);

        }

        public function testLoginArongPassword()
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "Eko";
            $user->password = password_hash("eko", PASSWORD_BCRYPT);

            $this->expectException(ValidationExeption::class);
            $request = new UserLoginRequest();
            $request->id = "eko";
            $request->password = "salah";

            $this->userService->login($request);

        }

        public function testLoginSuccess($response)
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "Eko";
            $user->password = password_hash("eko", PASSWORD_BCRYPT);

            $this->expectException(ValidationExeption::class);
            $request = new UserLoginRequest();
            $request->id = "eko";
            $request->password = "salah";

            $this->userService->login($request);

            self::assertEquals($request->id, $response->user->id);
            self::assertTrue(password_verify($request->password, $response->user->password));

        }



    }

}

