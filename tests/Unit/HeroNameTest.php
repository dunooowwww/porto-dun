<?php

namespace Tests\Unit;

use App\Support\HeroName;
use PHPUnit\Framework\TestCase;

class HeroNameTest extends TestCase
{
    public function test_starred_text_wins(): void
    {
        $this->assertSame(
            ['before' => 'Halo, kenalin aku ', 'name' => 'Ardan Putra', 'after' => '!'],
            HeroName::split('Halo, kenalin aku *Ardan Putra*!', 'Dunnow'),
        );
    }

    public function test_display_name_inside_greeting(): void
    {
        $this->assertSame(
            ['before' => 'Halo, aku ', 'name' => 'Dun', 'after' => ''],
            HeroName::split('Halo, aku Dun', 'Dun'),
        );
    }

    public function test_display_name_match_is_case_insensitive(): void
    {
        $this->assertSame('dun', HeroName::split('Halo, aku dun', 'Dun')['name']);
    }

    public function test_falls_back_to_last_word_without_duplicating_display_name(): void
    {
        $this->assertSame(
            ['before' => 'Halo, kenalin aku ', 'name' => 'ardan', 'after' => ''],
            HeroName::split('Halo, kenalin aku ardan', 'Dunnow'),
        );
    }

    public function test_trailing_punctuation_stays_outside_the_underline(): void
    {
        $this->assertSame(
            ['before' => 'Halo, aku ', 'name' => 'ardan', 'after' => '!'],
            HeroName::split('Halo, aku ardan!', null),
        );
    }

    public function test_empty_greeting_uses_display_name(): void
    {
        $this->assertSame(['before' => '', 'name' => 'Dun', 'after' => ''], HeroName::split('', 'Dun'));
    }
}
