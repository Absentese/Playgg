<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupportChatService
{
    public function isAiEnabled(): bool
    {
        return $this->resolveProvider() !== 'fallback';
    }

    public function providerLabel(): string
    {
        return match ($this->resolveProvider()) {
            'openai' => 'AI-ассистент',
            'gemini' => 'AI Gemini',
            'groq' => 'AI Groq',
            'pollinations' => 'AI-ассистент',
            default => 'Умный помощник',
        };
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function reply(string $message, array $history = []): string
    {
        $provider = $this->resolveProvider();

        if ($provider !== 'fallback') {
            $aiReply = match ($provider) {
                'openai' => $this->askOpenAiCompatible($message, $history, config('services.openai')),
                'gemini' => $this->askGemini($message, $history),
                'groq' => $this->askOpenAiCompatible($message, $history, config('services.groq')),
                'pollinations' => $this->askPollinations($message, $history),
                default => null,
            };

            if ($aiReply !== null) {
                return $aiReply;
            }
        }

        return $this->fallbackReply($message);
    }

    private function resolveProvider(): string
    {
        $forced = config('services.support_ai.provider');

        if ($forced && $forced !== 'auto' && $this->providerConfigured($forced)) {
            return $forced;
        }

        if (filled(config('services.openai.key'))) {
            return 'openai';
        }

        if (filled(config('services.gemini.key'))) {
            return 'gemini';
        }

        if (filled(config('services.groq.key'))) {
            return 'groq';
        }

        if (config('services.pollinations.enabled')) {
            return 'pollinations';
        }

        return 'fallback';
    }

    private function providerConfigured(string $provider): bool
    {
        return match ($provider) {
            'openai' => filled(config('services.openai.key')),
            'gemini' => filled(config('services.gemini.key')),
            'groq' => filled(config('services.groq.key')),
            'pollinations' => (bool) config('services.pollinations.enabled'),
            default => false,
        };
    }

    /**
     * @param  array{key?: string, base_url: string, model: string}  $config
     * @param  array<int, array{role: string, content: string}>  $history
     */
    private function askOpenAiCompatible(string $message, array $history, array $config): ?string
    {
        if (empty($config['key'])) {
            return null;
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ...array_slice($history, -8),
            ['role' => 'user', 'content' => $message],
        ];

        try {
            $response = Http::withToken($config['key'])
                ->timeout(30)
                ->post(rtrim($config['base_url'], '/').'/chat/completions', [
                    'model' => $config['model'],
                    'messages' => $messages,
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $this->cleanAiText($response->json('choices.0.message.content'));
            }
        } catch (\Throwable) {
            //
        }

        return null;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    private function askGemini(string $message, array $history): ?string
    {
        $key = config('services.gemini.key');
        if (! $key) {
            return null;
        }

        $model = config('services.gemini.model');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $contents = [];
        foreach (array_slice($history, -6) as $item) {
            $role = $item['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = ['role' => $role, 'parts' => [['text' => $item['content']]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        try {
            $response = Http::timeout(30)
                ->withHeaders(['x-goog-api-key' => $key])
                ->post($url, [
                    'systemInstruction' => ['parts' => [['text' => $this->systemPrompt()]]],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 500,
                    ],
                ]);

            if ($response->successful()) {
                return $this->cleanAiText($response->json('candidates.0.content.parts.0.text'));
            }
        } catch (\Throwable) {
            //
        }

        return null;
    }

    /**
     * Бесплатный анонимный API: https://text.pollinations.ai
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    private function askPollinations(string $message, array $history): ?string
    {
        $prompt = $this->buildPollinationsPrompt($message, $history);

        try {
            $client = Http::timeout(45);

            if (! config('services.pollinations.verify_ssl')) {
                $client = $client->withoutVerifying();
            }

            $response = $client->get(rtrim(config('services.pollinations.base_url'), '/').'/'.rawurlencode($prompt), [
                'model' => config('services.pollinations.model'),
            ]);

            if ($response->successful()) {
                $text = $this->cleanAiText($response->body());

                if ($text !== null && ! str_starts_with($text, '⚠️')) {
                    return $text;
                }
            }
        } catch (\Throwable) {
            //
        }

        return null;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    private function buildPollinationsPrompt(string $message, array $history): string
    {
        $lines = [
            $this->systemPrompt(),
            '',
            'Веди диалог на русском языке. Отвечай кратко (2–4 предложения).',
        ];

        foreach (array_slice($history, -6) as $item) {
            $prefix = $item['role'] === 'assistant' ? 'Ассистент' : 'Клиент';
            $lines[] = "{$prefix}: {$item['content']}";
        }

        $lines[] = "Клиент: {$message}";
        $lines[] = 'Ассистент:';

        return implode("\n", $lines);
    }

    private function cleanAiText(mixed $text): ?string
    {
        if (! is_string($text)) {
            return null;
        }

        $text = trim($text);

        return $text !== '' ? $text : null;
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Ты — виртуальный ассистент интернет-магазина цифровых видеоигр playgg (Россия).
Отвечай кратко, дружелюбно и по делу на русском языке.
Помогай с: каталогом игр, ключами Steam/Epic, доставкой ключей на email, оплатой, гарантией, пополнением Steam, заказами и контактами.
Факты о магазине:
- Ключи доставляются на email в течение 5–15 минут после оплаты.
- Платформы: Steam, Epic Games, GOG, Ubisoft Connect.
- Телефон: +7 (999) 100-20-30, email: info@playgg.ru.
- Гарантия подлинности ключей от официальных дистрибьюторов.
Если вопрос требует действий менеджера — предложи связаться по телефону или через страницу «Контакты».
Не выдумывай цены и наличие конкретных игр — направляй в каталог.
PROMPT;
    }

    private function fallbackReply(string $message): string
    {
        $text = Str::lower($message);

        if (Str::contains($text, ['привет', 'здравств', 'добрый', 'hi', 'hello'])) {
            return 'Здравствуйте! Я ассистент playgg. Помогу с выбором игр, активацией ключей, заказом или отвечу на вопросы о магазине. Чем могу помочь?';
        }

        if (Str::contains($text, ['доставк', 'ключ', 'email', 'получ', 'отправ'])) {
            return 'Цифровые ключи доставляются на email в течение 5–15 минут после оплаты. Также ключи доступны в личном кабинете в разделе «Мои заказы». Подробнее — на странице «Доставка ключей».';
        }

        if (Str::contains($text, ['гарант', 'подлин', 'оригинал', 'поддел', 'скам'])) {
            return 'Все ключи приобретаются у официальных дистрибьюторов. На каждый ключ действует гарантия активации. Подробности — в разделе «Гарантия».';
        }

        if (Str::contains($text, ['steam', 'стим', 'пополн', 'кошел', 'wallet'])) {
            return 'Пополнение кошелька Steam доступно в разделе «Пополнение Steam». Комиссия от 0%, зачисление за 1–5 минут. Минимальная сумма — 100 ₽.';
        }

        if (Str::contains($text, ['оплат', 'карт', 'налич', 'рассроч', 'сбп'])) {
            return 'Оплата доступна банковской картой и СБП при оформлении заказа. После оплаты ключ отправляется автоматически. Вопросы по оплате: +7 (999) 100-20-30.';
        }

        if (Str::contains($text, ['заказ', 'оформ', 'купить', 'корзин', 'каталог', 'игр'])) {
            return 'Чтобы купить игру: выберите товар в каталоге, добавьте в корзину, войдите в аккаунт и оформите заказ. После оплаты ключ придёт на email.';
        }

        if (Str::contains($text, ['скидк', 'распродаж', 'акци', 'дешев'])) {
            return 'Актуальные скидки — в каталоге с фильтром «Со скидкой» и на главной в блоке «Лучшие скидки». Скидки до 90% на хиты продаж.';
        }

        if (Str::contains($text, ['контакт', 'телефон', 'почт', 'адрес', 'где вы', 'находит', 'связать'])) {
            return 'Контакты playgg: телефон +7 (999) 100-20-30, email info@playgg.ru. Также форма на странице «Контакты».';
        }

        if (Str::contains($text, ['возврат', 'обмен', 'вернут'])) {
            return 'Возврат цифровых товаров возможен до активации ключа. Для индивидуальной ситуации: +7 (999) 100-20-30 или info@playgg.ru.';
        }

        if (Str::contains($text, ['спасибо', 'благодар'])) {
            return 'Рад помочь! Если появятся ещё вопросы — пишите. Приятной игры!';
        }

        return 'Спасибо за вопрос! Я могу подсказать по каталогу, доставке ключей, оплате, гарантии и пополнению Steam. Уточните, что вас интересует — или позвоните: +7 (999) 100-20-30.';
    }
}
