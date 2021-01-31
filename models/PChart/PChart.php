<?php


namespace app\models\PChart;

use CpChart\Data;
use CpChart\Image;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class PChart
{
    private $width = 1300;
    private $height = 500;
    private $pointsCount = 48;
    private $labelsCount = 10;

    private function approximateData($_data, $y_step = 500)
    {

        $points = [];
        $min_time = 0;
        $max_time = 0;
        $min_val = 99999999999999;
        $max_val = 0;

        if (count($_data) < 5) {
            return [];
        }


        foreach ($_data as $key => $p) {
            $x = strtotime($p['date']);
            $y = intval($p['value']);

            $points[$x] = $y;

            if($min_time == 0) {
                $min_time = $x;
            }

            if($x <= $min_time) {
                $min_time = $x;
            }
            if($x >= $max_time) {
                $max_time = $x;
            }

            if($y <= $min_val) {
                $min_val = $y;
            }

            if($y >= $max_val) {
                $max_val = $y;
            }
        }

        $_points = [];
        foreach($points as $x => $y) {
            $_points[$x - $min_time] = $y;
        }

        $max_label = (floor($max_val / $y_step) + 1) * $y_step;

        $spline = new Spline();
        $step = ($max_time - $min_time) / $this->pointsCount;
        $spline->setCoords($_points, $step);
        $xy = $spline->process();

        $_y = [];
        $_x = [];
        $counter = 0;
        foreach($xy as $x => $y) {
            $counter++;
            $_y[] = max(intval($y), 0);
            if($counter % 4 == 0) {
                $_x[] = date('H:i', $x + $min_time);
            } else {
                $_x[] = "";
            }
        }

        return [
            'x' => $_x,
            'y' => $_y,
            'max' => $max_label
        ];
    }

    public function drawMultiChart($_data)
    {
        $total = 0;
        $rows = [];
        $isFirst = true;
        foreach ($_data as $row) {
            $decoded = Json::decode($row['value']);
            $regions = $decoded['regions'];

            foreach ($regions as $name => $online) {
                if ($isFirst) {
                    $rows[$name] = [];
                }
                $rows[$name][] = [
                    'value' => $online,
                    'date' => $row['date'],
                ];
            }

            $isFirst = false;
        }

        $chartData = [];
        foreach ($rows as $name => $points) {
            $chartData[$name] = $this->approximateData($points, 250);
        }

        $rowsOrder = ['ASI', 'EUR', 'RUS', 'USA', 'OCE', 'SAM', 'ME'];
        $rowsTotal = [
            'ASI' => 0,
            'EUR' => 0,
            'RUS' => 0,
            'USA' => 0,
            'OCE' => 0,
            'SAM' => 0,
            'ME'  => 0,
        ];

        foreach ($rowsOrder as $name) {
            $chart = $chartData[$name];
            if (empty($chart)) {
                continue;
            }
            $__y = $chart['y'];
            $rowsTotal[$name] = array_pop($__y);
        }

        $data = new Data();
        foreach ($chartData as $chart) {
            $_x = $chart['x'];
            $data->addPoints($_x, "Time");
            break;
        }
        $data->setAbscissa("Time");
        foreach ($rowsOrder as $name) {
            $chart = $chartData[$name];
            if (empty($chart)) {
                continue;
            }
            $_y = $chart['y'];
            if ($total < $chart['max']) {
                $total = $chart['max'];
            }
            $data->addPoints($_y, $name . ' - ' . $rowsTotal[$name]);
        }

        $image = new Image($this->width, $this->height, $data);
        $image->setFontProperties(["FontName" => "whitney.ttf", "FontSize" => 12]);
        $image->setGraphArea(60, 20, $this->width - 30, $this->height - 30);
        $image->drawFilledRectangle(-1, -1, $this->width + 1, $this->height + 1, [
            "R" => 52,
            "G" => 54,
            "B" => 60,
            "Dash" => false,
            "DashR" => 54,
            "DashG" => 57,
            "DashB" => 63,
            "BorderR" => 0,
            "BorderG" => 0,
            "BorderB" => 0
        ]);
        $image->setFontProperties(["R" => 255, "G" => 255, "B" => 255]);
        $ScaleSettings = [
            "XMargin" => 0,
            //"DrawSubTicks" => true,
            "DrawXLines" => false,
            //"DrawYLines" => ALL,
            "GridR" => 255,
            "GridG" => 255,
            "GridB" => 255,
            "AxisR" => 255,
            "AxisG" => 255,
            "AxisB" => 255,
            "GridAlpha" => 40,
            "CycleBackground" => false,
            "OuterTickWidth" => 5,
            "Factors" => [$this->labelsCount / 10],
            "Mode" => SCALE_MODE_MANUAL,
            "ManualScale" => [ 0 => ["Min" => 0, "Max" => $total]],
            "TickR" => 255,
            "TickG" => 255,
            "TickB" => 255,
        ];
        $image->drawScale($ScaleSettings);

        $alpha = 45;
        $colors = [
            'ASI' => ['R' => 27,  'G' => 44,  'B' => 255, 'Alpha' => $alpha],
            'EUR' => ['R' => 12,  'G' => 145, 'B' => 232, 'Alpha' => $alpha],
            'RUS' => ['R' => 0,   'G' => 255, 'B' => 217, 'Alpha' => $alpha],
            'USA' => ['R' => 12,  'G' => 232, 'B' => 73,  'Alpha' => $alpha],
            'OCE' => ['R' => 104, 'G' => 255, 'B' => 18,  'Alpha' => $alpha],
            'SAM' => ['R' => 232, 'G' => 223, 'B' => 11,  'Alpha' => $alpha],
            'ME'  => ['R' => 255, 'G' => 164, 'B' => 15,  'Alpha' => $alpha],
            //'DEF' => ['R' => 187, 'G' => 85,  'B' => 98,  'Alpha' => $alpha],
        ];

        foreach ($chartData as $name => $chart) {
            if (empty($chart)) {
                continue;
            }
            if (ArrayHelper::keyExists($name, $colors)) {
                $seriesSetting = $colors[$name];
                $data->setPalette($name . ' - ' . $rowsTotal[$name], $seriesSetting);
                $data->setSerieWeight($name . ' - ' . $rowsTotal[$name], 1.2);
            }
        }

        $image->drawFilledStepChart([
            "ReCenter" => true,
            "DisplayR" => 255,
            "DisplayG" => 255,
            "DisplayB" => 255,
            "DisplayValues" => TRUE,
            "DisplayColor" => DISPLAY_AUTO
        ]);

        $image->setFontProperties([
            "FontName" => "whitney.ttf",
            "FontSize" => 16
        ]);
        $image->drawLegend(70,20, [
            "Style" => LEGEND_ROUND,
            "BoxSize" => 4,
            "R" => 0,
            "G" => 0,
            "B" => 0,
            "Alpha" => 40,
            "Surrounding" => 20,
            "Family" => LEGEND_FAMILY_CIRCLE
        ]);

        $image->autoOutput("example.drawSpline.png");
    }

    public function drawChart($_data)
    {
        $xy = $this->approximateData($_data);
        if (empty($xy)) {
            return;
        }
        $_x = $xy['x'];
        $_y = $xy['y'];
        $max_label = $xy['max'];

        /* Create and populate the Data object */
        $data = new Data();

        $data->addPoints($_x, "Time");
        $data->setAbscissa("Time");

        $data->addPoints($_y, "Points");

        /* Create the Image object */
        $image = new Image($this->width, $this->height, $data);

        /* Write the picture title */
        $image->setFontProperties(["FontName" => "whitney.ttf", "FontSize" => 12]);

        /* Define the chart area */
        $image->setGraphArea(60, 20, $this->width - 30, $this->height - 30);

        /* Draw a rectangle */
        $image->drawFilledRectangle(-1, -1, $this->width + 1, $this->height + 1, [
            "R" => 52,
            "G" => 54,
            "B" => 60,
            "Dash" => false,
            "DashR" => 54,
            "DashG" => 57,
            "DashB" => 63,
            "BorderR" => 0,
            "BorderG" => 0,
            "BorderB" => 0
        ]);

        /* Draw the scale */
        $image->setFontProperties(["R" => 255, "G" => 255, "B" => 255]);
        $ScaleSettings = [
            "XMargin" => 0,
            //"DrawSubTicks" => true,
            "DrawXLines" => false,
            //"DrawYLines" => ALL,
            "GridR" => 255,
            "GridG" => 255,
            "GridB" => 255,
            "AxisR" => 255,
            "AxisG" => 255,
            "AxisB" => 255,
            "GridAlpha" => 40,
            "CycleBackground" => false,
            "OuterTickWidth" => 5,
            "Factors" => [$this->labelsCount / 10],
            "Mode" => SCALE_MODE_MANUAL,
            "ManualScale" => [ 0 => ["Min" => 0, "Max" => $max_label]],
            "TickR" => 255,
            "TickG" => 255,
            "TickB" => 255,
        ];
        $image->drawScale($ScaleSettings);

        $seriesSetting = ["R"=>79,"G"=>84,"B"=>92,"Alpha"=> 100];

        $data->setPalette("Points", $seriesSetting);
        $data->setSerieWeight("Points", 1.2);

        /* Draw the spline chart */
        $image->drawFilledStepChart(["ReCenter" => true, "DisplayR" => 255, "DisplayG" => 255, "DisplayB" => 255]);
        $seriesSetting = ["R"=>255,"G"=>255,"B"=>255,"Alpha"=> 100];

        $data->setPalette("Points", $seriesSetting);
        $image->drawStepChart(["DisplayColor" => DISPLAY_MANUAL,"ReCenter" => true, "DisplayR" => 255, "DisplayG" => 255, "DisplayB" => 255]);

        $BoundsSettings = array("MaxDisplayR" => 255,"MaxDisplayG" => 255,"MaxDisplayB" => 255, "MaxLabelTxt" => "", "BoxAlpha" => 80);
        $image->writeBounds(BOUND_MAX,$BoundsSettings);

        // Render the picture (choose the best way)
        $image->autoOutput("example.drawSpline.png");
    }
}