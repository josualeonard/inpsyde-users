<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeUsers;

class UsersAPI
{
    private $url = "http://jsonplaceholder.typicode.com";
    private $usersEndpoint = "/users/";
    private $timeout = 30; // Seconds

    /**
     * Constructor
    *
    * @since  0.0.1
    */
    public function __construct(int $timeout = 30)
    {
        $this->timeout = $timeout;
    }

    public function users(): array
    {
        $cache = get_transient('inpsyde-users');
        if ($cache!==false) {
            return [
                'code' => 200,
                'message' => 'Success',
                'users' => json_decode($cache, true),
            ];
        }

        $result = [
            'code' => 0,
            'message' => '',
            'users' => [],
        ];
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->request(
                'GET',
                $this->url.$this->usersEndpoint,
                [
                    'connect_timeout' => $this->timeout,
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $users = json_decode($res->getBody()->getContents(), true);
            $result = [
                'code' => 200,
                'message' => 'Success',
                'users' => $users,
            ];
            set_transient('inpsyde-users', json_encode($users), 300);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            $result['code'] = $exception->getCode();
            $result['message'] = $exception->getMessage();
            if ($result['code']===404) {
                $result['message'] = 'Endpoint not found';
            }
        } catch (\GuzzleHttp\Exception\ConnectException $exception) {
            $result['code'] = $exception->getCode();
            $result['message'] = "Connection timeout";
        } catch (\GuzzleHttp\Exception\ServerException $exception) {
            $result['code'] = $exception->getCode();
            $result['message'] = "Service unavailable";
        }
        return $result;
    }

    public function user(int $id): array
    {
        $cache = get_transient('inpsyde-user-'.$id);
        if ($cache!==false) {
            return [
                'code' => 200,
                'message' => 'Success',
                'user' => json_decode($cache, true),
            ];
        }

        $result = [
            'code' => 0,
            'message' => '',
        ];
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->request(
                'GET',
                $this->url.$this->usersEndpoint.$id,
                [
                    'connect_timeout' => $this->timeout,
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $user = json_decode($res->getBody()->getContents(), true);
            $result = [
                'code' => 200,
                'message' => 'Success',
                'user' => $user,
            ];
            set_transient('inpsyde-user-'.$id, json_encode($user), 300);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            $result['code'] = $exception->getCode();
            $result['message'] = $exception->getMessage();
        } catch (\GuzzleHttp\Exception\ConnectException $exception) {
            $result['code'] = $exception->getCode();
            $result['message'] = "Connection timeout";
        } catch (\GuzzleHttp\Exception\ServerException $exception) {
            $result['code'] = $exception->getCode();
            $result['message'] = "User not exists";
        }
        return $result;
    }
}
