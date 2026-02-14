<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SpeechAnalysisService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.speech_analysis.base_url'), '/');
        $this->timeout = (int) config('services.speech_analysis.timeout', 120);
    }

    /**
     * Send an audio file to the speech analysis API and return the parsed response.
     *
     * @param  string  $audioPath  Absolute path to the audio file on disk
     * @param  string  $level      Challenge level name (e.g. "Beginner")
     * @param  string  $category   Challenge category name (e.g. "Public Speaking")
     * @param  string  $title      Challenge title
     * @return array               The full JSON response from the API
     *
     * @throws RuntimeException
     */
    public function analyze(string $audioPath, string $level, string $category, string $title): array
    {
        $mimeType = mime_content_type($audioPath) ?: 'application/octet-stream';

        $response = Http::timeout($this->timeout)
            ->attach('file', fopen($audioPath, 'r'), basename($audioPath), ['Content-Type' => $mimeType])
            ->post("{$this->baseUrl}/api/v1/transcribe", [
                'level'    => $level,
                'category' => $category,
                'title'    => $title,
            ]);

        if ($response->failed()) {
            Log::error('Speech analysis API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new RuntimeException(
                "Speech analysis API returned HTTP {$response->status()}: {$response->body()}"
            );
        }

        return $response->json();
    }

    /**
     * Build a human-readable feedback string from the API response.
     */
    public function buildFeedback(array $analysis): string
    {
        $lines = [];

        $lines[] = "Overall Rating: {$analysis['overall_rating']}";
        $lines[] = "Confidence Score: {$analysis['confidence_score']}/100";
        $lines[] = "Fluency Score: {$analysis['fluency_score']}/100";
        $lines[] = "Words Per Minute: {$analysis['wpm']}";
        $lines[] = "Word Count: {$analysis['word_count']}";
        $lines[] = "Duration: {$analysis['duration_seconds']}s";
        $lines[] = "Filler Words: {$analysis['filler_count']}";
        $lines[] = "Total Pauses: {$analysis['total_pauses']}";
        $lines[] = "Total Hesitations: {$analysis['total_hesitations']}";

        if (!empty($analysis['recommendations'])) {
            $lines[] = '';
            $lines[] = 'Recommendations:';
            foreach ($analysis['recommendations'] as $i => $rec) {
                $lines[] = ($i + 1) . ". {$rec}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Extract the metadata array to persist alongside the result.
     */
    public function buildMetadata(array $analysis): array
    {
        return [
            'improved_text'          => $analysis['improved_text'] ?? null,
            'tts_speech'             => $analysis['tts_speech'] ?? null,
            'cleaned_text'           => $analysis['cleaned_text'] ?? null,
            'filler_words'           => $analysis['filler_words'] ?? [],
            'filler_count'           => $analysis['filler_count'] ?? 0,
            'duration_seconds'       => $analysis['duration_seconds'] ?? 0,
            'word_count'             => $analysis['word_count'] ?? 0,
            'wpm'                    => $analysis['wpm'] ?? 0,
            'total_pauses'           => $analysis['total_pauses'] ?? 0,
            'total_hesitations'      => $analysis['total_hesitations'] ?? 0,
            'pause_durations'        => $analysis['pause_durations'] ?? [],
            'average_pause_duration' => $analysis['average_pause_duration'] ?? 0,
            'total_pause_time'       => $analysis['total_pause_time'] ?? 0,
            'hesitation_words'       => $analysis['hesitation_words'] ?? [],
            'fluency_score'          => $analysis['fluency_score'] ?? 0,
            'pause_ratio'            => $analysis['pause_ratio'] ?? 0,
            'hesitation_rate'        => $analysis['hesitation_rate'] ?? 0,
            'wpm_score'              => $analysis['wpm_score'] ?? 0,
            'filler_score'           => $analysis['filler_score'] ?? 0,
            'pause_score'            => $analysis['pause_score'] ?? 0,
            'hesitation_score'       => $analysis['hesitation_score'] ?? 0,
            'overall_rating'         => $analysis['overall_rating'] ?? null,
            'recommendations'        => $analysis['recommendations'] ?? [],
        ];
    }
}
