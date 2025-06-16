<?php 

namespace App\Services;

use App\Interfaces\TaskInterface;
use App\Models\Task;

class TaskService implements TaskInterface
{
    protected $model;
    
    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function getDate($data)
    {
        $tasks =  $this->model->forUser()
            ->status($data['status'] ?? null)
            ->priority($data['priority'] ?? null)
            ->dueBetween($data['start_date'] ?? null , $data['end_date'] ?? null)
            ->search($data['search'] ?? null)
            ->orderByField($data['sort_by'] ?? null, $data['sort_dir'] ?? null)
            ->paginate($data['per_page'] ?? 8);
        return $tasks;
    }

    public function store($data)
    {
        $data['user_id'] = auth()->user()->id;
        $data['status'] = $data['status'] ?? Task::STATUS_PENDING; 
        $task = $this->model->create($data);
        return $task;
    }

    public function update(array $data, $id)
    {
        $task = $this->model->findOrFail($id);
        if (isset($data['status'])) {
            $this->validateStatusTransition($task, $data['status']);
        }
        $task->update($data);
        return $task;
    }

    public function delete($id)
    {
        return  $this->model->findOrFail($id)->delete();
    }
    
    protected function validateStatusTransition(Task $task, string $newStatus)
    {
        if ($newStatus === Task::STATUS_COMPLETED && $task->status !== Task::STATUS_IN_PROGRESS) {
            throw new \InvalidArgumentException(
                'Invalid status transition: Task must be In Progress before being marked as Completed.'
            );
        }

        // Add other transition rules as needed
        if ($task->status === Task::STATUS_COMPLETED && $newStatus !== Task::STATUS_COMPLETED) {
            throw new \InvalidArgumentException(
                'Completed tasks cannot be reverted'
            );
        }
    }


    public function getTrashedData(){
         $tasks =  $this->model->onlyTrashed()->forUser()
            ->status($data['status'] ?? null)
            ->priority($data['priority'] ?? null)
            ->dueBetween($data['start_date'] ?? null , $data['end_date'] ?? null)
            ->search($data['search'] ?? null)
            ->orderByField($data['sort_by'] ?? null, $data['sort_dir'] ?? null)
            ->paginate($data['per_page'] ?? 8);
        return $tasks;
    }

    public function restore($id)
    {
        $task = $this->model->withTrashed()->findOrFail($id);
        return $task->restore();
    }

    public function forceDelete($id)
    {
        $task = $this->model->withTrashed()->findOrFail($id);
        return $task->forceDelete();
    }
    
}