<?php

namespace App\Database\Seeds;

use Jumilla\Versionia\Laravel\Support\Seeder;
use Illuminate\Database\Eloquent\Model;

class Testing extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Model::reguard();
    }
}
