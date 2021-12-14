<?php

echo "--- Task 1: \r\n";
stairwayToHaven(100);
echo "\r\n";

echo "--- Task 2: \r\n";
matrixInRange::demo(5, 7, 1, 1000);
echo "\r\n";


/**
 * @param int $n
 */
function stairwayToHaven(int $n = 100)
{
    $inLine = 0;
    $newLine = 1;

    for ($i = 1; $i <= $n; ++$i) {
        echo $i;
        ++$inLine;

        if ($newLine === $inLine) {
            echo "\r\n";
            $inLine = 0;
            ++$newLine;
        } else {
            echo ' ';
        }
    }
    echo "\r\n";
}


/**
 * Class matrixInRange
 */
class matrixInRange {

    protected  $rowsCount;
    protected  $columnsCount;

    protected  $min;
    protected  $max;

    protected  $matrix = [];

    private $sumsByCol = [];
    private $sumsByRow = [];

    private $printMaxLength;

    /**
     * matrixInRange constructor.
     * @param int $rows
     * @param int $cols
     * @param int $min
     * @param int $max
     * @throws ErrorException
     */
    public function __construct(int $rows = 5, int $cols = 7, int $min = 1, int $max = 1000)
    {
        $this->rowsCount = $rows;
        $this->columnsCount = $cols;
        $this->max = $max;
        $this->min = $min;
        $this->validateParams();
    }

    /**
     * @throws ErrorException
     */
    protected function validateParams()
    {
        if (($this->columnsCount * $this->rowsCount) > (abs($this->max - $this->min) +1)) {
            throw new ErrorException("Uniqueness is unreachable :(");
        }

        if ($this->rowsCount <= 0 || $this->columnsCount <= 0) {
            throw new ErrorException(sprintf("The number of columns and rows must be positive! %s, %s received",
                $this->rowsCount, $this->columnsCount));
        }

        if ($this->min > $this->max) {
            throw new ErrorException("Invalid range parameters: min exceeds max value");
        }
    }

    /**
     * Get maximum length of value
     * for printing
     * @return mixed
     */
    protected function getPrintMaxLength()
    {
        return $this->printMaxLength ?? $this->printMaxLength = max(
                array_merge(
                    [strlen($this->min), strlen($this->max)],
                    array_map(function($sum) {
                        return strlen($sum);
                    }, $this->sumsByCol)
                )
            );
    }

    /**
     * @throws ErrorException
     */
    public function generate()
    {
        $range = range($this->min, $this->max);
        shuffle($range);

        $values = array_slice($range, 0, $this->columnsCount * $this->rowsCount);
        reset($values);

        for ($i = 0; $i < $this->rowsCount; ++$i) {
            for ($j = 0; $j < $this->columnsCount; ++$j) {
                $value = current($values);
                if ($value === false) {
                    throw new ErrorException("Matrix generation failed! Range exhausted");
                }

                $this->matrix[$i][$j] = $value;
                $this->sumsByCol[$j] = ($this->sumsByCol[$j] ?? 0) + $value;
                $this->sumsByRow[$i] = ($this->sumsByRow[$i] ?? 0) + $value;

                next($values);
            }
        }
    }

    /**
     *
     */
    public function printFormatted()
    {

        for ($i = 0; $i < $this->rowsCount; ++$i) {
            echo '|';
            for ($j = 0; $j < $this->columnsCount; ++$j) {
                echo $this->getElement($this->matrix[$i][$j]);
            }
            echo $this->getElement($this->sumsByRow[$i], ' ', '|  '), "\r\n";
        }
        echo "\r\n ";
        for ($j = 0; $j < $this->columnsCount; ++$j) {
            echo $this->getElement($this->sumsByCol[$j], ' ');
        }
        echo "  \r\n";
    }

    /**
     * @param $value
     * @param string $repeat
     * @param string $start
     * @param string $end
     * @return string
     */
    private function getElement(string $value, string $repeat = ' ', string $start = ' ', string $end = ' ')
    {
        return sprintf("%s%s%s%s",
            $start,
            str_repeat($repeat, $this->getPrintMaxLength() - strlen($value)),
            $value,
            $end
        );
    }

    /**
     * @param int $rows
     * @param int $cols
     * @param int $min
     * @param int $max
     */
    public static function demo(int $rows = 5, int $cols = 7, int $min = 1, int $max = 1000)
    {
        try {
            $matrix = new matrixInRange($rows, $cols, $min, $max);
            $matrix->generate();
            $matrix->printFormatted();
        } catch (ErrorException $e) {
            echo sprintf("Something went wrong in %s: \r\n %s \r\n", self::class, $e->getMessage());
        }
    }
}
