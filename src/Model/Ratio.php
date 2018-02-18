<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 11.12.2017
 * Time: 12:42
 */

namespace mrcnpdlk\Xmdb\Model;

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
    public $directorsDisplay;
    /**
     * @var string|null
     */
    public $releaseYear;
    /**
     * @var \mrcnpdlk\Xmdb\Model\RatioElement[]
     */
    public $items = [];

    /**
     * Ratio constructor.
     *
     * @param string      $title
     * @param string|null $directorsDisplay
     * @param string|null $releaseYear
     */
    public function __construct(string $title, string $directorsDisplay = null, string $releaseYear = null)
    {
        $this->title            = $title;
        $this->directorsDisplay = $directorsDisplay;
        $this->releaseYear      = $releaseYear;
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
            if (!empty($this->director) && !empty($item->directorsDisplay)) {
                $oL             = new DamerauLevenshtein($this->directorsDisplay, $item->directorsDisplay);
                $fDirectorRatio = $oL->getRelativeDistance();
            }
            /** @noinspection IsEmptyFunctionUsageInspection */
            if (!empty($this->releaseYear) && !empty($item->releaseYear)) {
                $oL         = new DamerauLevenshtein($this->releaseYear, $item->releaseYear);
                $fYearRatio = $oL->getRelativeDistance();
            }
            $score = round(($fTitleRatio * 0.5 + $fDirectorRatio * 0.3 + $fYearRatio * 0.2) * 100, 3);

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
