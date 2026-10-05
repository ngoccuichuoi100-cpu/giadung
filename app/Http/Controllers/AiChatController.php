<?php

namespace App\Http\Controllers;

use App\Services\AiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    protected AiChatService $aiService;

    public function __construct(AiChatService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle incoming user message to AI Chat
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'nullable|string|in:user,assistant,model',
            'history.*.content' => 'nullable|string|max:2000',
        ]);

        $message = trim($validated['message']);
        $history = $validated['history'] ?? [];

        try {
            $response = $this->aiService->chat($message, $history);

            return response()->json([
                'success' => true,
                'reply' => $response['reply'],
                'products' => $response['products'] ?? [],
                'suggestions' => $response['suggestions'] ?? [],
            ]);
        } catch (\Throwable $e) {
            Log::error('AiChatController error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'success' => true,
                'reply' => 'Dạ GiaDụng Pro xin chào bạn! Em là Trợ lý AI GiaDụng. Hiện tại em đang xử lý hơi nhiều câu hỏi, bạn có thể gửi lại hoặc cho em biết bạn cần tìm sản phẩm nào (ví dụ: nồi chiên, nồi cơm, máy xay sinh tố...) để em hỗ trợ tư vấn nhé!',
                'products' => [],
                'suggestions' => [
                    'Tư vấn nồi chiên không dầu',
                    'Nồi cơm điện cho gia đình',
                    'Chính sách bảo hành & đổi trả',
                    'Sản phẩm bán chạy nhất',
                ],
            ]);
        }
    }

    /**
     * Get initial quick suggestions
     */
    public function getSuggestions(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'suggestions' => [
                'Tư vấn nồi chiên không dầu phù hợp',
                'Nồi cơm điện cho gia đình 4-6 người',
                'Chính sách bảo hành & đổi trả thế nào?',
                'Top thiết bị gia dụng bán chạy nhất',
            ],
        ]);
    }
}
