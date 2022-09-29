<?php

/** 
 * This class gets video from TikTok with logo.
 * Author: >>> Phenix <<<
 * Date: 29.09.2022
 */
class GetFromTikTok
{
    private string $url;
    private string $video;
    private string $format;
    private string $message;
    private int $error;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->error = 0;
        $this->message = '';
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
    private function get_content(): string
    {
        $html = $this->wget($this->url);
        preg_match(
            '/<script id="SIGI_STATE" type="application\/json">(.*?)<\/script>/',
            $html,
            $json);
        $params = json_decode($json[1]);
        $id = $params->ItemList->video->list[0];
        $this->format = $params->ItemModule->$id->video->format;
        $url_video = $params->ItemModule->$id->video->downloadAddr;
        $video = $this->wget($url_video);

        return $video;
    }

    /**
     * Finction for gets data from TikTok
     */
    private function wget(string $url): string
    {
        $useragent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (' .
                        'KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36';
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_USERAGENT      => $useragent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 10,
        ]);

        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }
}