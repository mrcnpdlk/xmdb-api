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
use mrcnpdlk\Xmdb\Model\Ratio;

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

    /**
     * @param string                          $imdbId
     *
     * @param \mrcnpdlk\Xmdb\Model\Ratio|null $oRatio
     *
     * @return Title
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getByImdbId(string $imdbId, Ratio $oRatio = null)
    {
        try {
            $oResp = $this->oClient->getAdapter()->useCache(
                function () use ($imdbId) {
                    $oCurl = new Curl();
                    $oCurl->setUserAgent(UserAgent::random());
                    $oCurl->setHeader('Accept-Language', $this->oClient->getLang());
                    $params = [
                        'i'    => $imdbId,
                        'plot' => 'full',
                        'r'    => 'json',
                    ];
                    $oCurl->get($this->url . '&' . http_build_query($params));

                    if ($oCurl->error) {
                        throw new \RuntimeException('Curl Error! ' . ($oCurl->httpStatusCode ? Http::message($oCurl->httpStatusCode) : 'Unknown code'),
                            $oCurl->error_code);
                    }
                    if ($oCurl->response->Response !== 'True') {
                        throw new \RuntimeException($oCurl->response->Error);
                    }

                    return $oCurl->response;
                },
                [__METHOD__, $imdbId, $this->oClient->getLang()],
                3600 * 2)
            ;

            $oTitle = Title::create($oResp);

            if ($oRatio) {
                $oRatio->calculateRatio([$oTitle]);
            }

            return $oTitle;

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), 1, $e);
        }
    }

    /**
     * @param string $title
     *
     * @return Title
     * @throws \mrcnpdlk\Xmdb\Exception
     */
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
                        'r'    => 'json',
                    ];
                    $oCurl->get($this->url . '&' . http_build_query($params));

                    if ($oCurl->error) {
                        throw new \RuntimeException('Curl Error! ' . ($oCurl->httpStatusCode ? Http::message($oCurl->httpStatusCode) : 'Unknown code'),
                            $oCurl->error_code);
                    }
                    if ($oCurl->response->Response !== 'True') {
                        throw new \RuntimeException($oCurl->response->Error);
                    }

                    return $oCurl->response;
                },
                [__METHOD__, $title, $this->oClient->getLang()],
                3600 * 2)
            ;


            return Title::create($oResp);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), 1, $e);
        }
    }

}
