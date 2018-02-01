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

    /**
     * @expectedException Exception
     * @dataProvider incorrectDataProvider
     */
    public function testIncorectData($data)
    {
        $project = new ProjectTimeCalculator();
        $project->setStartDate($data["startDate"]);
        $project->setTasks($data["tasks"]);
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
            "simple_1_task_dateTimeObject" => [
                [
                    "startDate" => new \DateTime("2018-01-10 09:00:00"),
                    "tasks" => [
                        [
                            "startDate" => new \DateTime("2018-01-10 09:00:00"),
                            "duration" => 8
                        ],
                    ],
                    "result" => "2018-01-10 17:00:00"
                ]
            ],
        ];
    }

    public function incorrectDataProvider()
    {
        return [
            "tasks_is_not_array" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => "hello",
                    "result" => ""
                ]
            ],
            "invidual_task_is_not_array" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => [
                        "ello",
                    ],
                    "result" => ""
                ]
            ],
            "task_has_no_startDate" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => [
                        []
                    ],
                    "result" => ""
                ]
            ],
            "task_has_no_duration" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => [
                        [
                            "startDate" => "2018-01-10 09:00:00"
                        ]
                    ],
                    "result" => ""
                ]
            ],
            "task_duration_non_int" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => [
                        [
                            "startDate" => "2018-01-10 09:00:00",
                            "duration" => 3.14
                        ]
                    ],
                    "result" => ""
                ]
            ],
            "startDate_is_int" => [
                [
                    "startDate" => 2018,
                    "tasks" => [
                        [
                            "startDate" => "2018-01-10 09:00:00",
                            "duration" => 3
                        ]
                    ],
                    "result" => ""
                ]
            ],
            "none_work_day_start" => [
                [
                    "startDate" => "2018-01-13 09:00:00",
                    "tasks" => [
                        [
                            "startDate" => "2018-01-10 09:00:00",
                            "duration" => 8
                        ],
                    ],
                    "result" => "2018-01-10 17:00:00"
                ]
            ],
            "none_work_hour_start" => [
                [
                    "startDate" => "2018-01-10 09:00:00",
                    "tasks" => [
                        [
                            "startDate" => "2018-01-10 07:00:00",
                            "duration" => 8
                        ],
                    ],
                    "result" => "2018-01-10 17:00:00"
                ]
            ],
        ];
    }
}
