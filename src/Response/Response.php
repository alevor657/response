<?php

namespace Anax\Response;

/**
 * Handling a response.
 *
 */
class Response
{
    /**
    * Properties
    *
    */
    private $headers;       // Set all headers to send
    private $statusCode;    // Set statuscode to use
    private $body;          // Body to send with response



    /**
     * Set headers.
     *
     * @param string $header type of header to set
     *
     * @return $this
     */
    public function setStatusCode($value)
    {
        $supportedValues = [200, 403, 404, 500];
        if (!in_array($value, $supportedValues)) {
            throw new Exception("Unsupported statuscode: $value");
        }
        $this->statusCode = $value;
    }



    /**
     * Set headers.
     *
     * @param string $header type of header to set
     *
     * @return $this
     */
    public function setHeader($header)
    {
        $this->headers[] = $header;
    }



    /**
     * Check if headers are already sent and throw exception if it is.
     *
     * @return void
     *
     * @throws Exception
     */
    public function checkIfHeadersAlreadySent()
    {
        if (headers_sent($file, $line)) {
            throw new Exception("Try to send headers but headers already sent, output started at $file line $line.");
        }
    }



    /**
     * Send headers.
     *
     * @return $this
     */
    public function sendHeaders()
    {
        $statusHeader = [
            "200" => "HTTP/1.1 200 OK",
            "403" => "HTTP/1.1 403 Forbidden",
            "404" => "HTTP/1.1 404 Not Found",
            "500" => "HTTP/1.1 500 Internal Server Error"
        ];

        $this->checkIfHeadersAlreadySent();

        header($statusHeader[$this->statusCode]);

        foreach ($this->headers as $header) {
            header($header);
        }

        return $this;
    }



    /**
     * Set the body.
     *
     * @param string $body
     *
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }



    /**
     * Get the body.
     *
     * @return void
     */
    public function getBody()
    {
        return $this->body;
    }



    /**
     * Send response with an optional statuscode.
     *
     * @param integer $statusCode optional statuscode to send.
     *
     * @return void
     */
    public function send($statusCode = null)
    {
        if ($statusCode) {
            $this->setStatusCode($statusCode);
        }
        $this->sendHeaders();
        echo $this->getBody();
    }



    /**
     * Send JSON response with an optional statuscode.
     *
     * @param mixed   $data       to be encoded as json.
     * @param integer $statusCode optional statuscode to send.
     *
     * @return void
     */
    public function sendJson($data, $statusCode = null)
    {
        if ($statusCode) {
            $this->setStatusCode($statusCode);
        }

        $this->addHeader("Content-Type: application/json; charset=utf8");
        $this->sendHeaders();
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
    }



    /**
     * Redirect to another page.
     *
     * @param string $url to redirect to
     *
     * @return void
     */
    public function redirect($url)
    {
        $this->checkIfHeadersAlreadySent();
        header("Location: " . $url);
    }
}
