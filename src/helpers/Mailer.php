<?php

/**
 * Envoi de mails simplifié. En environnement local (MAIL_LOG_ONLY=true),
 * les mails ne sont pas envoyés mais écrits dans storage/mails/ pour être
 * consultés facilement pendant les démonstrations / la soutenance.
 */
class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $config = require __DIR__ . '/../config/config.php';

        if ($config['mail']['log_only']) {
            $dir = __DIR__ . '/../../storage/mails';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $filename = $dir . '/' . date('Y-m-d_His') . '_' . preg_replace('/[^a-z0-9]+/i', '_', $to) . '.txt';
            file_put_contents($filename, "À : $to\nSujet : $subject\n\n$body");
            return true;
        }

        $headers = "From: {$config['mail']['from_name']} <{$config['mail']['from']}>\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        return @mail($to, $subject, $body, $headers);
    }
}
