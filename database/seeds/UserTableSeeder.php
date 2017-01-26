<?php

use Illuminate\Database\Seeder;
use App\User;

class UserTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    User::truncate();
    User::insert([
      ['name'=>'Apera','email'=>'admin','password'=>Hash::make('admin'),'access'=>'Admin' ,'created_at' => \Carbon\Carbon::now()->toDateTimeString(),'updated_at' => \Carbon\Carbon::now()->toDateTimeString()],
    ]);
  }
}
