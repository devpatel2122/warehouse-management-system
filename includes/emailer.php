<?php
/**
 * Warehouse Pro - Enterprise SMTP Emailer
 * Handles direct SMTP communication without external dependencies.
 */
class EnterpriseEmailer {
    private $host;
    private $port;
    private $user;
    private $pass;
    private $from;
    private $from_name;
    private $log = [];

    public function __construct($settings) {
        $this->host = $settings['smtp_host'] ?? '';
        $this->port = (int)($settings['smtp_port'] ?? 587);
        $this->user = $settings['smtp_user'] ?? '';
        $this->pass = $settings['smtp_pass'] ?? '';
        $this->from = $settings['company_email'] ?? '';
        $this->from_name = $settings['company_name'] ?? 'Warehouse Pro';
    }

    public function send($to, $subject, $message) {
        if (empty($this->host) || empty($this->user) || empty($this->pass)) {
            throw new Exception("SMTP Configuration is missing. Please go to System Settings and enter Host, User, and Password.");
        }

        $timeout = 15;
        $newline = "\r\n";
        
        $host_prefix = ($this->port == 465) ? 'ssl://' : '';
        $socket = @fsockopen($host_prefix . $this->host, $this->port, $errno, $errstr, $timeout);

        if (!$socket) {
            throw new Exception("Failed to connect to {$this->host}:{$this->port}. Error: $errstr ($errno)");
        }

        $this->read($socket); // Catch greeting

        $this->write($socket, "EHLO " . $this->host);
        $this->read($socket);

        if ($this->port == 587) {
            $this->write($socket, "STARTTLS");
            $res = $this->read($socket);
            if (strpos($res, '220') === false) {
                throw new Exception("STARTTLS failed: " . $res);
            }
            if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception("TLS negotiation failed. Please check if OpenSSL is enabled in php.ini");
            }
            $this->write($socket, "EHLO " . $this->host);
            $this->read($socket);
        }

        $this->write($socket, "AUTH LOGIN");
        $this->read($socket);
        
        $this->write($socket, base64_encode($this->user));
        $this->read($socket);
        
        $this->write($socket, base64_encode($this->pass));
        $auth_res = $this->read($socket);
        
        if (strpos($auth_res, '235') === false) {
            throw new Exception("Authentication failed: " . $auth_res);
        }

        $this->write($socket, "MAIL FROM: <" . $this->user . ">");
        $this->read($socket);
        
        $this->write($socket, "RCPT TO: <" . $to . ">");
        $this->read($socket);

        $this->write($socket, "DATA");
        $this->read($socket);

        $headers = "MIME-Version: 1.0" . $newline;
        $headers .= "Content-type: text/html; charset=utf-8" . $newline;
        $headers .= "To: <" . $to . ">" . $newline;
        $headers .= "From: " . $this->from_name . " <" . $this->user . ">" . $newline;
        $headers .= "Subject: " . $subject . $newline;
        $headers .= "Date: " . date('r') . $newline;
        $headers .= "Message-ID: <" . time() . "admin@" . $this->host . ">" . $newline;

        fputs($socket, $headers . $newline . $message . $newline . "." . $newline);
        $final_res = $this->read($socket);

        $this->write($socket, "QUIT");
        fclose($socket);

        return strpos($final_res, '250') !== false;
    }

    private function write($socket, $cmd) {
        fputs($socket, $cmd . "\r\n");
    }

    private function read($socket) {
        $res = "";
        while ($line = fgets($socket, 515)) {
            $res .= $line;
            if (substr($line, 3, 1) == " ") break;
        }
        return $res;
    }
}
?>
