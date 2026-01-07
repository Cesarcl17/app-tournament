<?php

namespace Database\Seeders;

use App\Models\Tournament;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignTeamsAndUsersToTournamentsSeeder extends Seeder
{
    /**
     * Asigna equipos y usuarios existentes a los torneos existentes.
     * Cada equipo debe tener 5 usuarios, uno de ellos capitán.
     */
    public function run(): void
    {
        $tournaments = Tournament::all();
        $teams = Team::all();
        $users = User::all();
        $userIndex = 0;

        foreach ($tournaments as $tournament) {
            // Asignar equipos al torneo si no tiene suficientes
            $teamsForTournament = $teams->where('tournament_id', $tournament->id);
            $neededTeams = 8 - $teamsForTournament->count();
            if ($neededTeams > 0) {
                $availableTeams = $teams->whereNull('tournament_id')->take($neededTeams);
                foreach ($availableTeams as $team) {
                    $team->tournament_id = $tournament->id;
                    $team->save();
                }
            }
            // Asignar usuarios a cada equipo
            $teamsForTournament = $teams->where('tournament_id', $tournament->id);
            foreach ($teamsForTournament as $team) {
                $teamUsers = $team->users;
                $neededUsers = 5 - $teamUsers->count();
                if ($neededUsers > 0) {
                    $availableUsers = $users->whereNotIn('id', $teamUsers->pluck('id'))->slice($userIndex, $neededUsers);
                    $isFirst = true;
                    foreach ($availableUsers as $user) {
                        $team->users()->attach($user->id, [
                            'role' => $isFirst ? 'captain' : 'player',
                        ]);
                        $tournament->users()->attach($user->id, [
                            'status' => 'assigned',
                        ]);
                        $isFirst = false;
                        $userIndex++;
                    }
                }
            }
        }
    }
}
