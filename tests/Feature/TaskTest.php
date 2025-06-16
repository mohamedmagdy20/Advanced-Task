<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    protected $user;

    /** @test */
    public function it_can_create_a_new_task()
    {
        $data = [
            'title' => 'New Task',
            'description' => 'Task description',
            'due_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'priority' => Task::PRIORITY_MEDIUM,
            'status' => Task::STATUS_PENDING,
        ];

       $this->post(route('tasks.store'), $data);

        $this->assertDatabaseHas('tasks', $data + ['user_id' => $this->user->id]);
    }


    /** @test */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        // Authenticate the user
        $this->actingAs($this->user);
    }


    public function it_belongs_to_a_user()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        
        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($this->user->id, $task->user->id);
    }

    public function it_has_fillable_attributes()
    {
        $fillable = [
            'title',
            'description',
            'due_date',
            'priority',
            'status',
            'user_id',
            'is_readed'
        ];

        $task = new Task();

        $this->assertEquals($fillable, $task->getFillable());
    }


        /** @test */
    public function it_has_correct_status_constants()
    {
        $this->assertEquals('pending', Task::STATUS_PENDING);
        $this->assertEquals('in_progress', Task::STATUS_IN_PROGRESS);
        $this->assertEquals('completed', Task::STATUS_COMPLETED);
        $this->assertEquals('overdue', Task::STATUS_OVERDUE);
    }

    /** @test */
    public function it_has_correct_priority_constants()
    {
        $this->assertEquals('high', Task::PRIORITY_HIGH);
        $this->assertEquals('medium', Task::PRIORITY_MEDIUM);
        $this->assertEquals('low', Task::PRIORITY_LOW);
    }


        /** @test */
    public function it_can_filter_tasks_by_status()
    {
        Task::factory()->create(['status' => Task::STATUS_PENDING]);
        Task::factory()->create(['status' => Task::STATUS_COMPLETED]);

        $pendingTasks = Task::status(Task::STATUS_PENDING)->get();
        $completedTasks = Task::status(Task::STATUS_COMPLETED)->get();

        $this->assertCount(1, $pendingTasks);
        $this->assertCount(1, $completedTasks);
        $this->assertEquals(Task::STATUS_PENDING, $pendingTasks->first()->status);
        $this->assertEquals(Task::STATUS_COMPLETED, $completedTasks->first()->status);
    }

    /** @test */
    public function it_can_filter_tasks_by_priority()
    {
        Task::factory()->create(['priority' => Task::PRIORITY_HIGH]);
        Task::factory()->create(['priority' => Task::PRIORITY_LOW]);

        $highPriorityTasks = Task::priority(Task::PRIORITY_HIGH)->get();
        $lowPriorityTasks = Task::priority(Task::PRIORITY_LOW)->get();

        $this->assertCount(1, $highPriorityTasks);
        $this->assertCount(1, $lowPriorityTasks);
        $this->assertEquals(Task::PRIORITY_HIGH, $highPriorityTasks->first()->priority);
        $this->assertEquals(Task::PRIORITY_LOW, $lowPriorityTasks->first()->priority);
    }


    /** @test */
    public function it_can_filter_tasks_by_due_date_range()
    {
        $now = now();
        $pastTask = Task::factory()->create(['due_date' => $now->copy()->subDays(5)]);
        $futureTask = Task::factory()->create(['due_date' => $now->copy()->addDays(5)]);

        $from = $now->copy()->subDays(10)->format('Y-m-d');
        $to = $now->copy()->addDays(10)->format('Y-m-d');

        $tasks = Task::dueBetween($from, $to)->get();

        $this->assertCount(2, $tasks);
        $this->assertTrue($tasks->contains($pastTask));
        $this->assertTrue($tasks->contains($futureTask));
    }

    /** @test */
    public function it_can_search_tasks_by_title_or_description()
    {
        $task1 = Task::factory()->create([
            'title' => 'Complete project documentation',
            'description' => 'Write all API docs'
        ]);

        $task2 = Task::factory()->create([
            'title' => 'Fix login bug',
            'description' => 'User cannot login on mobile'
        ]);

        // Search by title
        $results = Task::search('documentation')->get();
        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($task1));

        // Search by description
        $results = Task::search('mobile')->get();
        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($task2));
    }

        /** @test */
    public function it_can_get_tasks_for_specific_user()
    {
        $otherUser = User::factory()->create();

        $userTask = Task::factory()->create(['user_id' => $this->user->id]);
        $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

        $tasks = Task::forUser()->get();

        $this->assertCount(1, $tasks);
        $this->assertTrue($tasks->contains($userTask));
        $this->assertFalse($tasks->contains($otherTask));
    }

    /** @test */
    public function it_can_filter_sent_and_not_sent_tasks()
    {
        $sentTask = Task::factory()->create(['is_sent' => true]);
        $notSentTask = Task::factory()->create(['is_sent' => false]);

        $sentTasks = Task::sent()->get();
        $notSentTasks = Task::notSent()->get();

        $this->assertCount(1, $sentTasks);
        $this->assertCount(1, $notSentTasks);
        $this->assertTrue($sentTasks->contains($sentTask));
        $this->assertTrue($notSentTasks->contains($notSentTask));
    }

    /** @test */
    public function it_correctly_identifies_tasks_within_final_24_hours()
    {
        // Task due in 12 hours
        $urgentTask = Task::factory()->create([
            'due_date' => now()->addHours(12)
        ]);

        // Task due in 36 hours
        $nonUrgentTask = Task::factory()->create([
            'due_date' => now()->addHours(36)
        ]);

        // Task that's already overdue
        $overdueTask = Task::factory()->create([
            'due_date' => now()->subHours(1),
            'status' => Task::STATUS_OVERDUE
        ]);

        $this->assertTrue($urgentTask->isWithinFinal24Hours());
        $this->assertFalse($nonUrgentTask->isWithinFinal24Hours());
        $this->assertFalse($overdueTask->isWithinFinal24Hours());
    }

    /** @test */
    public function it_does_not_update_to_overdue_if_already_completed()
    {
        $task = Task::factory()->create([
            'due_date' => now()->subDay(),
            'status' => Task::STATUS_COMPLETED
        ]);

        $task->refresh();

        $this->assertEquals(Task::STATUS_COMPLETED, $task->status);
    }
}


