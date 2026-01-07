<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupTestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup 
                            {--force : Execute the cleanup without confirmation}
                            {--dry-run : Preview what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up test users, keeping only admin and color team members';

    /**
     * Color team name patterns to preserve.
     */
    protected array $colorPatterns = [
        'Red',
        'Blue', 
        'Green',
        'Yellow',
        'Orange',
        'Purple',
        'Black',
        'White',
    ];

    /**
     * Protected admin email.
     */
    protected string $adminEmail = 'admin@admin.com';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧹 Cleanup Test Users Command');
        $this->newLine();

        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get IDs of users to preserve
        $preservedUserIds = $this->getPreservedUserIds();
        
        // Get users to delete
        $usersToDelete = User::whereNotIn('id', $preservedUserIds)->get();
        
        // Get teams to delete (non-color teams)
        $colorTeamIds = $this->getColorTeamIds();
        $teamsToDelete = Team::whereNotIn('id', $colorTeamIds)->get();

        // Show summary
        $this->showSummary($preservedUserIds, $usersToDelete, $teamsToDelete);

        if ($usersToDelete->isEmpty() && $teamsToDelete->isEmpty()) {
            $this->info('✅ Nothing to clean up!');
            return Command::SUCCESS;
        }

        if ($isDryRun) {
            $this->newLine();
            $this->info('ℹ️  Run without --dry-run to execute the cleanup');
            return Command::SUCCESS;
        }

        // Confirm before proceeding
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with the cleanup?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Execute cleanup
        $this->executeCleanup($usersToDelete, $teamsToDelete);

        return Command::SUCCESS;
    }

    /**
     * Get IDs of users that should be preserved.
     */
    protected function getPreservedUserIds(): array
    {
        $preservedIds = [];

        // 1. Get admin user
        $admin = User::where('email', $this->adminEmail)->first();
        if ($admin) {
            $preservedIds[] = $admin->id;
            $this->line("📌 Admin user: {$admin->name} ({$admin->email})");
        }

        // 2. Get color team IDs
        $colorTeamIds = $this->getColorTeamIds();

        // 3. Get all users belonging to color teams
        $colorTeamUserIds = DB::table('team_user')
            ->whereIn('team_id', $colorTeamIds)
            ->pluck('user_id')
            ->toArray();

        $preservedIds = array_unique(array_merge($preservedIds, $colorTeamUserIds));

        // Show color teams info
        $colorTeams = Team::whereIn('id', $colorTeamIds)->get();
        $this->newLine();
        $this->info("🎨 Color teams to preserve ({$colorTeams->count()}):");
        
        foreach ($colorTeams as $team) {
            $memberCount = $team->users()->count();
            $captains = $team->captains()->pluck('name')->join(', ') ?: 'No captain';
            $this->line("   - {$team->name} ({$memberCount} members, Captain: {$captains})");
        }

        return $preservedIds;
    }

    /**
     * Get IDs of color teams.
     */
    protected function getColorTeamIds(): array
    {
        return Team::where(function ($query) {
            foreach ($this->colorPatterns as $color) {
                $query->orWhere('name', 'like', "{$color}%");
            }
        })->pluck('id')->toArray();
    }

    /**
     * Show summary of what will be deleted.
     */
    protected function showSummary(array $preservedUserIds, $usersToDelete, $teamsToDelete): void
    {
        $this->newLine();
        $this->info('📊 Summary:');
        $this->table(
            ['Category', 'Count'],
            [
                ['Users to preserve', count($preservedUserIds)],
                ['Users to delete', $usersToDelete->count()],
                ['Teams to delete (non-color)', $teamsToDelete->count()],
            ]
        );

        if ($usersToDelete->count() > 0) {
            $this->newLine();
            $this->warn('🗑️  Users to be deleted:');
            
            // Show first 10 users
            $preview = $usersToDelete->take(10);
            foreach ($preview as $user) {
                $this->line("   - {$user->name} ({$user->email})");
            }
            
            if ($usersToDelete->count() > 10) {
                $remaining = $usersToDelete->count() - 10;
                $this->line("   ... and {$remaining} more users");
            }
        }

        if ($teamsToDelete->count() > 0) {
            $this->newLine();
            $this->warn('🗑️  Teams to be deleted:');
            
            foreach ($teamsToDelete->take(10) as $team) {
                $this->line("   - {$team->name}");
            }
            
            if ($teamsToDelete->count() > 10) {
                $remaining = $teamsToDelete->count() - 10;
                $this->line("   ... and {$remaining} more teams");
            }
        }
    }

    /**
     * Execute the cleanup.
     */
    protected function executeCleanup($usersToDelete, $teamsToDelete): void
    {
        $this->newLine();
        $this->info('🚀 Executing cleanup...');

        DB::beginTransaction();

        try {
            // Delete users first (cascade will clean team_user pivot)
            $userCount = $usersToDelete->count();
            if ($userCount > 0) {
                // Delete in chunks to avoid memory issues
                $userIds = $usersToDelete->pluck('id')->toArray();
                
                // Clean tournament_user pivot
                DB::table('tournament_user')->whereIn('user_id', $userIds)->delete();
                
                // Delete users
                User::whereIn('id', $userIds)->delete();
                $this->info("   ✅ Deleted {$userCount} users");
            }

            // Delete non-color teams
            $teamCount = $teamsToDelete->count();
            if ($teamCount > 0) {
                $teamIds = $teamsToDelete->pluck('id')->toArray();
                Team::whereIn('id', $teamIds)->delete();
                $this->info("   ✅ Deleted {$teamCount} teams");
            }

            DB::commit();

            $this->newLine();
            $this->info('🎉 Cleanup completed successfully!');

            // Show final stats
            $this->newLine();
            $this->info('📈 Final database stats:');
            $this->table(
                ['Table', 'Count'],
                [
                    ['Users', User::count()],
                    ['Teams', Team::count()],
                    ['Team memberships', DB::table('team_user')->count()],
                ]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error during cleanup: {$e->getMessage()}");
            throw $e;
        }
    }
}
