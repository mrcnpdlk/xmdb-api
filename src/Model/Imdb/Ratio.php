<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 11.12.2017
 * Time: 12:42
 */

namespace mrcnpdlk\Xmdb\Model\Imdb;


use Oefenweb\DamerauLevenshtein\DamerauLevenshtein;

class Ratio
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var string|null
     */
    public $director;
    /**
     * @var string|null
     */
    public $year;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\RatioElement[]
     */
    public $items = [];

    /**
     * Ratio constructor.
     *
     * @param string      $title
     * @param string|null $director
     * @param string|null $year
     */
    public function __construct(string $title, string $director = null, string $year = null)
    {
        $this->title    = $title;
        $this->director = $director;
        $this->year     = $year;
    }

    /**
     * @param \mrcnpdlk\Xmdb\Model\Imdb\Title[] $tTitles
     *
     * @return $this
     */
    public function calculateRatio(array $tTitles)
    {
        $this->items = [];
        foreach ($tTitles as $item) {
            $fTitleRatio    = 0;
            $fDirectorRatio = 0;
            $fYearRatio     = 0;
            if (!empty($this->title) && !empty($item->titleOrg)) {
                $oL          = new DamerauLevenshtein($this->title, $item->titleOrg);
                $fTitleRatio = $oL->getRelativeDistance();
            }
            /** @noinspection IsEmptyFunctionUsageInspection */
            if (!empty($this->director) && !empty($item->directorDisplay)) {
                $oL             = new DamerauLevenshtein($this->director, $item->directorDisplay);
                $fDirectorRatio = $oL->getRelativeDistance();
            }
            /** @noinspection IsEmptyFunctionUsageInspection */
            if (!empty($this->year) && !empty($item->year)) {
                $oL         = new DamerauLevenshtein($this->year, $item->year);
                $fYearRatio = $oL->getRelativeDistance();
            }
            $score = round((($fTitleRatio + $fDirectorRatio + $fYearRatio) / 3) * 100, 3);

            $this->items[] = new RatioElement($score, $item);

        }
        usort(
            $this->items,
            function (RatioElement $itemOne, RatioElement $itemTwo) {
                return $itemTwo->score <=> $itemOne->score;
            });

        return $this;
    }
}
