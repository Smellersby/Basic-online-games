<?php

namespace Database\Seeders;

use App\Models\lobbies;
use App\Models\fields;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Type\Integer;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Andy',
            'email' => 'test@example.com',
        ]);
        User::factory()->create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
        ]);
        User::factory()->create([
            'name' => 'SomeGuy',
            'email' => 'SomeGuy@inbox.lv',
            'password'=>'SomeGuy'
        ]);
        User::factory()->create([
            'name' => 'Vadim',
            'email' => 'vadim@inbox.lv',
            'password'=>'vadim228'
        ]);
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@inbox.lv',
            'password'=>'admin',
            'role' => 'admin'
        ]);

        lobbies::create([
            'name' => "My first lobby",
            'creator' => 1,
            'playerOne' => 1,
            'playerTwo' => 2,
            'gameType' => "tic-tac-toe",
            'turn' => 1,
            'speed'=>"no",
        ]);
        lobbies::create([
            'name' => "SomeGuy's lobby",
            'creator' => 3,
            'gameType' => "tic-tac-toe",
            'turn' => 1,
            'speed'=>"no",
        ]);
        lobbies::create([
            'name' => "Empty snake lobby",
            'creator' => 2,
            'gameType' => "snake",
            'turn' => 0,
            'speed'=>"slow",
        ]);
        
        for($y=0;$y<3;$y++){
            for($x=0;$x<3;$x++){
                if($x==1&&$y==1){
                    $cellState="X";
                }else if($x==2&&$y==2){
                    $cellState="O";
                }else{
                    $cellState=null;
                }
                fields::create([
                    'lobbyID' => 1,
                    'x' => $x,
                    'y' => $y,
                    'cellState'=>$cellState
                ]);
            }
        }
        for($y=0;$y<3;$y++){
            for($x=0;$x<3;$x++){
                fields::create([
                    'lobbyID' => 2,
                    'x' => $x,
                    'y' => $y,
                ]);
            }
        }
        for($y=0;$y<12;$y++){
            for($x=0;$x<12;$x++){
                fields::create([
                    'lobbyID' => 3,
                    'x' => $x,
                    'y' => $y,
                ]);
            }
        }

    }
}
