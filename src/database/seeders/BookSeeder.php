<?php

namespace Database\Seeders;

use App\Models\Book;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(Faker $faker): void
    {
        Book::create([
            'client_id' => 1,
            'book_category_id' => 1,
            'status' => Book::STATUS_CAN_LEND,
            'title' => 'トップセールスが使いこなす-〝基本にして最高の営業術″総まとめ-営業1年目の教科書-菊原智明',
            'description' => $book->description,
            'image_path' => $imagePath,
        ]);
    }
}
