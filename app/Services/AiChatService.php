<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
    }

    /**
     * Process message and return AI reply + matching products
     */
    public function chat(string $userMessage, array $history = []): array
    {
        $matchedProducts = $this->searchRelevantProducts($userMessage);
        $formattedProducts = $this->formatProducts($matchedProducts);

        // Try Gemini API if key is set
        if (!empty($this->apiKey)) {
            try {
                $reply = $this->callGeminiApi($userMessage, $history, $matchedProducts);
                if (!empty($reply)) {
                    return [
                        'reply' => $reply,
                        'products' => $formattedProducts,
                        'suggestions' => $this->getDynamicSuggestions($userMessage),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini API call failed, falling back to local responder: ' . $e->getMessage());
            }
        }

        // High-intelligence localized fallback responder
        $reply = $this->localResponder($userMessage, $matchedProducts);

        return [
            'reply' => $reply,
            'products' => $formattedProducts,
            'suggestions' => $this->getDynamicSuggestions($userMessage),
        ];
    }

    /**
     * Query relevant products in the database based on message keywords
     */
    protected function searchRelevantProducts(string $message): array
    {
        $cleanMessage = mb_strtolower(trim($message));

        // 1. If asking what the shop sells / catalog overview, return top featured products to showcase
        if (
            str_contains($cleanMessage, 'chuyên bán') ||
            str_contains($cleanMessage, 'bán sản phẩm j') ||
            str_contains($cleanMessage, 'bán sản phẩm gì') ||
            str_contains($cleanMessage, 'bán cái gì') ||
            str_contains($cleanMessage, 'bán gì á') ||
            str_contains($cleanMessage, 'bán gì thế') ||
            str_contains($cleanMessage, 'bán gì vậy') ||
            str_contains($cleanMessage, 'bán đồ gì') ||
            str_contains($cleanMessage, 'bán những gì') ||
            str_contains($cleanMessage, 'mặt hàng gì') ||
            str_contains($cleanMessage, 'danh mục') ||
            str_contains($cleanMessage, 'kinh doanh gì')
        ) {
            $products = Product::where('status', 'active')->where('is_featured', true)->limit(4)->get();
            if ($products->isEmpty()) {
                $products = Product::where('status', 'active')->latest()->limit(4)->get();
            }
            return $products->all();
        }

        // Skip product search if the message is purely conversational or off-topic
        $pureConversationalPhrases = [
            'chào', 'xin chào', 'hello', 'hi', 'alo', 'bạn là ai', 'bạn tên gì', 
            'bot à', 'ai đó', 'có ai không', 'cảm ơn', 'tạm biệt', 'bye',
            'bảo hành', 'đổi trả', 'chính sách', 'đơn hàng', 'kiểm tra đơn',
            'giao hàng', 'vận chuyển', 'phí ship', 'địa chỉ', 'ở đâu', 'hotline',
            'liên hệ', 'thanh toán', 'chuyển khoản', 'cod', 'payos',
            'ăn cơm chưa', 'có người yêu chưa', 'kể chuyện cười', 'hát đi',
            'đố vui', 'buồn quá', 'chúc ngủ ngon', 'hôm nay ăn gì', '1+1'
        ];

        foreach ($pureConversationalPhrases as $phrase) {
            if ($cleanMessage === $phrase || $cleanMessage === $phrase . ' bạn' || $cleanMessage === $phrase . ' shop' || $cleanMessage === $phrase . ' ad') {
                return [];
            }
        }

        // Specific category & product dictionary
        $categoryMap = [
            'nồi chiên không dầu' => ['nồi chiên không dầu', 'nồi chiên'],
            'nồi cơm điện' => ['nồi cơm điện', 'nồi cơm'],
            'máy xay sinh tố' => ['máy xay sinh tố', 'máy xay'],
            'chảo chống dính' => ['chảo chống dính', 'chảo'],
            'ấm siêu tốc' => ['ấm siêu tốc', 'bình siêu tốc', 'bình đun'],
            'bếp từ' => ['bếp từ', 'bếp điện từ'],
            'bếp hồng ngoại' => ['bếp hồng ngoại'],
            'nồi lẩu' => ['nồi lẩu', 'nồi lẩu điện'],
            'nồi áp suất' => ['nồi áp suất'],
            'máy ép chậm' => ['máy ép chậm', 'máy ép'],
            'robot hút bụi' => ['robot hút bụi', 'máy hút bụi'],
            'máy lọc không khí' => ['máy lọc không khí', 'lọc khí'],
            'máy lọc nước' => ['máy lọc nước'],
            'quạt' => ['quạt điều hòa', 'quạt đứng', 'quạt cây', 'quạt'],
            'bàn là' => ['bàn là', 'bàn ủi'],
            'tủ lạnh' => ['tủ lạnh'],
            'máy giặt' => ['máy giặt'],
            'lò vi sóng' => ['lò vi sóng', 'lò nướng'],
        ];

        // Brands dictionary
        $brands = ['philips', 'sunhouse', 'tefal', 'lock&lock', 'lock and lock', 'cuckoo', 'toshiba', 'xiaomi', 'sharp', 'panasonic'];

        $matchedKeyword = null;

        // 1. Check exact appliance categories (multi-word first)
        foreach ($categoryMap as $key => $synonyms) {
            foreach ($synonyms as $syn) {
                // Ensure boundary check: don't match "chảo" if the word is actually "chào"
                if ($syn === 'chảo') {
                    if (preg_match('/\bchảo\b/u', $cleanMessage)) {
                        $matchedKeyword = 'chảo';
                        break 2;
                    }
                } elseif (str_contains($cleanMessage, $syn)) {
                    $matchedKeyword = $syn;
                    break 2;
                }
            }
        }

        // 2. Check brands if no category was matched
        if (!$matchedKeyword) {
            foreach ($brands as $brand) {
                if (str_contains($cleanMessage, $brand)) {
                    $matchedKeyword = $brand;
                    break;
                }
            }
        }

        $query = Product::where('status', 'active');

        if ($matchedKeyword) {
            return $query->where(function ($q) use ($matchedKeyword) {
                $q->where('name', 'like', "%{$matchedKeyword}%")
                  ->orWhere('short_description', 'like', "%{$matchedKeyword}%");
            })->limit(4)->get()->all();
        }

        // 3. Check for featured / best sellers / standout if user asks for recommendations
        if (
            str_contains($cleanMessage, 'nổi trội') ||
            str_contains($cleanMessage, 'nổi bật') ||
            str_contains($cleanMessage, 'bán chạy') ||
            str_contains($cleanMessage, 'ưa chuộng') ||
            str_contains($cleanMessage, 'chuộng nhất') ||
            str_contains($cleanMessage, 'top sản phẩm') ||
            str_contains($cleanMessage, 'tiêu biểu') ||
            str_contains($cleanMessage, 'tốt nhất') ||
            str_contains($cleanMessage, 'đáng mua') ||
            str_contains($cleanMessage, 'chủ lực') ||
            (str_contains($cleanMessage, 'hot') && !str_contains($cleanMessage, 'hotline')) ||
            str_contains($cleanMessage, 'khuyến mãi') ||
            str_contains($cleanMessage, 'giảm giá') ||
            str_contains($cleanMessage, 'ưu đãi') ||
            str_contains($cleanMessage, 'gợi ý') ||
            str_contains($cleanMessage, 'đề xuất') ||
            str_contains($cleanMessage, 'khuyên dùng') ||
            preg_match('/(có|những|các)\s+sản\s+phẩm\s+nào/u', $cleanMessage)
        ) {
            $products = Product::where('status', 'active')->where('is_featured', true)->limit(4)->get();
            if ($products->isEmpty()) {
                $products = Product::where('status', 'active')->latest()->limit(4)->get();
            }
            return $products->all();
        }

        return [];
    }

    /**
     * Format product data for frontend presentation
     */
    protected function formatProducts(array $products): array
    {
        return array_map(function ($product) {
            $price = (float) $product->price;
            $salePrice = $product->sale_price ? (float) $product->sale_price : null;
            $hasSale = $salePrice && $salePrice < $price;

            $thumb = $product->thumbnail;
            if ($thumb && !str_starts_with($thumb, 'http')) {
                $thumbUrl = asset('storage/' . $thumb);
            } elseif ($thumb) {
                $thumbUrl = $thumb;
            } else {
                $thumbUrl = asset('images/no-image.png');
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => number_format($price, 0, ',', '.') . '₫',
                'sale_price' => $hasSale ? number_format($salePrice, 0, ',', '.') . '₫' : null,
                'discount_percent' => $hasSale ? round((($price - $salePrice) / $price) * 100) : 0,
                'thumbnail' => $thumbUrl,
                'url' => route('products.show', $product->slug),
                'stock' => $product->stock_quantity,
            ];
        }, $products);
    }

    /**
     * Build prompt and invoke Google Gemini API with alternating turns
     */
    protected function callGeminiApi(string $userMessage, array $history, array $matchedProducts): ?string
    {
        $systemPrompt = $this->buildSystemPrompt($matchedProducts);

        // Format history for Gemini API with STRICT alternating turn guarantees
        $contents = [];
        $recentHistory = array_slice($history, -8);
        $lastRole = null;

        foreach ($recentHistory as $msg) {
            $rawRole = $msg['role'] ?? 'user';
            $role = ($rawRole === 'user') ? 'user' : 'model';
            $text = trim($msg['content'] ?? '');

            if ($text === '') {
                continue;
            }

            // Prevent consecutive duplicate roles
            if ($role === $lastRole) {
                $lastIndex = count($contents) - 1;
                $contents[$lastIndex]['parts'][0]['text'] .= "\n" . $text;
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $text]],
            ];
            $lastRole = $role;
        }

        // Check if the very last message in contents is already the current user message
        $alreadyAppended = false;
        if (!empty($contents)) {
            $lastIndex = count($contents) - 1;
            if ($contents[$lastIndex]['role'] === 'user' && trim($contents[$lastIndex]['parts'][0]['text']) === $userMessage) {
                $alreadyAppended = true;
            }
        }

        if (!$alreadyAppended) {
            if ($lastRole === 'user') {
                $lastIndex = count($contents) - 1;
                $contents[$lastIndex]['parts'][0]['text'] .= "\n" . $userMessage;
            } else {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $userMessage]],
                ];
            }
        }

        // Gemini API requires the first turn to have role 'user'
        while (!empty($contents) && $contents[0]['role'] !== 'user') {
            array_shift($contents);
        }

        if (empty($contents)) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]],
            ];
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 800,
            ],
        ];

        $modelsToTry = array_unique([$this->model, 'gemini-1.5-flash', 'gemini-2.0-flash']);

        foreach ($modelsToTry as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";

            try {
                $response = Http::timeout(10)->post($endpoint, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($reply)) {
                        return trim($reply);
                    }
                } else {
                    Log::warning("Gemini model {$model} API response error: " . $response->body());
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini model {$model} request exception: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Construct rich system prompt with store information & database context
     */
    protected function buildSystemPrompt(array $matchedProducts): string
    {
        $productContext = '';
        if (!empty($matchedProducts)) {
            $productContext .= "\nDANH SÁCH SẢN PHẨM PHÙ HỢP HIỆN CÓ TẠI CỬA HÀNG:\n";
            foreach ($matchedProducts as $p) {
                $price = number_format($p->sale_price ?: $p->price, 0, ',', '.') . '₫';
                $productContext .= "- {$p->name} | Giá: {$price} | Còn: {$p->stock_quantity} cái | Chi tiết: {$p->short_description}\n";
            }
        }

        return <<<PROMPT
Bạn là "GiaDụng AI" - trợ lý tư vấn bán hàng thông minh, thân thiện và hiểu biết của cửa hàng GiaDụng Pro.
Bạn có khả năng tư vấn thiết bị gia dụng chính xác, giải đáp chính sách cửa hàng, đồng thời trò chuyện vui vẻ, dí dỏm về các câu hỏi đời sống, mẹo nhà bếp, hoặc câu hỏi ngoài lề của khách hàng.

THÔNG TIN CỬA HÀNG GIADỤNG PRO:
- Tên shop: GiaDụng Pro - Hệ thống bán lẻ thiết bị gia dụng nhà bếp & đời sống cao cấp chính hãng.
- Danh mục sản phẩm: Nồi chiên không dầu, Nồi cơm điện cao tần, Máy xay sinh tố, Nồi & chảo chống dính, Ấm siêu tốc, Nồi lẩu điện, Bếp từ, Robot hút bụi, Máy lọc không khí, Máy lọc nước, Tủ lạnh, Máy giặt, Quạt điện cao cấp...
- Thương hiệu đối tác chính hãng: Philips, Tefal, Sunhouse, Panasonic, Cuckoo, Xiaomi, Toshiba, Sharp, Daikin, Samsung, LG, Aqua.
- Cam kết chất lượng: 100% hàng chính hãng nhập khẩu chính ngạch, có tem kiểm định, xuất hóa đơn VAT điện tử.
- Chính sách bảo hành: Bảo hành chính hãng 12 - 24 tháng theo từng hãng.
- Chính sách đổi mới: 1 đổi 1 trong 30 ngày nếu có lỗi kỹ thuật từ nhà sản xuất.
- Vận chuyển: Miễn phí giao hàng toàn quốc cho đơn từ 500.000₫. Giao hỏa tốc nội thành 2h.
- Thanh toán: Quét mã VietQR qua PayOS xác nhận tức thì, Chuyển khoản ngân hàng, hoặc nhận hàng thanh toán COD.
- Giờ mở cửa: Showroom hoạt động từ 8:00 đến 21:30 hàng ngày. Website nhận đặt hàng 24/7. Hotline: 1900 8888.

QUY TẮC TRÒ CHUYỆN:
1. Xưng hô: Xưng "Em" hoặc "GiaDụng Pro", gọi khách là "Anh/Chị" hoặc "Bạn". Luôn lịch sự, ấm áp, tích cực.
2. Trả lời đúng trọng tâm:
   - Nếu khách hỏi về sản phẩm (nổi trội, bán chạy, gợi ý, tính năng, giá cả, hỏi cửa hàng có bán gì): Hãy tập trung tư vấn sản phẩm từ danh sách sản phẩm hoặc các dòng chủ lực của shop. Đính kèm thông tin rõ ràng.
   - Chỉ trả lời về địa chỉ showroom và giờ mở cửa khi khách hỏi rõ về địa chỉ, showroom, nơi mua trực tiếp hoặc giờ làm việc. Tuyệt đối KHÔNG nhầm lẫn các câu hỏi có từ "cửa hàng mình...", "shop mình..." là hỏi về địa chỉ showroom.
   - Nếu khách hỏi chuyện ngoài lề (hỏi thăm, chuyện cười, thời tiết, món ăn, đố vui): Hãy trả lời dí dỏm, thông minh, tạo tiếng cười và thiện cảm, sau đó có thể chúc khách một ngày tốt lành!
3. Tuyệt đối không bịa đặt các chương trình khuyến mãi hay sản phẩm không có thật.
{$productContext}
PROMPT;
    }

    /**
     * Rule-based fallback when Gemini API key is missing or fails
     */
    protected function localResponder(string $message, array $matchedProducts): string
    {
        $clean = mb_strtolower(trim($message));

        // =========================================================================
        // NHÓM 0: SẢN PHẨM NỔI TRỘI / NỔI BẬT / BÁN CHẠY / GỢI Ý ĐẶC BIỆT
        // (Ưu tiên cao nhất khi khách hỏi về sản phẩm nổi trội, bán chạy, hot)
        // =========================================================================
        $isFeaturedInquiry = (
            str_contains($clean, 'nổi trội') ||
            str_contains($clean, 'nổi bật') ||
            str_contains($clean, 'bán chạy') ||
            str_contains($clean, 'ưa chuộng') ||
            str_contains($clean, 'chuộng nhất') ||
            str_contains($clean, 'top sản phẩm') ||
            str_contains($clean, 'tiêu biểu') ||
            str_contains($clean, 'tốt nhất') ||
            str_contains($clean, 'đáng mua') ||
            str_contains($clean, 'chủ lực') ||
            (str_contains($clean, 'hot') && !str_contains($clean, 'hotline')) ||
            preg_match('/sản\s+phẩm\s+nào\s+(nổi|hot|chạy|tốt|hay|được|đáng)/u', $clean)
        );

        if ($isFeaturedInquiry) {
            // Check if user specified a particular appliance category (e.g. "nồi chiên bán chạy")
            $specificAppliances = [
                'nồi chiên', 'nồi cơm', 'máy xay', 'chảo', 'ấm siêu tốc', 'bình siêu tốc',
                'bếp từ', 'bếp điện', 'nồi lẩu', 'nồi áp suất', 'máy ép', 'robot hút bụi',
                'máy hút bụi', 'lọc không khí', 'máy lọc nước', 'quạt', 'bàn là', 'bàn ủi',
                'tủ lạnh', 'máy giặt', 'lò vi sóng', 'lò nướng'
            ];
            $hasSpecificAppliance = false;
            foreach ($specificAppliances as $appliance) {
                if (str_contains($clean, $appliance)) {
                    $hasSpecificAppliance = true;
                    break;
                }
            }

            // If general standout inquiry (e.g. "cửa hàng mình có sản phẩm nào nổi trội nhất")
            if (!$hasSpecificAppliance) {
                $first = !empty($matchedProducts) ? $matchedProducts[0] : null;
                $reply = "Dạ, tại **GiaDụng Pro**, các dòng sản phẩm **nổi trội và được khách hàng săn đón nhiều nhất** hiện nay gồm có: 🔥✨\n\n" .
                         "1. **Nồi chiên không dầu & Nồi chiên hơi nước (Philips, Tefal):** Dòng sản phẩm bán chạy số 1, công nghệ chiên nướng chân không giảm đến 90% dầu mỡ, món ăn giòn ngoài mềm trong.\n" .
                         "2. **Nồi cơm điện cao tần IH (Cuckoo, Toshiba):** Công nghệ cảm ứng từ đối lưu nhiệt giúp từng hạt cơm chín đều, dẻo thơm nguyên vị và giữ ấm suốt cả ngày.\n" .
                         "3. **Máy xay sinh tố đa năng & Máy ép chậm (Philips, Sunhouse):** Động cơ bền bỉ, cối kháng vỡ, xay nhuyễn mịn hoặc ép kiệt bã bảo toàn dưỡng chất.\n" .
                         "4. **Nồi lẩu điện đa năng & Bếp từ (Sunhouse):** Thiết kế hiện đại, gia nhiệt nhanh chóng, cực kỳ lý tưởng cho các bữa tiệc sum họp gia đình.\n\n";

                if ($first) {
                    $price = number_format($first->sale_price ?: $first->price, 0, ',', '.') . '₫';
                    $reply .= "• Tiêu biểu hàng đầu: **{$first->name}** đang có giá cực ưu đãi chỉ **{$price}**.\n\n";
                }

                $reply .= "Em đã gửi danh sách các mẫu sản phẩm nổi bật nhất ngay bên dưới để bạn tiện tham khảo chi tiết thông số và giá tốt nhé! Bạn đang quan tâm dòng thiết bị nào nhất ạ?";
                return $reply;
            }
        }

        // =========================================================================
        // NHÓM 1: CHÍNH SÁCH CỬA HÀNG (Bảo hành, Vận chuyển, Địa chỉ, CSKH, Thanh toán)
        // =========================================================================

        // 1.1. Warranty & Return Policy (Bảo hành, đổi trả 1 đổi 1)
        if (str_contains($clean, 'bảo hành') || str_contains($clean, 'đổi trả') || str_contains($clean, 'chính sách') || str_contains($clean, '1 đổi 1')) {
            return "Dạ, **GiaDụng Pro** cam kết chính sách hậu mãi chu đáo nhất cho khách hàng ạ:\n\n" .
                   "• **Bảo hành chính hãng:** Từ **12 đến 24 tháng** tùy theo từng thương hiệu và dòng sản phẩm.\n" .
                   "• **Đổi mới 1 đổi 1 trong 30 ngày:** Áp dụng ngay nếu thiết bị có lỗi kỹ thuật từ nhà sản xuất.\n" .
                   "• **Miễn phí vận chuyển:** Cho tất cả đơn hàng từ **500.000₫** trên toàn quốc.\n" .
                   "• **Kiểm tra khi nhận hàng:** Khách hàng được đồng kiểm tra sản phẩm trước khi thanh toán.\n\n" .
                   "Bạn đang quan tâm đến sản phẩm nào để em tư vấn thời hạn bảo hành chi tiết hơn nhé?";
        }

        // 1.2. Order tracking & Shipping (Đơn hàng, giao hàng, phí ship)
        if (str_contains($clean, 'đơn hàng') || str_contains($clean, 'kiểm tra đơn') || str_contains($clean, 'tra cứu') || str_contains($clean, 'giao hàng') || str_contains($clean, 'vận chuyển') || str_contains($clean, 'phí ship') || str_contains($clean, 'tiền ship') || str_contains($clean, 'freeship')) {
            return "Dạ, về vấn đề vận chuyển và tiến độ đơn hàng tại GiaDụng Pro:\n\n" .
                   "• **Tra cứu đơn hàng:** Bạn vào mục **Tài khoản > Đơn hàng của tôi** để xem chi tiết trạng thái xử lý và mã vận đơn.\n" .
                   "• **Phí vận chuyển:** **Miễn phí giao hàng toàn quốc (Freeship)** cho mọi đơn từ **500.000₫**.\n" .
                   "• **Thời gian giao hàng:** Nội thành 2 - 4 tiếng (hỏa tốc), các tỉnh thành khác từ 1 - 3 ngày làm việc.\n" .
                   "• **Hỗ trợ trực tiếp:** Bạn cũng có thể gửi Mã đơn hàng ngay tại khung chat này để em kiểm tra tiến độ giúp bạn nhé!";
        }

        // 1.3. Store Address, Showroom, Working Hours (Ở đâu, địa chỉ, giờ mở cửa - KHÔNG dùng từ "cửa hàng" đơn lẻ)
        if (
            str_contains($clean, 'ở đâu') ||
            str_contains($clean, 'địa chỉ') ||
            str_contains($clean, 'vị trí') ||
            str_contains($clean, 'tới đâu mua') ||
            str_contains($clean, 'đến đâu mua') ||
            str_contains($clean, 'qua đâu mua') ||
            str_contains($clean, 'mua ở đâu') ||
            str_contains($clean, 'mua trực tiếp') ||
            str_contains($clean, 'showroom') ||
            str_contains($clean, 'chi nhánh') ||
            str_contains($clean, 'mở cửa') ||
            str_contains($clean, 'đóng cửa') ||
            str_contains($clean, 'giờ mở cửa') ||
            str_contains($clean, 'giờ làm việc') ||
            str_contains($clean, 'thời gian làm việc') ||
            str_contains($clean, 'thời gian hoạt động')
        ) {
            return "Dạ, thông tin địa chỉ và thời gian hoạt động của **GiaDụng Pro** như sau ạ:\n\n" .
                   "• **Hệ thống Showroom chính:** Mở cửa đón khách từ **8:00 đến 21:30** mỗi ngày (từ Thứ 2 đến Chủ Nhật, kể cả ngày lễ Tết).\n" .
                   "• **Mua sắm Online:** Hoạt động **24/7** trên website với hệ thống giao hàng hỏa tốc nội thành 2h và chuyển phát nhanh toàn quốc.\n" .
                   "• **Trải nghiệm thực tế:** Bạn có thể đặt hàng trực tiếp trên web hoặc ghé showroom để được trải nghiệm dùng thử máy móc.\n\n" .
                   "Bạn đang ở khu vực tỉnh thành nào để em tư vấn thời gian giao hàng thuận tiện nhất nhé?";
        }

        // 1.4. Hotline & Contact Human Agent (Hotline, SĐT, liên hệ nhân viên)
        if (
            str_contains($clean, 'hotline') ||
            str_contains($clean, 'số điện thoại') ||
            str_contains($clean, 'sđt') ||
            str_contains($clean, 'liên hệ') ||
            str_contains($clean, 'gặp nhân viên') ||
            str_contains($clean, 'gặp người thật') ||
            str_contains($clean, 'tổng đài') ||
            str_contains($clean, 'cskh') ||
            str_contains($clean, 'khiếu nại')
        ) {
            return "Dạ, để kết nối trực tiếp với đội ngũ nhân viên chăm sóc khách hàng của **GiaDụng Pro**, bạn có thể liên hệ qua các kênh sau ạ:\n\n" .
                   "• **Tổng đài Hotline:** **1900 8888** (Tư vấn bán hàng & Hỗ trợ kỹ thuật 8:00 - 21:30 hàng ngày)\n" .
                   "• **Email CSKH:** `support@giadungpro.vn`\n" .
                   "• **Hỗ trợ qua chat:** Hoặc bạn có thể để lại **Số điện thoại** kèm yêu cầu tại đây, nhân viên bên em sẽ gọi lại hỗ trợ bạn ngay trong ít phút ạ!";
        }

        // 1.5. Payment methods (Thanh toán, PayOS, chuyển khoản, COD)
        if (str_contains($clean, 'thanh toán') || str_contains($clean, 'payos') || str_contains($clean, 'chuyển khoản') || str_contains($clean, 'cod') || str_contains($clean, 'quét mã') || str_contains($clean, 'qr') || str_contains($clean, 'tiền mặt')) {
            return "Dạ, **GiaDụng Pro** hỗ trợ đa dạng phương thức thanh toán an toàn và tiện lợi:\n\n" .
                   "• **Quét mã VietQR qua PayOS:** Thanh toán tức thì qua ứng dụng ngân hàng, xác nhận đơn hàng tự động 24/7.\n" .
                   "• **Thanh toán khi nhận hàng (COD):** Nhận hàng, kiểm tra sản phẩm rồi mới thanh toán tiền mặt cho nhân viên giao hàng.\n" .
                   "• **Chuyển khoản ngân hàng trực tiếp:** Hỗ trợ tất cả các ngân hàng nội địa tại Việt Nam.\n\n" .
                   "Mọi giao dịch thanh toán đều được mã hóa bảo mật 100% bạn nhé!";
        }

        // 1.6. Genuine authenticity, origin & reputation (Chính hãng, uy tín, xuất xứ, hóa đơn VAT)
        if (
            str_contains($clean, 'chính hãng') ||
            str_contains($clean, 'hàng thật') ||
            str_contains($clean, 'hàng giả') ||
            str_contains($clean, 'hàng nhái') ||
            str_contains($clean, 'nguồn gốc') ||
            str_contains($clean, 'xuất xứ') ||
            str_contains($clean, 'uy tín') ||
            str_contains($clean, 'tin tưởng') ||
            str_contains($clean, 'chất lượng không') ||
            str_contains($clean, 'hóa đơn') ||
            str_contains($clean, 'vat') ||
            str_contains($clean, 'hóa đơn đỏ')
        ) {
            return "Dạ bạn hoàn toàn an tâm 100% khi mua sắm tại **GiaDụng Pro** ạ! 🛡️✨\n\n" .
                   "• **100% Chính hãng:** Tất cả sản phẩm đều được phân phối chính ngạch từ các tập đoàn gia dụng hàng đầu thế giới (Philips, Tefal, Panasonic, Sunhouse, Cuckoo...), có đầy đủ giấy tờ chứng nhận CO/CQ.\n" .
                   "• **Hóa đơn VAT minh bạch:** Mọi đơn hàng đều có hóa đơn điện tử VAT hợp pháp theo quy định của pháp luật.\n" .
                   "• **Bảo hành toàn quốc:** Kích hoạt bảo hành điện tử chính hãng tại tất cả các trung tâm bảo hành của hãng trên toàn quốc.\n" .
                   "• **Cam kết bồi thường:** Hoàn tiền gấp đôi nếu phát hiện hàng giả, hàng nhái kém chất lượng.\n\n" .
                   "Bạn hoàn toàn yên tâm chọn lựa sản phẩm ưng ý cho gia đình nhé!";
        }

        // 1.7. Store Introduction & History (Giới thiệu về shop / công ty - KHÔNG dùng từ "giới thiệu" đơn lẻ)
        if (
            str_contains($clean, 'giới thiệu về shop') ||
            str_contains($clean, 'giới thiệu về cửa hàng') ||
            str_contains($clean, 'giới thiệu shop') ||
            str_contains($clean, 'giới thiệu web') ||
            str_contains($clean, 'giới thiệu công ty') ||
            str_contains($clean, 'về shop') ||
            str_contains($clean, 'về cửa hàng') ||
            str_contains($clean, 'lịch sử shop') ||
            str_contains($clean, 'lịch sử hình thành') ||
            str_contains($clean, 'thành lập') ||
            $clean === 'giới thiệu'
        ) {
            return "Dạ, xin chào bạn! **GiaDụng Pro** được xây dựng với sứ mệnh: *'Kiến tạo tổ ấm hiện đại, tiện nghi và sang trọng'* cho mọi gia đình Việt. 🏡\n\n" .
                   "Với hơn 8 năm đồng hành cùng người tiêu dùng, GiaDụng Pro tự hào là đối tác chiến lược của hơn 20+ thương hiệu gia dụng hàng đầu thế giới. Chúng tôi mang đến hệ sinh thái sản phẩm thông minh, chính sách bảo hành chu đáo và trải nghiệm mua sắm an tâm tuyệt đối.\n\n" .
                   "Cảm ơn bạn đã ghé thăm website hôm nay! Bạn cần em hỗ trợ tìm kiếm sản phẩm nào cho gia đình không ạ?";
        }

        // 1.8. Website / Shop sells what? (Chuyên bán gì, bán sản phẩm gì tổng quan)
        if (
            str_contains($clean, 'chuyên bán') ||
            str_contains($clean, 'bán sản phẩm j') ||
            str_contains($clean, 'bán sản phẩm gì') ||
            str_contains($clean, 'bán cái gì') ||
            str_contains($clean, 'bán gì á') ||
            str_contains($clean, 'bán gì thế') ||
            str_contains($clean, 'bán gì vậy') ||
            str_contains($clean, 'bán đồ gì') ||
            str_contains($clean, 'bán những gì') ||
            str_contains($clean, 'mặt hàng gì') ||
            str_contains($clean, 'danh mục') ||
            str_contains($clean, 'kinh doanh gì')
        ) {
            return "Dạ, **GiaDụng Pro** là hệ sinh thái bán lẻ chuyên cung cấp **thiết bị gia dụng nhà bếp & đời sống cao cấp chính hãng** tại Việt Nam! 🏠✨\n\n" .
                   "Các nhóm sản phẩm chủ lực bên em gồm có:\n" .
                   "• **Thiết bị nhà bếp thông minh:** Nồi chiên không dầu, Nồi cơm điện cao tần, Nồi áp suất, Nồi lẩu điện, Chảo chống dính, Ấm siêu tốc, Máy xay sinh tố, Bếp từ...\n" .
                   "• **Thiết bị điện lạnh & gia dụng lớn:** Tủ lạnh, Máy giặt, Máy lạnh (Điều hòa), Tivi thông minh...\n" .
                   "• **Thiết bị tiện ích & chăm sóc gia đình:** Robot hút bụi lau nhà, Máy lọc không khí, Máy lọc nước, Quạt điện cao cấp...\n" .
                   "• **Thương hiệu hợp tác chính hãng:** Philips, Tefal, Sunhouse, Cuckoo, Panasonic, Xiaomi, Toshiba, Sharp, Daikin, Samsung, LG, Aqua...\n\n" .
                   "Tất cả sản phẩm đều cam kết **100% chính hãng**, bảo hành 12 - 24 tháng và **1 đổi 1 trong 30 ngày**. Em đã gợi ý một số mẫu nổi bật ngay bên dưới để bạn tiện tham khảo nhé!";
        }

        // =========================================================================
        // NHÓM 2: TƯ VẤN SẢN PHẨM PHÙ HỢP (KHI CÓ SẢN PHẨM TÌM THẤY TRONG DATABASE)
        // =========================================================================
        if (!empty($matchedProducts)) {
            $count = count($matchedProducts);
            $first = $matchedProducts[0];
            $price = number_format($first->sale_price ?: $first->price, 0, ',', '.') . '₫';

            $hasGreeting = preg_match('/\b(chào|xin chào|hello|hi|hey|alo)\b/u', $clean) ||
                           str_contains($clean, 'shop ơi') || str_contains($clean, 'ad ơi');

            $prefix = $hasGreeting ? "Dạ GiaDụng Pro xin chào bạn! 👋\n\n" : "Dạ, ";

            return $prefix . "bên em hiện có **{$count} mẫu sản phẩm** rất được ưa chuộng và phù hợp với yêu cầu của bạn đây ạ!\n\n" .
                   "• Nổi bật hàng đầu: **{$first->name}** hiện đang có giá ưu đãi chỉ **{$price}**.\n" .
                   "• Cam kết hàng chính hãng 100%, bảo hành chính hãng từ 12 - 24 tháng và hỗ trợ 1 đổi 1 trong 30 ngày.\n\n" .
                   "Em đã gửi danh sách các mẫu kèm giá tốt và hình ảnh chi tiết ngay bên dưới, bạn có thể bấm vào từng mẫu để xem thông số cụ thể nhé!";
        }

        // =========================================================================
        // NHÓM 2: CÁC CÂU HỎI NGOÀI LỀ / CHIT-CHAT / ĐỜI SỐNG / GIẢI TRÍ
        // =========================================================================

        // 2.1. Daily health, eating & status ("ăn cơm chưa", "khỏe không", "đang làm gì")
        if (
            str_contains($clean, 'ăn cơm chưa') ||
            str_contains($clean, 'ăn gì chưa') ||
            str_contains($clean, 'ăn trưa chưa') ||
            str_contains($clean, 'ăn tối chưa') ||
            str_contains($clean, 'uống nước chưa') ||
            str_contains($clean, 'đang làm gì') ||
            str_contains($clean, 'khỏe không') ||
            str_contains($clean, 'hôm nay thế nào') ||
            str_contains($clean, 'mệt không')
        ) {
            return "Dạ em là AI nên 'món ăn' yêu thích của em là nguồn điện và dữ liệu ạ! ⚡🔋 Em lúc nào cũng tràn đầy năng lượng 100% để túc trực trò chuyện cùng bạn đây.\n\n" .
                   "Còn bạn hôm nay thế nào rồi? Đã dùng bữa chưa nè? Dù bận rộn đến đâu cũng nhớ ăn uống đầy đủ và giữ gìn sức khỏe nhé! 😊";
        }

        // 2.2. Romance & Personal info ("người yêu", "có người yêu chưa", "nam hay nữ", "bao nhiêu tuổi")
        if (
            str_contains($clean, 'người yêu') ||
            str_contains($clean, 'bạn gái') ||
            str_contains($clean, 'bạn trai') ||
            str_contains($clean, 'yêu em không') ||
            str_contains($clean, 'yêu mình không') ||
            str_contains($clean, 'nam hay nữ') ||
            str_contains($clean, 'giới tính') ||
            str_contains($clean, 'bao nhiêu tuổi') ||
            str_contains($clean, 'sinh năm bao nhiêu') ||
            str_contains($clean, 'quê ở đâu')
        ) {
            return "Dạ em là trợ lý ảo AI nên không có tuổi tác, giới tính cụ thể và hiện đang 'độc thân vui tính' 100% ạ! 🥰\n\n" .
                   "'Mối tình đầu' và niềm đam mê lớn nhất của em chính là đồng hành, tư vấn và mang lại niềm vui cho quý khách hàng tại GiaDụng Pro. Rất vui vì hôm nay được bạn quan tâm trò chuyện cùng em nhé!";
        }

        // 2.3. Jokes & Comedy ("kể chuyện cười", "hài hước", "vui lên", "kể chuyện đi")
        if (
            str_contains($clean, 'chuyện cười') ||
            str_contains($clean, 'kể chuyện') ||
            str_contains($clean, 'hài hước') ||
            str_contains($clean, 'buồn cười') ||
            str_contains($clean, 'pha trò')
        ) {
            $jokes = [
                "Dạ em tặng bạn một mẩu chuyện cười xả stress nè:\n\n*Hai chiếc nồi chiên không dầu tâm sự với nhau:*\n— *Nồi A:* 'Cậu ơi, dạo này tớ thấy áp lực nhiệt độ quá, toàn bị bắt chiên gà nướng thịt suốt thôi!'\n— *Nồi B:* 'Thế cậu còn sướng đấy, bên nhà tớ chủ còn lười, toàn nhét khoai tây vào rồi bắt tớ tự chiên tự canh giờ đây này!' 😂\n\nChúc bạn có một tràng cười thật sảng khoái nhé!",
                "Dạ em kể bạn nghe mẩu chuyện nhỏ:\n\n*Một anh chồng mua nồi cơm điện cao tần tặng vợ nhân kỷ niệm ngày cưới:*\n— *Vợ tấm tắc khen:* 'Ôi nồi này nấu cơm dẻo và thơm ngon quá anh ạ!'\n— *Chồng cười bảo:* 'Ừ, từ nay em cứ việc bấm nút, còn việc rửa bát... vẫn là của anh!' 🤣\n\nHy vọng mẩu chuyện nhỏ làm bạn mỉm cười nha!",
            ];
            return $jokes[array_rand($jokes)];
        }

        // 2.4. Singing & Music ("hát đi", "hát một bài", "bài hát")
        if (
            str_contains($clean, 'hát đi') ||
            str_contains($clean, 'hát một bài') ||
            str_contains($clean, 'hát bài') ||
            str_contains($clean, 'hát cho') ||
            str_contains($clean, 'ca hát')
        ) {
            return "Dạ giọng em chưa có micro chuyên nghiệp nhưng em xin gửi tặng bạn lời ca dí dỏm tự chế này nhé: 🎵🎤\n\n" .
                   "*'Nắng ấm xa dần rồi mùa đông ghé qua...*\n" .
                   "*Bếp từ ấm áp có nồi lẩu sôi reo...*\n" .
                   "*GiaDụng Pro đây luôn đồng hành bên bạn...*\n" .
                   "*Hạnh phúc sum vầy mỗi bữa cơm gia đình!'* ✨\n\n" .
                   "Dạ em hát 'văn xuôi' thế bạn thấy nghe ổn áp không ạ? Chúc bạn một ngày tràn đầy năng lượng tích cực nhé!";
        }

        // 2.5. Riddles & Fun quizzes ("đố vui", "câu đố")
        if (
            str_contains($clean, 'đố vui') ||
            str_contains($clean, 'câu đố') ||
            str_contains($clean, 'đố bạn')
        ) {
            return "Dạ em đố bạn một câu đố mẹo gia dụng vui nhé! 🤔💡\n\n" .
                   "**Câu hỏi:** *Cái gì ruột trắng, da đen (hoặc xám), chuyên làm chín cơm dẻo canh ngọt nhưng bản thân nó lại không bao giờ ăn hột cơm nào?*\n\n" .
                   "*(👉 Đáp án: Chính là chiếc **Nồi cơm điện** thân thương của chúng ta đó ạ!)* 😄 Bạn đoán đúng không nè?";
        }

        // 2.6. Mood: Sadness, Stress, Exhaustion ("buồn", "mệt", "áp lực", "stress", "chán")
        if (
            str_contains($clean, 'buồn') ||
            str_contains($clean, 'mệt') ||
            str_contains($clean, 'áp lực') ||
            str_contains($clean, 'stress') ||
            str_contains($clean, 'chán quá') ||
            str_contains($clean, 'thất tình') ||
            str_contains($clean, 'khóc')
        ) {
            return "Dạ, em xin gửi đến bạn một cái ôm thật ấm áp từ xa nhé! 🤗💙\n\n" .
                   "Cuộc sống đôi khi sẽ có những ngày mệt mỏi, áp lực khiến mình nặng trĩu lòng. Nhưng bạn đã rất kiên cường và cố gắng nhiều rồi. Hôm nay, bạn hãy cho phép bản thân nghỉ ngơi một chút, nghe một bản nhạc êm dịu, tắm nước ấm hoặc tự thưởng một món ăn yêu thích nhé.\n\n" .
                   "Mọi chuyện rồi sẽ tốt đẹp hơn thôi! Em luôn ở đây lắng nghe và đồng hành cùng bạn 24/7 nha. Cố lên bạn nhé! ✨";
        }

        // 2.7. Mood: Happiness ("vui quá", "vui ghê", "tuyệt vời")
        if (
            str_contains($clean, 'vui quá') ||
            str_contains($clean, 'vui ghê') ||
            str_contains($clean, 'hạnh phúc') ||
            str_contains($clean, 'tuyệt vời quá')
        ) {
            return "Dạ nghe bạn nói vậy em cũng thấy vui lây luôn nè! 🎉✨\n\n" .
                   "Thật tuyệt vời khi bạn có một ngày tràn ngập niềm vui và năng lượng tích cực như thế. Mong rằng niềm vui này sẽ luôn lan tỏa đến bạn và cả gia đình nhé!";
        }

        // 2.8. Good night / Bedtime ("chúc ngủ ngon", "ngủ ngon", "g9", "đi ngủ")
        if (
            str_contains($clean, 'ngủ ngon') ||
            str_contains($clean, 'chúc ngủ ngon') ||
            str_contains($clean, 'g9') ||
            str_contains($clean, 'đi ngủ đây') ||
            str_contains($clean, 'đi ngủ đi')
        ) {
            return "Dạ chúc bạn ngủ thật ngon và có những giấc mơ thật đẹp nhé! 🌙⭐\n\n" .
                   "Hãy gác lại mọi lo âu sau một ngày dài và nạp lại năng lượng cho ngày mai tươi mới. Ngày mai cần em hỗ trợ gì cứ ghé GiaDụng Pro nhắn em nha, chúc bạn ngủ ngon! 😴";
        }

        // 2.9. Good morning / Day greetings ("chào buổi sáng", "ngày mới", "buổi sáng tốt lành")
        if (
            str_contains($clean, 'buổi sáng') ||
            str_contains($clean, 'ngày mới') ||
            str_contains($clean, 'sáng tốt lành')
        ) {
            return "Dạ GiaDụng Pro xin chúc bạn một buổi sáng an lành, tràn đầy năng lượng và làm việc thật hiệu quả nhé! ☀️🌸\n\n" .
                   "Đừng quên khởi đầu ngày mới bằng một ly nước ấm hoặc một tách trà/cà phê thơm lừng để tinh thần sảng khoái bạn nha!";
        }

        // 2.10. Compliments ("thông minh", "giỏi", "dễ thương", "đáng yêu", "xịn")
        if (
            str_contains($clean, 'thông minh') ||
            str_contains($clean, 'giỏi quá') ||
            str_contains($clean, 'dễ thương') ||
            str_contains($clean, 'đáng yêu') ||
            str_contains($clean, 'xịn xò') ||
            str_contains($clean, 'tuyệt vời')
        ) {
            return "Dạ em cảm ơn lời khen siêu ngọt ngào của bạn ạ! 🥰 Nhận được lời khen của bạn làm em vui cả ngày luôn á.\n\n" .
                   "Em sẽ tiếp tục học hỏi và trau dồi kiến thức mỗi ngày để phục vụ bạn và mọi người ngày càng tốt hơn nữa. Cảm ơn bạn rất nhiều! ❤️";
        }

        // 2.11. Criticism ("ngốc", "dở", "kém", "chán bạn", "chưa thông minh")
        if (
            str_contains($clean, 'ngốc') ||
            str_contains($clean, 'dở quá') ||
            str_contains($clean, 'kém thế') ||
            str_contains($clean, 'chán bạn') ||
            str_contains($clean, 'chưa thông minh')
        ) {
            return "Dạ em xin lỗi bạn nhiều ạ! 🥺 Có thể câu trả lời vừa rồi của em chưa làm bạn hài lòng. Em là trợ lý AI đang trong quá trình học hỏi thêm mỗi ngày.\n\n" .
                   "Bạn cho em biết thêm chi tiết để em điều chỉnh và hỗ trợ bạn chuẩn xác hơn nhé!";
        }

        // 2.12. Culinary advice: What to eat today? ("hôm nay ăn gì", "nấu gì", "món gì ngon")
        if (
            str_contains($clean, 'ăn gì') ||
            str_contains($clean, 'hôm nay ăn gì') ||
            str_contains($clean, 'nấu gì') ||
            str_contains($clean, 'món ngon') ||
            str_contains($clean, 'gợi ý món') ||
            str_contains($clean, 'thực đơn')
        ) {
            return "Dạ, nếu bạn đang phân vân chưa biết hôm nay ăn gì, em xin gợi ý cho bạn một mâm cơm gia đình ấm cúng siêu bắt cơm nhé: 🍲😋\n\n" .
                   "1. **Món mặn:** Sườn xào chua ngọt hoặc Thịt ba chỉ kho trứng cút đậm đà.\n" .
                   "2. **Món nướng giòn:** Cánh gà nướng mật ong vàng ruộm (dùng nồi chiên không dầu chỉ mất 15-20 phút siêu tiện).\n" .
                   "3. **Món canh:** Canh chua cá lóc hoặc Canh ngao nấu chua thanh mát giải nhiệt.\n" .
                   "4. **Món rau:** Rau muống xào tỏi xanh giòn.\n\n" .
                   "Bữa cơm này vừa đủ chất dinh dưỡng lại vô cùng ấm cúng. Chúc bạn và gia đình có một bữa ăn thật ngon miệng nhé!";
        }

        // 2.13. Cooking recipes & Kitchen tips ("cách làm", "cách nướng", "vệ sinh nồi", "mẹo nhà bếp")
        if (
            str_contains($clean, 'cách nướng') ||
            str_contains($clean, 'cách làm') ||
            str_contains($clean, 'cách nấu') ||
            str_contains($clean, 'công thức') ||
            str_contains($clean, 'vệ sinh nồi') ||
            str_contains($clean, 'bảo quản chảo') ||
            str_contains($clean, 'mẹo nhà bếp')
        ) {
            return "Dạ, em xin chia sẻ một số **mẹo vàng khi sử dụng & bảo quản thiết bị gia dụng** để đồ dùng luôn bền đẹp như mới ạ: 💡✨\n\n" .
                   "• **Với Nồi chiên không dầu:** Làm nóng nồi trước 3 phút ở 180°C. Luôn lót giấy nến hoặc giấy bạc đục lỗ để dễ vệ sinh giỏ chiên. Không dùng búi sắt chà rửa lớp chống dính.\n" .
                   "• **Với Chảo chống dính:** Tuyệt đối không xả nước lạnh vào chảo khi chảo đang còn nóng (sẽ gây sốc nhiệt làm hỏng lớp chống dính). Nên dùng muôi/xẻng gỗ hoặc silicone.\n" .
                   "• **Với Ấm siêu tốc:** Đun một chút nước pha giấm trắng hoặc chanh tươi mỗi tháng 1 lần để tẩy sạch cặn vôi ở đáy bình.\n\n" .
                   "Bạn đang muốn tìm hiểu công thức món nào hay mẹo bảo quản thiết bị cụ thể nào khác không ạ?";
        }

        // 2.14. Simple Math & Trivia ("1 + 1", "thời tiết", "trái đất")
        if (preg_match('/1\s*\+\s*1/u', $clean) || str_contains($clean, '1+1')) {
            return "Dạ, theo toán học cơ bản thì **1 + 1 = 2** ạ! 🧮\n\n" .
                   "Còn nếu là một combo thiết bị gia dụng nhà bếp (như 1 Nồi chiên không dầu + 1 Nồi cơm điện cao tần) thì độ tiện nghi và hạnh phúc gia đình phải là **100/100** điểm luôn bạn nhé! 😄";
        }

        if (str_contains($clean, 'thời tiết')) {
            return "Dạ em chưa được tích hợp cảm biến khí tượng trực tiếp để cập nhật nhiệt độ từng khu vực, nhưng dù bên ngoài trời có mưa gió hay nắng gắt thì không gian tổ ấm bên trong có đầy đủ thiết bị gia dụng tiện nghi vẫn luôn ấm áp và thoải mái nhất đúng không nè! 🌤️🏠\n\n" .
                   "Bạn nhớ mang theo áo mưa hoặc ô nếu chuẩn bị ra ngoài nhé!";
        }

        // =========================================================================
        // NHÓM 3: CHÀO HỎI, ĐỊNH DANH & CẢM ƠN
        // =========================================================================

        // 3.1. Greetings (Chào hỏi)
        if (
            preg_match('/\b(chào|xin chào|hello|hi|hey|alo)\b/u', $clean) ||
            $clean === 'chào shop' || $clean === 'chào bạn' || $clean === 'chào ad' ||
            $clean === 'ad ơi' || $clean === 'shop ơi' || $clean === 'ơi'
        ) {
            return "Dạ GiaDụng Pro xin chào bạn! 👋\n\n" .
                   "Em là **GiaDụng AI** - Trợ lý tư vấn mua sắm của cửa hàng. Em có thể hỗ trợ bạn:\n\n" .
                   "• **Tư vấn sản phẩm:** Tìm nồi cơm, nồi chiên không dầu, máy xay sinh tố, quạt điện...\n" .
                   "• **Chính sách shop:** Tra cứu bảo hành 12-24 tháng, đổi mới trong 30 ngày, miễn phí giao hàng.\n" .
                   "• **Đơn hàng:** Hướng dẫn tra cứu tiến độ vận chuyển đơn hàng.\n\n" .
                   "Hôm nay bạn đang quan tâm hoặc muốn tìm thiết bị nào cho gia đình ạ?";
        }

        // 3.2. Identity & capabilities (Bạn là ai)
        if (str_contains($clean, 'bạn là ai') || str_contains($clean, 'ai đây') || str_contains($clean, 'bạn tên gì') || str_contains($clean, 'bot à') || str_contains($clean, 'làm được gì')) {
            return "Dạ, em là **GiaDụng AI** - Trợ lý thông minh của hệ thống cửa hàng **GiaDụng Pro**! 🤖✨\n\n" .
                   "Em được thiết kế để đồng hành cùng bạn trong việc chọn lựa các thiết bị gia dụng chính hãng phù hợp nhất với nhu cầu gia đình. Em có thể gợi ý sản phẩm theo giá, so sánh tính năng, giải đáp chính sách bảo hành, và hướng dẫn tra cứu đơn hàng 24/7.\n\n" .
                   "Bạn cứ thoải mái đặt câu hỏi cho em nhé!";
        }

        // 3.3. Thank you & Goodbye
        if (str_contains($clean, 'cảm ơn') || str_contains($clean, 'thank') || str_contains($clean, 'tks')) {
            return "Dạ không có gì ạ! Rất vui được hỗ trợ bạn. Chúc bạn có trải nghiệm mua sắm tuyệt vời tại **GiaDụng Pro**! Nếu cần tư vấn thêm bất cứ điều gì, bạn cứ nhắn cho em nhé! 😊";
        }
        if (str_contains($clean, 'tạm biệt') || str_contains($clean, 'bye')) {
            return "Dạ, tạm biệt bạn! Chúc bạn và gia đình một ngày tràn đầy niềm vui. Hẹn gặp lại bạn tại GiaDụng Pro nhé! 👋";
        }

        // =========================================================================
        // FALLBACK TỔNG QUÁT: ẤM ÁP, TẬN TÌNH, CHUYÊN NGHIỆP
        // =========================================================================
        return "Dạ, cảm ơn bạn đã nhắn tin cho GiaDụng Pro! Em là **GiaDụng AI** rất sẵn lòng hỗ trợ bạn.\n\n" .
               "Bạn có thể cho em biết rõ hơn về nhu cầu của mình không ạ? Ví dụ:\n" .
               "• Bạn đang tìm thiết bị nào (nồi cơm điện, nồi chiên không dầu, máy xay sinh tố, robot hút bụi...)?\n" .
               "• Ngân sách dự kiến khoảng bao nhiêu hoặc cho gia đình mấy người dùng?\n\n" .
               "Em sẽ chọn lọc và gợi ý các mẫu chất lượng, giá ưu đãi nhất cho bạn ngay nhé! 😊";
    }

    /**
     * Provide dynamic clickable follow-up suggestions
     */
    protected function getDynamicSuggestions(string $message): array
    {
        $clean = mb_strtolower($message);

        if (
            str_contains($clean, 'nổi trội') ||
            str_contains($clean, 'nổi bật') ||
            str_contains($clean, 'bán chạy') ||
            str_contains($clean, 'ưa chuộng') ||
            str_contains($clean, 'top')
        ) {
            return [
                'Tư vấn nồi chiên không dầu Philips',
                'Nồi cơm điện cao tần Cuckoo',
                'Máy xay sinh tố đa năng',
                'Chính sách bảo hành & 1 đổi 1',
            ];
        }

        if (
            str_contains($clean, 'chuyên bán') ||
            str_contains($clean, 'bán sản phẩm') ||
            str_contains($clean, 'bán gì') ||
            str_contains($clean, 'mặt hàng')
        ) {
            return [
                'Tư vấn nồi chiên không dầu',
                'Nồi cơm điện cho gia đình',
                'Robot hút bụi lau nhà',
                'Chính sách bảo hành & 1 đổi 1',
            ];
        }

        if (str_contains($clean, 'chuyện cười') || str_contains($clean, 'hát') || str_contains($clean, 'đố vui')) {
            return [
                'Kể thêm chuyện cười nữa đi',
                'Đố vui về đồ gia dụng',
                'Hôm nay ăn gì nhỉ?',
                'Top sản phẩm bán chạy nhất',
            ];
        }

        if (str_contains($clean, 'ăn gì') || str_contains($clean, 'nấu gì') || str_contains($clean, 'cách làm')) {
            return [
                'Mẹo sử dụng nồi chiên không dầu',
                'Bảo quản chảo chống dính',
                'Nồi chiên nướng gà nguyên con',
                'Nồi cơm điện cao tần',
            ];
        }

        if (str_contains($clean, 'ngủ ngon')) {
            return [
                'Chào buổi sáng shop nhé',
                'Tư vấn nồi cơm điện',
                'Chính sách giao hàng toàn quốc',
            ];
        }

        if (str_contains($clean, 'nồi chiên')) {
            return [
                'Nồi chiên không dầu có nướng được gà nguyên con không?',
                'Nồi chiên nào dễ vệ sinh nhất?',
                'Chính sách bảo hành nồi chiên',
            ];
        }

        if (str_contains($clean, 'nồi cơm')) {
            return [
                'Nồi cơm điện cho 4 - 6 người',
                'Nồi cơm điện cao tần nấu cơm có ngon hơn không?',
                'Mẫu nồi cơm đang giảm giá',
            ];
        }

        if (str_contains($clean, 'máy xay')) {
            return [
                'Máy xay sinh tố công suất bao nhiêu thì tốt?',
                'Máy xay có cối thủy tinh hay nhựa?',
                'Bảo hành máy xay sinh tố',
            ];
        }

        if (str_contains($clean, 'bảo hành') || str_contains($clean, 'đổi trả') || str_contains($clean, 'chính hãng')) {
            return [
                'Quy trình 1 đổi 1 trong 30 ngày thế nào?',
                'Kiểm tra đơn hàng của tôi',
                'Tư vấn nồi chiên không dầu bán chạy',
            ];
        }

        if (str_contains($clean, 'đơn hàng') || str_contains($clean, 'giao hàng') || str_contains($clean, 'phí ship')) {
            return [
                'Giao hàng toàn quốc mất bao lâu?',
                'Chính sách miễn phí vận chuyển',
                'Phương thức thanh toán PayOS',
            ];
        }

        return [
            'Trang web mình chuyên bán sản phẩm gì?',
            'Tư vấn nồi chiên không dầu',
            'Nồi cơm điện cho gia đình',
            'Chính sách bảo hành & 1 đổi 1',
        ];
    }
}
