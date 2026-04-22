<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'mail:test';
    protected $description = 'Test mail configuration';

    public function handle()
    {
        Mail::raw('Test email from OpEx HRIS - mail is working!', function ($m) {
            $m->to('hezekiahkarington@gmail.com')->subject('OpEx HRIS Mail Test');
        });
        $this->info('Mail sent successfully!');
    }
}
