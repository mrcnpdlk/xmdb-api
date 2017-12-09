<?php
/**
 * Created by Marcin.
 * Date: 09.12.2017
 * Time: 22:46
 */

namespace mrcnpdlk\Xmdb;


use Campo\UserAgent;
use Curl\Curl;
use HttpLib\Http;
use mrcnpdlk\Xmdb\Model\Omdb\Title;

class Omdb
{
    /**
     * @var \mrcnpdlk\Xmdb\Client
     */
    private $oClient;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $oLog;
    /**
     * @var \mrcnpdlk\Psr16Cache\Adapter
     */
    private $oAdapter;
    /**
     * @var string
     */
    private $sToken;
    /**
     * @var string
     */
    private $url;

    /**
     * Omdb constructor.
     *
     * @param \mrcnpdlk\Xmdb\Client $oClient
     *
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function __construct(Client $oClient)
    {
        $this->oClient  = $oClient;
        $this->oLog     = $oClient->getLogger();
        $this->oAdapter = $oClient->getAdapter();
        $this->sToken   = $oClient->getOmdbToken();
        $this->url      = 'http://www.omdbapi.com/?r=json&apikey=' . $oClient->getOmdbToken();
    }

    public function getByTitle(string $title)
    {
        try {
            $oResp = $this->oClient->getAdapter()->useCache(
                function () use ($title) {
                    $oCurl = new Curl();
                    $oCurl->setUserAgent(UserAgent::random());
                    $oCurl->setHeader('Accept-Language', $this->oClient->getLang());
                    $params = [
                        't'    => $title,
                        'plot' => 'full',
                    ];
                    $oCurl->get($this->url . '&' . http_build_query($params));

                    if ($oCurl->error) {
                        throw new \RuntimeException('Curl Error! ' . ($oCurl->httpStatusCode ? Http::message($oCurl->httpStatusCode) : 'Unknown code'),
                            $oCurl->error_code);
                    }

                    return $oCurl->response;
                },
                [$title, $this->oClient->getLang()],
                3600 * 2)
            ;

            return Title::create($oResp);

        } catch (\Exception $e) {
            return new Exception($e->getMessage(), 1, $e);
        }
    }
}
