<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected array $valid = [
        'name' => 'Rekruter Contoh',
        'email' => 'rekruter@example.com',
        'message' => 'Halo Dun, ada lowongan magang yang cocok.',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
        $this->seed();
        RateLimiter::clear('contact:127.0.0.1');
    }

    public function test_form_is_rendered_on_home_page(): void
    {
        $this->get('/')
            ->assertSee('action="'.route('contact.store').'"', false)
            ->assertSee('name="website"', false)
            ->assertSee('Kirim pesan');
    }

    public function test_valid_message_is_saved_and_redirects_back_without_js(): void
    {
        $this->post('/kontak', $this->valid)
            ->assertRedirect(url('/').'#box-box')
            ->assertSessionHas('contact_sent');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'rekruter@example.com',
            'ip' => '127.0.0.1',
            'read_at' => null,
        ]);

        $this->followingRedirects()->post('/kontak', [...$this->valid, 'email' => 'lain@example.com'])
            ->assertSee('Pesan terkirim!');
    }

    public function test_valid_message_returns_json_for_fetch(): void
    {
        $this->postJson('/kontak', $this->valid)
            ->assertOk()
            ->assertJson(['message' => 'Pesan terkirim! Aku balas secepatnya.']);

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_invalid_input_is_rejected_with_indonesian_messages(): void
    {
        $this->postJson('/kontak', ['name' => '', 'email' => 'bukan-email', 'message' => 'pendek'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'message'])
            ->assertJsonPath('errors.name.0', 'Nama wajib diisi.')
            ->assertJsonPath('errors.message.0', 'Pesan harus berisi minimal 10 karakter.');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_honeypot_submission_is_not_saved(): void
    {
        $this->postJson('/kontak', [...$this->valid, 'website' => 'http://spam.example'])
            ->assertOk();

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_rate_limit_allows_three_messages_per_ten_minutes(): void
    {
        foreach (range(1, 3) as $i) {
            $this->postJson('/kontak', $this->valid)->assertOk();
        }

        $this->postJson('/kontak', $this->valid)
            ->assertUnprocessable()
            ->assertJsonPath('errors.message.0', fn (string $text) => str_contains($text, '3 pesan dalam 10 menit'));

        $this->assertSame(3, ContactMessage::count());

        $this->travel(11)->minutes();
        $this->postJson('/kontak', $this->valid)->assertOk();
        $this->assertSame(4, ContactMessage::count());
    }

    public function test_failed_validation_does_not_use_up_the_limit(): void
    {
        foreach (range(1, 5) as $i) {
            $this->postJson('/kontak', ['name' => 'x'])->assertUnprocessable();
        }

        $this->postJson('/kontak', $this->valid)->assertOk();
    }

    public function test_no_email_is_sent_when_mail_is_not_configured(): void
    {
        Mail::fake();
        config(['mail.default' => 'log']);
        SiteSetting::set('contact_email', 'dun@example.com');

        $this->postJson('/kontak', $this->valid)->assertOk();

        Mail::assertNothingSent();
    }

    public function test_email_is_sent_to_owner_when_mail_is_configured(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);
        SiteSetting::set('contact_email', 'dun@example.com');

        $this->postJson('/kontak', $this->valid)->assertOk();

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
            return $mail->hasTo('dun@example.com') && $mail->hasReplyTo('rekruter@example.com');
        });
    }

    public function test_broken_mail_server_does_not_break_the_form(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1,
            'mail.mailers.smtp.timeout' => 1,
        ]);
        SiteSetting::set('contact_email', 'dun@example.com');

        $this->postJson('/kontak', $this->valid)->assertOk();

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_contact_links_come_from_settings(): void
    {
        SiteSetting::set('contact_email', 'dun@example.com');
        SiteSetting::set('contact_whatsapp', '6281234567890');
        SiteSetting::set('contact_github', 'https://github.com/dun');

        $this->get('/')
            ->assertSee('mailto:dun@example.com', false)
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee('github.com/dun')
            ->assertDontSee('Instagram</span>', false);
    }
}
