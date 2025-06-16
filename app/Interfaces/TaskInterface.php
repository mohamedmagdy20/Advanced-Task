<?php 
namespace App\Interfaces;

interface TaskInterface
{
    public function getDate(array $filter);
    public function store(array $data);
    public function update(array $data , $id);
    public function delete($id);

    public function getTrashedData($data);
    public function restore($id);
    public function forceDelete($id);


}