<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $photos = GalleryPhoto::where('is_active', true)
            ->orderBy('display_order')
            ->orderByDesc('created_at')
            ->get();

        $reviews = $this->fetchGoogleReviews();

        return view('users.index', [
            'photos' => $photos,
            'reviews' => $reviews,
        ]);
    }

    private function fetchGoogleReviews(): array
    {
        $apiKey = config('services.google_places.api_key');
        $placeId = config('services.google_places.place_id');
        $limit = (int) config('services.google_places.reviews_limit', 6);

        if (empty($apiKey) || empty($placeId)) {
            return [];
        }

        $cacheKey = 'google_reviews_' . md5($placeId);

        $rawReviews = Cache::remember($cacheKey, now()->addHour(), function () use ($apiKey, $placeId) {
            $endpoint = 'https://maps.googleapis.com/maps/api/place/details/json';
            $response = Http::timeout(10)->get($endpoint, [
                'place_id' => $placeId,
                'fields' => 'reviews',
                'key' => $apiKey,
            ]);

            if (!$response->ok()) {
                return [];
            }

            $data = $response->json();
            $reviews = $data['result']['reviews'] ?? [];
            return is_array($reviews) ? $reviews : [];
        });

        // Transform, filter, sort, and limit
        $filtered = collect($rawReviews)
            ->filter(function ($review) {
                $hasText = isset($review['text']) && Str::of($review['text'])->trim()->isNotEmpty();
                $ratingOk = isset($review['rating']) && (int)$review['rating'] > 3;
                return $hasText && $ratingOk;
            })
            ->sortByDesc(function ($review) {
                return (int) ($review['time'] ?? 0);
            })
            ->take($limit)
            ->map(function ($review) {
                $timestamp = (int) ($review['time'] ?? 0);
                return [
                    'author_name' => $review['author_name'] ?? 'Anonymous',
                    'rating' => (int) ($review['rating'] ?? 0),
                    'text' => $review['text'] ?? '',
                    'date' => $timestamp > 0 ? date('M d, Y', $timestamp) : '',
                ];
            })
            ->values()
            ->all();

        return $filtered;
    }
}