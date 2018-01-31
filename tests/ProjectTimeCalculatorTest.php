<?php
namespace ProjectTimeCalculator\Tests;

use \PHPUnit\Framework\TestCase;
use \ProjectTimeCalculator\ProjectTimeCalculator;

/**
 * @coversDefaultClass \ProjectTimeCalculator\ProjectTimeCalculator
 */
final class ProjectTimeCalculatorTest extends TestCase
{
    /**
     * @dataProvider correctDataProvider
     */
    public function testCorectData($data)
    {
        $project = new ProjectTimeCalculator($data["startDate"], $data["tasks"]);
        $endDate = $project->getEndDate();
        $this->assertEquals($data["result"], $endDate->format("Y-m-d H:i:s"));
    }

    public function correctDataProvider()
    {
        return [
            "simple_1_task" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => [
                        [
                            "startDate" => "2018-01-10 09:00:00",
                            "duration" => 8
                        ],
                    ],
                    "result" => "2018-01-10 17:00:00"
                ]
            ],
            "simple_2_task" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => [
                        [
                            "startDate" => "2018-01-10 09:00:00",
                            "duration" => 8
                        ],
                        [
                            "startDate" => "2018-01-11 09:00:00",
                            "duration" => 16
                        ],
                    ],
                    "result" => "2018-01-12 17:00:00"
                ]
            ],
            "simple_3_task_weekend" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => [
                        [
                            "startDate" => "2018-01-10 09:00:00",
                            "duration" => 8
                        ],
                        [
                            "startDate" => "2018-01-11 09:00:00",
                            "duration" => 16
                        ],
                        [
                            "startDate" => "2018-01-12 15:00:00",
                            "duration" => 4
                        ],
                    ],
                    "result" => "2018-01-15 11:00:00"
                ]
            ],
        ];
    }
}
