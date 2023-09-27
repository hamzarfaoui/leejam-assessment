<?php
namespace App\Http\Repositories\Interfaces;

/**
 * Interface TaskRepositoryInterface
 *
 * This interface defines the contract that concrete task repositories
 * must implement. It provides a set of methods for managing tasks.
 */
interface TaskRepositoryInterface {
    
    /**
     * Retrieve all tasks from the repository.
     *
     * @return mixed
     */
    public function allTasks();

    /**
     * Find a task in the repository based on provided data.
     *
     * @param mixed $data Data used for task lookup.
     * @return mixed The found task or null if not found.
     */
    public function findTask($data);

    /**
     * Save a new task to the repository.
     *
     * @param mixed $data Data representing the task to be saved.
     * @return mixed The newly created task.
     */
    public function saveTask($data);

    /**
     * Update an existing task in the repository.
     *
     * @param mixed $data Data representing the updated task.
     * @param mixed $task The task to be updated.
     * @return mixed The updated task.
     */
    public function updateTask($data, $task);

    /**
     * Delete a task from the repository.
     *
     * @param mixed $data Data representing the task to be deleted.
     * @return mixed True if the task was successfully deleted, false otherwise.
     */
    public function deleteTask($data);
}
