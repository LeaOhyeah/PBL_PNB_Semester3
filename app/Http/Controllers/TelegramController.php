<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function webhook()
    {
        $updates = Telegram::getWebhookUpdate();
        $chatId = $updates->getMessage()->getChat()->getId();
        $message = $updates->getMessage()->getText();

        if ($message === '/start') {
            $this->sendStartTutorial($chatId);
            return;
        }

        if ($this->handleEmailPasswordRegistration($message, $chatId)) {
            return;
        }

        $user = User::where('telegram_id', $chatId)->first();

        if (!$user) {
            $this->handlePublicCommands($message, $chatId);
            return;
        }

        $this->handleUserCommands($message, $chatId, $user);
    }

    
    private function sendStartTutorial($chatId)
    {
        $tutorial = "Selamat datang di bot kami! Berikut adalah panduan penggunaan bot ini:\n\n" .
            "1. Untuk registrasi telegram dengan portal berita, kirim pesan dengan format: email:contoh@contoh.com password:contoh\n" .
            "2. Anda dapat menggunakan perintah berikut:\n" .
            "   - /beritabaru: Lihat 5 berita terbaru.\n" .
            "   - /kategori: Lihat semua kategori.\n" .
            "   - /tag: Lihat semua tag.\n\n" .
            "Perintah tambahan untuk pengguna terdaftar:\n" .
            "   - /beritasayamingguini: Lihat berita Anda dalam minggu ini.\n" .
            "   - /beritasayabulanini: Lihat berita Anda bulan ini.\n" .
            "   - /buatberita: Buat berita baru dengan judul, deskripsi, kategori, dan tag.";

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $tutorial,
        ]);
    }


    private function handleEmailPasswordRegistration($message, $chatId)
    {
        if (preg_match('/^email:(?<email>[^\s]+) password:(?<password>.+)$/', $message, $matches)) {
            $email = $matches['email'];
            $password = $matches['password'];

            $user = User::where('email', $email)->first();

            if ($user && Hash::check($password, $user->password)) {
                $user->update(['telegram_id' => $chatId]);

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "<b>Registrasi berhasil!</b> Akun Anda telah terhubung dengan Telegram ID Anda. Kami menyarankan untuk menghapus pesan registrasi yang berisi email dan password untuk alasan keamanan",
                    'parse_mode' => 'HTML'
                ]);
            } else {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Email atau password tidak ditemukan. Silakan coba lagi.",
                    'parse_mode' => 'HTML'
                ]);
            }
            return true;
        }
        return false;
    }


    private function handlePublicCommands($message, $chatId)
    {
        switch ($message) {
            case '/beritabaru':
                $latestNews = News::latest()->take(5)->get();
                $responseText = $this->formatPosts($latestNews, "Terbaru");
                break;

            case '/kategori':
                $categories = Category::all('name');
                $responseText = "Kategori yang tersedia:\n" . $categories->pluck('name')->implode("\n");
                break;

            case '/tag':
                $tags = Tag::all('name');
                $responseText = "Tag yang tersedia:\n" . $tags->pluck('name')->implode("\n");
                break;

            default:
                $responseText = 'Perintah tidak dikenal. Gunakan /start untuk melihat daftar perintah atau registrasi telegram untuk perintah tertentu.';
        }

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $responseText,
            'parse_mode' => 'HTML'
        ]);
    }

    private function handleUserCommands($message, $chatId, $user)
    {
        switch ($message) {
            case '/beritabaru':
                $latestNews = News::latest()->take(5)->get();
                $responseText = $this->formatPosts($latestNews, "Terbaru");
                break;

            case '/kategori':
                $categories = Category::all('name');
                $responseText = "Kategori yang tersedia:\n" . $categories->pluck('name')->implode("\n");
                break;

            case '/tag':
                $tags = Tag::all('name');
                $responseText = "Tag yang tersedia:\n" . $tags->pluck('name')->implode("\n");
                break;

            case '/beritasayamingguini':
                $posts = $user->news()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
                $responseText = $this->formatPosts($posts, "Minggu Ini");
                break;

            case '/beritasayabulanini':
                $posts = $user->news()->whereMonth('created_at', now()->month)->get();
                $responseText = $this->formatPosts($posts, "Bulan Ini");
                break;

            case '/buatberita':
                $responseText = "Untuk membuat berita, kirim dengan format:\n" .
                    "judul: (judul berita) \ndeskripsi: (deskripsi) \nkategori: (kategori) \nurl: (tautan video YouTube) \ntag: (tag1, tag2)";
                break;

            default:
                if ($this->handleCreateNews($message, $user)) {
                    return;
                }
                $responseText = 'Perintah tidak dikenal.';
        }

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $responseText,
            'parse_mode' => 'HTML'
        ]);
    }

    private function handleCreateNews($message, $user)
    {
        if (preg_match('/judul:\s*(?<title>.+?)\s+deskripsi:\s*(?<description>.+?)\s+kategori:\s*(?<category>.+?)\s+url:\s*(?<url>[^\s]+)\s+tag:\s*(?<tags>.+)/is', $message, $matches)) {
            $title = trim($matches['title']);
            $description = trim($matches['description']);
            $category = trim($matches['category']);
            $url = trim($matches['url']);
            $tags = array_map('trim', explode(',', $matches['tags']));

            if (count($tags) > 3) {
                Telegram::sendMessage([
                    'chat_id' => $user->telegram_id,
                    'text' => 'Maksimal 3 tag yang diperbolehkan.',
                ]);
                return true;
            }

            $youtubeId = $this->extractYoutubeId($url);
            if (!$youtubeId) {
                Telegram::sendMessage([
                    'chat_id' => $user->telegram_id,
                    'text' => 'Tautan YouTube tidak valid.',
                ]);
                return true;
            }

            $validCategory = $this->getCategoryId($category);
            if (!$validCategory) {
                Telegram::sendMessage([
                    'chat_id' => $user->telegram_id,
                    'text' => 'Kategori tidak valid.',
                ]);
                return true;
            }

            $existingTags = Tag::whereIn('name', $tags)->pluck('id', 'name')->toArray();
            $invalidTags = array_diff($tags, array_keys($existingTags));

            if (!empty($invalidTags)) {
                Telegram::sendMessage([
                    'chat_id' => $user->telegram_id,
                    'text' => 'Tag tidak valid: ' . implode(', ', $invalidTags),
                ]);
                return true;
            }

            $news = new News([
                'title' => $title,
                'description' => $description,
                'content_url' => $youtubeId,
                'category_id' => $validCategory,
            ]);

            $user->news()->save($news);

            $news->tags()->sync(array_values($existingTags));

            Telegram::sendMessage([
                'chat_id' => $user->telegram_id,
                'text' => 'Berita berhasil dibuat!',
                'parse_mode' => 'HTML',
            ]);

            return true;
        }

        // Jika format tidak sesuai
        Telegram::sendMessage([
            'chat_id' => $user->telegram_id,
            'text' => 'Format pesan tidak sesuai. Gunakan format berikut:' . PHP_EOL .
                'judul: <judul>' . PHP_EOL .
                'deskripsi: <deskripsi>' . PHP_EOL .
                'kategori: <kategori>' . PHP_EOL .
                'url: <tautan YouTube>' . PHP_EOL .
                'tag: <tag1, tag2, tag3>',
        ]);

        return false;
    }


    private function extractYoutubeId($url)
    {
        if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
            return $videoId;
        }
        return null;
    }


    private function getCategoryId($category)
    {
        return Category::where('name', $category)->value('id');
    }


    private function formatPosts($posts, $label)
    {
        if ($posts->isEmpty()) {
            return 'Tidak ada postingan ditemukan.';
        }

        $response = "Postingan {$label} :\n\n";
        foreach ($posts as $post) {
            $response .= "<b>{$post->title}</b>\n";
            $response .= "Link: https://youtu.be/{$post->content_url}\n\n";
        }

        return $response;
    }


    public function setBotCommands()
    {
        $commands = [
            ['command' => 'start', 'description' => 'Mulai bot'],
            ['command' => 'beritabaru', 'description' => 'Lihat berita terbaru'],
            ['command' => 'kategori', 'description' => 'Lihat semua kategori tersedia'],
            ['command' => 'tag', 'description' => 'Lihat semua tag tersedia'],
            ['command' => 'beritasayamingguini', 'description' => 'Lihat postingan anda minggu ini'],
            ['command' => 'beritasayabulanini', 'description' => 'Lihat postingan anda bulan ini'],
            ['command' => 'buatberita', 'description' => 'Tutorial membuat berita'],


        ];
        $this->telegram->setMyCommands(['commands' => $commands]);

        return response()->json(['status' => 'success', 'message' => 'Commands updated!']);
    }
}
