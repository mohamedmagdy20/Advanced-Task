<?php 

namespace App\Services;
use App\Interfaces\TaskNotificationInterface;
use App\Models\Task;
use Exception;


class TaskNotificationService implements TaskNotificationInterface
{
    protected $model ; 
    public function __construct(Task $model){
        $this->model = $model;
    }
    public function getNewTasks()
    {
        return $this->model->notSent()->pluck('id')->toArray();
    }

    public function getOverDueTask()
    {
        $ids = [];
        $this->model
        ->where('status', '!=', Task::STATUS_OVERDUE)
        ->latest()
        ->chunk(500, function ($tasks) use (&$ids) {
            foreach ($tasks as $task) {
                if ($task->isWithinFinal24Hours()) {
                    array_push($ids,$task->id);
                }
            }
        });
        return $ids;
    }

     public function markAsSend(array $tasksId)
    {
        try {
            $this->model
                ->whereIn('id', $tasksId)
                ->update(['is_sent' => true]);

            return true;
        } catch (Exception $e) {
            throw new \InvalidArgumentException('Completed tasks cannot be reverted.');
        }
    }

}