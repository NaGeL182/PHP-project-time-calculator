<?php
namespace ProjectTimeCalculator;

class ProjectTimeCalculator
{
    const WORK_DAYS = [1,2,3,4,5];
    const WORK_HOURS = [8,9,10,11,12,13,14,15,16];
    private $startDate;
    private $tasks;
    private $calculatedEndDate;
    private $dirty;

    public function __construct($startDate = null, $tasks = [])
    {
        if ($startDate !== null) {
            $this->setStartDate($startDate);
        }
        $this->setTasks($tasks);
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $this->validateAndReturnDateTime($startDate);
    }

    public function setTasks($tasks = [])
    {
        if (!\is_array($tasks)) {
            throw new \Exception("tasks must be an array. " . \gettype($tasks) . " given.");
        }
        $this->tasks = [];
        $this->dirty = true;
        foreach ($tasks as $key => $value) {
            $this->addTask($value);
        }
    }

    public function addTask($task)
    {
        if (!\is_array($task)) {
            throw new \Exception("task must be an array. " . \gettype($task) . " given.");
        }
        if (!\array_key_exists("startDate", $task)) {
            throw new \Exception("task must have a startDate. None given");
        }
        $task["startDate"] = $this->validateAndReturnDateTime($task["startDate"]);
        
        if (!\array_key_exists("duration", $task)) {
            throw new \Exception("task must have a duration. None given");
        }
        if (!\is_int($task["duration"])) {
            throw new \Exception(
                "task duration must be an integer (duration in hours). ". \gettype($task["duration"]) . " given."
            );
        }
        $task["duration"] = new \DateInterval("PT" . $task["duration"] . "H");
        $this->tasks[] = $task;
        $this->dirty = true;
    }

    public function getEndDate()
    {
        if ($this->dirty) {
            $this->calculatedEndDate = $this->calculateEndDate();
        }
        return $this->calculatedEndDate;
    }

    private function validateAndReturnDateTime($dateTime)
    {
        $date;
        if ($dateTime instanceof \DateTime) {
            $date = $dateTime;
        } elseif (\is_string($dateTime)) {
            //throws exception if its wrong format.
            $date = new \DateTime($dateTime);
        } else {
            throw new \Exception(
                "startDate must be a DateTime object or datetime string. " . \gettype($startDate) . " given."
            );
        }
        // check if start day is on a workday
        if (!\in_array($date->format('w'), self::WORK_DAYS)) {
            throw new \Exception(
                "Date doesnt start on a workday! (" . $date->format('Y-m-d H:i:s') . ")"
            );
        }
        // check if it start in a work hour
        if (!\in_array($date->format('G'), self::WORK_HOURS)) {
            throw new \Exception(
                "Date doesnt start in a work hour! (" . $date->format('Y-m-d H:i:s') . ")"
            );
        }
        return $date;
    }

    private function calculateEndDate()
    {
        $date = $this->startDate;
        foreach ($this->tasks as $key => $value) {
            $placeHolder = clone $value["startDate"];
            $placeHolder->add($value["duration"]);
            if ($date <= $placeHolder) {
                $date = $value["startDate"];
                $duration = $value["duration"];
                $workingHour = (int)$date->format('G');
                $firstWorkingHour = self::WORK_HOURS[0];
                $lastWorkingHour = self::WORK_HOURS[count(self::WORK_HOURS)-1];
                $newHour = $workingHour + $duration->h;
                if ($newHour - 1 > $lastWorkingHour) {
                    $remainingDurationForNextDay = $newHour - $lastWorkingHour;
                    $newWorkHour = $firstWorkingHour + $remainingDurationForNextDay;
                    $date->setTime($newWorkHour, 0);
                    $date->modify("+1 day");
                    if (!\in_array($date->format('w'), self::WORK_DAYS)) {
                        do {
                            $date->modify("+1 day");
                        } while (!\in_array($date->format('w'), self::WORK_DAYS));
                    }
                } else {
                    $date->setTime($newHour, 0);
                }
            }
        }
        $this->dirty = false;
        return $date;
    }
}
