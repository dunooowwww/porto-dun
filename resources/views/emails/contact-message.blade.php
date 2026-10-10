Pesan baru dari form Box, Box di portofolio.

Nama  : {{ $contactMessage->name }}
Email : {{ $contactMessage->email }}
Waktu : {{ $contactMessage->created_at?->format('d M Y, H:i') }}

{{ $contactMessage->message }}

—
Balas email ini untuk langsung membalas pengirim.
Semua pesan juga tersimpan di admin: {{ url('/admin/pesan-masuk') }}
