<?php

namespace Migrations\MigrationsGenerator;

class Message
{
    /**
     * Retorna mensagem de informação formatada para o terminal.
     *
     * @param string $message Mensagem a ser exibida
     * @param string $title Título opcional para a mensagem
     * @return string Mensagem formatada
     */
    public static function info(string $message, string $title = ''): string
    {
        return "{$title} \033[34m{$message}\033[0m".PHP_EOL;
    }

    /**
     * Retorna mensagem de sucesso formatada para o terminal.
     *
     * @param string $message Mensagem a ser exibida
     * @param string $title Título opcional para a mensagem
     * @return string Mensagem formatada
     */
    public static function success(string $message, string $title = ''): string
    {
        return "{$title} \033[32m{$message}\033[0m".PHP_EOL;
    }

    /**
     * Retorna mensagem de aviso formatada para o terminal.
     *
     * @param string $message Mensagem a ser exibida
     * @param string $title Título opcional para a mensagem
     * @return string Mensagem formatada
     */
    public static function warning(string $message, string $title = ''): string
    {
        return "{$title} \033[33m{$message}\033[0m".PHP_EOL;
    }

    /**
     * Retorna mensagem de erro formatada para o terminal.
     *
     * @param string $message Mensagem a ser exibida
     * @param string $title Título opcional para a mensagem
     * @return string Mensagem formatada
     */
    public static function error(string $message, string $title = ''): string
    {
        return "{$title} \033[31m{$message}\033[0m".PHP_EOL;
    }
}