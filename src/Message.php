<?php

namespace Migrations\MigrationsGenerator;

class Message
{
    /**
     * Retorna mensagem de informação formatada para o terminal.
     *
     * @param string $message
     * @return string
     */
    public static function info(string $message): string
    {
        return "\033[34m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Retorna mensagem de sucesso formatada para o terminal.
     *
     * @param string $message
     * @return string
     */
    public static function success(string $message): string
    {
        return "\033[32m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Retorna mensagem de aviso formatada para o terminal.
     *
     * @param string $message
     * @return string
     */
    public static function warning(string $message): string
    {
        return "\033[33m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Retorna mensagem de erro formatada para o terminal.
     *
     * @param string $message
     * @return string
     */
    public static function error(string $message): string
    {
        return "\033[31m{$message}\033[0m" . PHP_EOL;
    }
}