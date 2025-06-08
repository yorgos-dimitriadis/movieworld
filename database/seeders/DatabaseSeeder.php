<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $user1 = \App\Models\User::factory()->create([
           'name' => 'Yorgos',
           'email' => 'yo.dimitriadis@gmail.com',
        ]);

        $user2 = \App\Models\User::factory()->create([
           'name' => 'Kostas',
           'email' => 'kostas@example.com',
        ]);

        $user3 = \App\Models\User::factory()->create([
            'name' => 'Takis',
            'email' => 'takis@example.com',
        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user1->id,
            'title' => 'The Godfather',
            'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user2->id,
            'title' => 'The Dark Knight',
            'description' => 'When the menace known as the Joker emerges from his mysterious past, he wreaks havoc and chaos on the people of Gotham.',
        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user3->id,
            'title' => 'Pulp Fiction',
            'description' => 'The lives of two mob hitmen, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user1->id,
            'title' => 'Inception',
            'description' => 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a CEO.',
        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user3->id,
            'title' => 'Fight Club',
            'description' => 'An insomniac office worker and a devil-may-care soap maker form an underground fight club that evolves into something much, much more.',
        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user3->id,
            'title' => 'Forrest Gump',
            'description' => 'The presidencies of Kennedy and Johnson, the Vietnam War, the Watergate scandal and other historical events unfold through the perspective of an Alabama',
        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user2->id,
            'title' => 'The Matrix',
            'description' => 'A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.',
        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user2->id,
            'title' => 'The Lord of the Rings: The Return of the King',
            'description' => 'Gandalf and Aragorn lead the World',

        ]);

        \App\Models\Movie::factory()->create([
            'user_id' => $user1->id,
            'title' => 'Gladiator',
            'description' => 'A former Roman General sets out to exact vengeance against the corrupt emperor who murdered his family and sent him to slavery.',
        ]);




    }
}
