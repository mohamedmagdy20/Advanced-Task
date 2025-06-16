<?php 
namespace App\Interfaces;

interface TaskNotificationInterface
{
    // check for upcoming tasks every hour. 
    public function getNewTasks();

    //  24 hours before a task's due date
    public function getOverDueTask();

    // Update new tasks to mark as readed or notification sent 
    public function markAsSend(array $tasks );


}