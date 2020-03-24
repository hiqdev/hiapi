<?php

namespace hiapi\Core\Console;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;

/**
 * Class for marshaling a request object sent with console.
 */
class ServerRequestFactory
{
    public const PROTOCOL_VERSION = '1.1';

    public const ATTRIBUTE_NAME = 'from-console';

    private $command;

    private $ops = [
        self::ATTRIBUTE_NAME => true,
    ];

    private $query = [];

    private $post = [];

    /**
     * Create a request from superglobal values: argv.
     * @return ServerRequest
     */
    public static function fromGlobals(): ServerRequest
    {
        return (new static())->createFromGlobals();
    }

    public function createFromGlobals(): ServerRequest
    {
        $this->parseGlobals();

        return $this->addAttributes(new ServerRequest(
            $_SERVER,
            $_FILES,
            $this->prepareUri($this->command),
            'POST',
            'php://input',
            $this->prepareHeaders($this->ops),
            $this->prepareCookies(),
            $this->query,
            $this->post,
            static::PROTOCOL_VERSION
        ), $this->ops);
    }

    private function parseGlobals()
    {
        $argv = $GLOBALS['argv'];
        $this->program = array_shift($argv);
        while ($argv[0][0]=='-') {
            $vs = explode('=', substr(array_shift($argv), 1), 2);
            $this->ops[$vs[0]] = $vs[1] ?? true;
        }
        $this->command = array_shift($argv);

        foreach ($argv as $n => $arg) {
            if ($arg[0] == '-') {
                $vs = explode('=', substr($arg, 1), 2);
                $this->query[$vs[0]] = is_null($vs[1]) ? true : $vs[1];
            } else $this->query[$n] = $arg;
        }

        if (!empty($this->ops['stdin'])) {
            $this->post = json_decode(file_get_contents('php://stdin'), true);
        }
    }

    private function addAttributes(ServerRequest $request, array $ops): ServerRequest
    {
        foreach ($ops as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request;
    }

    private function prepareUri(string $command): Uri
    {
        $uri = new Uri('');

        return $uri->withPath("/$command");
    }

    private function prepareHeaders(array $ops): array
    {
        return [
            'Accept' => $this->detectContentType($ops),
        ];
    }

    private function detectContentType(array $ops): string
    {
        if (!empty($ops['text'])) {
            return 'text/plain';
        }
        if (!empty($ops['show']) || !empty($ops['dump'])) {
            return 'text/php';
        }

        return 'application/json';
    }

    private function prepareCookies(): array
    {
        return $_COOKIE;
    }
}
