<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 11.12.2017
 * Time: 13:22
 */

namespace mrcnpdlk\Xmdb\Model;


class RatioElement
{
    /**
     * @var float;
     */
    public $score;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Title
     */
    public $item;

    /**
     * RatioElement constructor.
     *
     * @param float                      $score
     * @param \mrcnpdlk\Xmdb\Model\Title $oTitle
     */
    public function __construct(float $score, Title $oTitle)
    {
        $this->score = $score;
        $this->item  = $oTitle;
    }
}
