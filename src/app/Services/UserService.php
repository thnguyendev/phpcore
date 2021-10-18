<?php
namespace App\Services;

use Psr\Http\Message\ServerRequestInterface;
use Firebase\JWT\JWT;

class UserService implements UserServiceInterface
{
    private const key = "secret key";
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function login($username, $password)
    {
        if (!is_string($username) || $username !== "username")
            throw new \Exception("Invalid username", 400);
        if (!is_string($password) || $password !== "password")
            throw new \Exception("Invalid password", 400);
        $time = time();
        $payload =
        [
            'iss' => "PHP-JWT",
            'iat' => $time,
            'nbf' => $time + 10,
            'exp' => $time + 600,
            'user' => $username,
        ];
        return JWT::encode($payload, $this::key);
    }

    public function authorize()
    {
        $query = $this->request->getQueryParams();
        $token = "";
        if (isset($query["token"]))
            $token = $query["token"];
        try
        {
            return JWT::decode($token, $this::key, array("HS256"));
        }
        catch (\Throwable $e)
        {
            throw new \Exception($e->getMessage(), 403);
        }
    }
}
?>