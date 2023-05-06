<?php
declare(strict_types = 1);

/** 
 * This class gets video from TikTok with logo.
 * Author: >>> SHDTD <<<
 * Date: 29.09.2022
 */
class GetFromTikTok
{
    private string $url;
    private string | bool $video;
    private string $format;
    private string $message;
    private int $error;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->error = 0;
        $this->message = 'OK';
        $this->format = '';
        $this->video = $this->get_content();
    }

    public function get_video()
    {
        return $this->video;
    }

    public function get_format()
    {
        return $this->format;
    }

    public function get_error()
    {
        return $this->error;
    }

    public function get_error_msg()
    {
        return $this->message;
    }

    /**
     * Main function. Gets and parse html, gets id, format and video
     */
    private function get_content(): string | bool
    {
        $html = $this->wget($this->url);
        if (false === $html) {
            return false;
        }

        if (!preg_match(
            '/<script id="SIGI_STATE" type="application\/json">(.*?)<\/script>/',
            $html, $json))
        {
            $this->set_error(4, 'JSON not found in the page');
            return false;
        }

        $params = json_decode($json[1]);
        if (false === $params || null === $params) {
            $this->set_error(5, 'Unable to decode JSON');
            return false;
        }

        try {
            $id = $params->ItemList->video->list[0];
            $this->format = $params->ItemModule->$id->video->format;
            $url_video = $params->ItemModule->$id->video->playAddr;
        } catch (Exception $e) {
            $this->set_error(6, $e->getMessage());
            return false;
        }

        $video = $this->wget($url_video);

        return $video;
    }

    /**
     * Finction for gets data from web
     */
    private function wget(string $url): string | bool
    {
        $useragent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (' .
                        'KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36';

        $curl = curl_init($url);
        if (false === $curl) {
            $this->set_error(1, 'CURL: Initialize error');
            return false;
        }

        if ( false === curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_USERAGENT      => $useragent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 10,
        ])) {
            $this->set_error(2, 'CURL: Set options error');
            return false;
        }

        $data = curl_exec($curl);
        if(false === $data) {
            $this->set_error(3, 'CURL Error data transfer from ' . $url);
        }

        curl_close($curl);

        return $data;
    }

    /**
     * The function of setting the code and message in case of an error.
     */
    private function set_error(int $code, string $message): void
    {
        $this->error = $code;
        $this->message = $message;
        return;
    }
}
