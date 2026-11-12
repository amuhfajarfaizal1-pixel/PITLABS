<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    // Beranda membaca tabel products sejak Fase 5B, jadi tes ini wajib menjalankan migration
    // pada database pengujian (SQLite in-memory) dan mengisolasi datanya dari tes lain.
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertStatus(200);
    }
}
