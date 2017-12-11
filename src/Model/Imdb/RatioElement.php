<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 11.12.2017
 * Time: 13:22
 */

namespace mrcnpdlk\Xmdb\Model\Imdb;


class RatioElement
{
    /**
     * @var float;
     */
    public $score;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\Title
     */
    public $item;

    /**
     * RatioElement constructor.
     *
     * @param float                           $score
     * @param \mrcnpdlk\Xmdb\Model\Imdb\Title $oTitle
     */
    public function __construct(float $score, Title $oTitle)
    {
        $this->score = $score;
        $this->item  = $oTitle;
    }
}