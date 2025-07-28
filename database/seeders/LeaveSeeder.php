<?php

namespace Database\Seeders;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $leaveTypes = array_column(\App\Enum\LeaveType::cases(), 'value');
        $statuses = array_column(\App\Enum\LeaveStatus::cases(), 'value');

        if ($users->isNotEmpty()) {
            for ($i = 0; $i < 5; $i++) {
                $user = $users->random();
                $startDate = fake()->dateTimeBetween('-6 months', '+3 months');
                $endDate = fake()->dateTimeBetween($startDate, $startDate->format('Y-m-d') . ' +15 days');

                $daysDiff = $startDate->diff($endDate)->days + 1;

                $status = fake()->randomElement($statuses);
                $reviewedBy = null;
                $reviewedAt = null;

                if ($status !== 'Pending') {
                    $reviewedBy = $users->where('id', '!=', $user->id)->random()->id;
                    $reviewedAt = fake()->dateTimeBetween($startDate, 'now');
                }

                Leave::create([
                    'user_id' => $user->id,
                    'type' => fake()->randomElement($leaveTypes),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'days_count' => $daysDiff,
                    'reason' => fake()->sentence(10),
                    'status' => $status,
                    'reviewed_by' => $reviewedBy,
                    'reviewed_at' => $reviewedAt
                ]);
            }
        }
    }
}
