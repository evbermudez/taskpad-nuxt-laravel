<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'matt@example.com'],
            [
                'name' => 'Matt Example',
                'password' => bcrypt('password'),
            ]
        );

        $samples = [
            'Write project documentation',
            'Push commits to GitHub',
            'Review pull requests',
            'Plan sprint backlog',
            'Refactor Vue components',
            'Optimize SQL queries',
            'Fix frontend lint errors',
            'Deploy to staging server',
            'Reply to team messages',
            'Prepare daily standup notes',
            'Update project roadmap',
            'Test new features',
            'Conduct code reviews',
            'Research new technologies',
            'Organize design assets',
            'Backup database',
            'Monitor application performance',
            'Clean up old branches',
            'Set up CI/CD pipeline',
            'Attend team meeting',
        ];

        // Seed 10 days of tasks
        for ($i = 0; $i < 10; $i++) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');

            $numTasks = rand(3, 6);
            $statements = collect($samples)->shuffle()->take($numTasks);

            foreach ($statements as $index => $statement) {
                Task::create([
                    'user_id' => $user->id,
                    'statement' => $statement,
                    'task_date' => $date,
                    'position' => $index + 1,
                    'is_done' => (bool) rand(0, 1),
                ]);
            }
        }

        $this->command->info('TaskSeeder completed for user: '.$user->email);
    }
}
