<!-- AI Chat Floating Widget -->
<div id="ai-chat-root" class="relative z-50 font-sans">
    <!-- Floating Trigger Button -->
    <div class="fixed bottom-6 right-6 z-50 flex items-center gap-3">
        <!-- Floating Hint Bubble (Disappears once clicked) -->
        <div id="ai-chat-tooltip" 
             class="hidden md:flex items-center gap-2 bg-slate-900/90 backdrop-blur-md text-white text-xs font-semibold py-2 px-3.5 rounded-full shadow-xl border border-white/10 animate-bounce transition-all duration-300">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>Hỏi trợ lý AI GiaDụng</span>
        </div>

        <!-- Main Button -->
        <button id="ai-chat-toggle-btn"
                type="button"
                aria-label="Mở khung chat AI"
                style="color: #ffffff !important;"
                class="group relative flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-orange-500 via-amber-500 to-orange-400 text-white shadow-xl shadow-orange-500/30 hover:shadow-orange-500/50 hover:scale-105 active:scale-95 transition-all duration-300 focus:outline-none">
            
            <!-- Glow ring -->
            <span class="absolute inset-0 rounded-full bg-orange-400/30 blur-md group-hover:blur-lg transition-all"></span>

            <!-- Icon: Sparkle / Bot -->
            <span id="ai-chat-btn-icon-open" class="relative transition-transform duration-300 group-hover:rotate-12">
                <svg class="w-7 h-7 text-white" stroke="#ffffff" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </span>

            <!-- Icon: Close (Hidden when collapsed) -->
            <span id="ai-chat-btn-icon-close" class="relative hidden">
                <svg class="w-6 h-6 text-white" stroke="#ffffff" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </span>

            <!-- Pulsing Active Badge -->
            <span class="absolute top-0 right-0 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white"></span>
            </span>
        </button>
    </div>

    <!-- Chat Modal Window -->
    <div id="ai-chat-modal"
         class="fixed bottom-24 right-4 sm:right-6 z-50 w-[380px] sm:w-[410px] max-w-[calc(100vw-2rem)] h-[580px] max-h-[calc(100vh-7.5rem)] bg-white rounded-3xl shadow-2xl border border-slate-200/90 flex flex-col overflow-hidden transition-all duration-300 transform scale-90 opacity-0 pointer-events-none origin-bottom-right">
        
        <!-- Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex items-center justify-between border-b border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 p-0.5 shadow-md">
                    <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center text-orange-400 font-bold text-lg">
                        ✨
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h3 class="font-extrabold text-sm tracking-wide text-white">GiaDụng AI</h3>
                        <span class="px-1.5 py-0.5 text-[10px] font-bold bg-orange-500/20 text-orange-400 rounded-md border border-orange-500/30">Gemini</span>
                    </div>
                    <p class="text-[11px] text-slate-400 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Trực tuyến 24/7 • Tư vấn chọn đồ
                    </p>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-1">
                <!-- Clear chat -->
                <button id="ai-chat-clear-btn"
                        title="Xóa đoạn chat làm lại"
                        class="p-2 text-slate-400 hover:text-white hover:bg-white/10 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <!-- Close -->
                <button id="ai-chat-close-btn"
                        title="Đóng chat"
                        class="p-2 text-slate-400 hover:text-white hover:bg-white/10 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div id="ai-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-4 text-xs sm:text-sm bg-slate-50/60 scroll-smooth">
            
            <!-- Greeting Message -->
            <div class="flex items-start gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 font-bold shadow-xs">
                    🤖
                </div>
                <div class="space-y-2 max-w-[85%]">
                    <div class="p-3.5 bg-white text-slate-800 rounded-2xl rounded-tl-xs shadow-xs border border-slate-200/80 leading-relaxed">
                        <p class="font-bold text-slate-900 mb-1">Xin chào bạn! 👋</p>
                        <p>Em là <strong>Trợ lý AI của GiaDụng Pro</strong>. Em có thể giúp gì cho bạn hôm nay?</p>
                        <ul class="mt-2 space-y-1 text-slate-600 text-xs list-disc list-inside">
                            <li>Tư vấn chọn nồi chiên, nồi cơm, máy xay sinh tố...</li>
                            <li>Tra cứu chính sách bảo hành 12 - 24 tháng, 1 đổi 1</li>
                            <li>Hỗ trợ kiểm tra đơn hàng, giao hàng toàn quốc</li>
                        </ul>
                    </div>

                    <!-- Quick Starter Chips -->
                    <div id="ai-quick-suggestions" class="flex flex-wrap gap-1.5 pt-1">
                        <button class="ai-suggestion-chip px-3 py-1.5 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 text-slate-700 text-xs font-medium rounded-full border border-slate-200 shadow-2xs transition-all">
                            🍗 Tư vấn nồi chiên không dầu
                        </button>
                        <button class="ai-suggestion-chip px-3 py-1.5 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 text-slate-700 text-xs font-medium rounded-full border border-slate-200 shadow-2xs transition-all">
                            🍚 Nồi cơm điện cho gia đình
                        </button>
                        <button class="ai-suggestion-chip px-3 py-1.5 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 text-slate-700 text-xs font-medium rounded-full border border-slate-200 shadow-2xs transition-all">
                            🛡️ Chính sách bảo hành & đổi trả
                        </button>
                        <button class="ai-suggestion-chip px-3 py-1.5 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 text-slate-700 text-xs font-medium rounded-full border border-slate-200 shadow-2xs transition-all">
                            🔥 Top sản phẩm bán chạy nhất
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Messages will be injected here -->

            <!-- Typing Indicator (Hidden by default) -->
            <div id="ai-typing-indicator" class="hidden items-start gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 font-bold shadow-xs">
                    🤖
                </div>
                <div class="p-3 bg-white text-slate-500 rounded-2xl rounded-tl-xs shadow-xs border border-slate-200/80 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-400 animate-bounce"></span>
                    <span class="w-2 h-2 rounded-full bg-slate-400 animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-2 h-2 rounded-full bg-slate-400 animate-bounce [animation-delay:0.4s]"></span>
                </div>
            </div>

        </div>

        <!-- Chat Input Form -->
        <div class="p-3.5 bg-white border-t border-slate-200/80 shrink-0">
            <form id="ai-chat-form" class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input id="ai-chat-input"
                           type="text"
                           maxlength="500"
                           autocomplete="off"
                           placeholder="Hỏi về nồi chiên, nồi cơm, robot hút bụi, bảo hành..."
                           class="w-full pl-4 pr-10 py-3 bg-slate-100 hover:bg-slate-50 focus:bg-white text-slate-800 text-xs sm:text-sm rounded-2xl border border-transparent focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none transition-all placeholder:text-slate-400">
                    
                    <button type="button" 
                            id="ai-chat-input-clear"
                            class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <button id="ai-chat-send-btn"
                        type="submit"
                        aria-label="Gửi tin nhắn"
                        class="flex items-center justify-center w-11 h-11 rounded-2xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white shadow-md shadow-orange-500/30 transition-all disabled:opacity-50 disabled:pointer-events-none">
                    <svg class="w-5 h-5 -rotate-45 ml-0.5 text-white" stroke="#ffffff" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
            <div class="mt-2 flex items-center justify-between text-[10px] text-slate-400 px-1">
                <span>⚡ AI tư vấn bán hàng GiaDụng Pro</span>
                <span>Nhấn Enter để gửi</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('ai-chat-root');
    const toggleBtn = document.getElementById('ai-chat-toggle-btn');
    const closeBtn = document.getElementById('ai-chat-close-btn');
    const clearBtn = document.getElementById('ai-chat-clear-btn');
    const modal = document.getElementById('ai-chat-modal');
    const tooltip = document.getElementById('ai-chat-tooltip');
    const iconOpen = document.getElementById('ai-chat-btn-icon-open');
    const iconClose = document.getElementById('ai-chat-btn-icon-close');
    const form = document.getElementById('ai-chat-form');
    const input = document.getElementById('ai-chat-input');
    const inputClear = document.getElementById('ai-chat-input-clear');
    const messagesContainer = document.getElementById('ai-chat-messages');
    const typingIndicator = document.getElementById('ai-typing-indicator');

    let isOpen = false;
    let isLoading = false;
    let chatHistory = [];

    // Load conversation from sessionStorage
    try {
        const savedHistory = sessionStorage.getItem('giadung_ai_chat_history');
        if (savedHistory) {
            chatHistory = JSON.parse(savedHistory);
            renderSavedHistory();
        }
    } catch (e) {
        chatHistory = [];
    }

    function toggleChat() {
        isOpen = !isOpen;
        if (isOpen) {
            modal.classList.remove('scale-90', 'opacity-0', 'pointer-events-none');
            modal.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            iconOpen.classList.add('hidden');
            iconClose.classList.remove('hidden');
            if (tooltip) tooltip.classList.add('hidden');
            setTimeout(() => input.focus(), 250);
            scrollToBottom();
        } else {
            modal.classList.add('scale-90', 'opacity-0', 'pointer-events-none');
            modal.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
        }
    }

    toggleBtn?.addEventListener('click', toggleChat);
    closeBtn?.addEventListener('click', toggleChat);

    // Input clear button
    input?.addEventListener('input', function () {
        if (input.value.length > 0) {
            inputClear?.classList.remove('hidden');
        } else {
            inputClear?.classList.add('hidden');
        }
    });

    inputClear?.addEventListener('click', function () {
        input.value = '';
        inputClear.classList.add('hidden');
        input.focus();
    });

    // Clear chat
    clearBtn?.addEventListener('click', function () {
        if (confirm('Bạn muốn xóa lịch sử trò chuyện và bắt đầu lại?')) {
            chatHistory = [];
            sessionStorage.removeItem('giadung_ai_chat_history');
            const dynamicMessages = messagesContainer.querySelectorAll('.ai-dynamic-msg');
            dynamicMessages.forEach(el => el.remove());
            scrollToBottom();
        }
    });

    // Handle suggestion chips
    messagesContainer.addEventListener('click', function (e) {
        const chip = e.target.closest('.ai-suggestion-chip');
        if (chip && !isLoading) {
            // Strip leading emoji cleanly
            const text = chip.innerText.replace(/^[\p{Extended_Pictographic}\s\p{Emoji}]+/u, '').trim() || chip.innerText.trim();
            input.value = text;
            sendMessage(text);
        }
    });

    // Handle form submit
    form?.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text || isLoading) return;
        sendMessage(text);
    });

    async function sendMessage(text) {
        input.value = '';
        if (inputClear) inputClear.classList.add('hidden');
        appendUserMessage(text);

        // Keep history to send BEFORE this current user message
        const historyToSend = [...chatHistory];

        // Save current user message
        chatHistory.push({ role: 'user', content: text });
        saveHistory();

        // Show typing indicator
        isLoading = true;
        showTyping(true);
        scrollToBottom();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('{{ route("chat-ai.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    message: text,
                    history: historyToSend
                })
            });

            if (!res.ok && res.status === 419) {
                throw new Error('CSRF_EXPIRED');
            }

            const data = await res.json();

            showTyping(false);
            isLoading = false;

            if (data.success && data.reply) {
                appendAiMessage(data.reply, data.products || [], data.suggestions || []);
                chatHistory.push({ role: 'model', content: data.reply });
                saveHistory();
            } else {
                appendAiMessage(data.reply || 'Dạ xin lỗi bạn, em chưa kịp nghe rõ, bạn nhắn lại giúp em nhé!');
            }
        } catch (err) {
            showTyping(false);
            isLoading = false;
            console.error('AI chat error:', err);
            if (err.message === 'CSRF_EXPIRED') {
                appendAiMessage('Phiên truy cập đã hết hạn. Bạn vui lòng tải lại trang (F5) để tiếp tục trò chuyện nhé!');
            } else {
                appendAiMessage('Dạ có chút sự cố kết nối. Bạn vui lòng thử lại sau giây lát nhé!');
            }
        }
    }

    function appendUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'ai-dynamic-msg flex justify-end';
        div.innerHTML = `
            <div class="max-w-[80%] p-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-2xl rounded-tr-xs shadow-md shadow-orange-500/20 text-xs sm:text-sm leading-relaxed break-words font-medium">
                ${escapeHtml(text)}
            </div>
        `;
        messagesContainer.insertBefore(div, typingIndicator);
        scrollToBottom();
    }

    function appendAiMessage(text, products = [], suggestions = []) {
        const div = document.createElement('div');
        div.className = 'ai-dynamic-msg flex items-start gap-2.5';

        let productsHtml = '';
        if (products && products.length > 0) {
            productsHtml = `
                <div class="mt-3 pt-3 border-t border-slate-200/80">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Sản phẩm gợi ý cho bạn:</p>
                    <div class="grid grid-cols-1 gap-2">
                        ${products.map(p => `
                            <a href="${p.url}" class="group/item flex items-center gap-2.5 p-2 bg-slate-50 hover:bg-orange-50/80 rounded-xl border border-slate-200/80 hover:border-orange-300 transition-all text-left">
                                <img src="${p.thumbnail}" alt="${escapeHtml(p.name)}" class="w-12 h-12 rounded-lg object-cover bg-white shrink-0 border border-slate-100" onerror="this.src='/images/no-image.png'" />
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-slate-800 text-xs truncate group-hover/item:text-orange-600 transition-colors">${escapeHtml(p.name)}</h4>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-orange-600 font-extrabold text-xs">${p.sale_price || p.price}</span>
                                        ${p.sale_price ? `<span class="text-slate-400 text-[10px] line-through">${p.price}</span>` : ''}
                                        ${p.discount_percent ? `<span class="px-1 py-0.2 bg-red-100 text-red-600 text-[9px] font-bold rounded">-${p.discount_percent}%</span>` : ''}
                                    </div>
                                </div>
                                <span class="text-slate-400 group-hover/item:text-orange-500 text-xs font-bold pr-1">→</span>
                            </a>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        let suggestionsHtml = '';
        if (suggestions && suggestions.length > 0) {
            suggestionsHtml = `
                <div class="flex flex-wrap gap-1.5 pt-1.5">
                    ${suggestions.map(s => `
                        <button class="ai-suggestion-chip px-2.5 py-1 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 text-slate-600 text-[11px] font-medium rounded-full border border-slate-200 transition-all shadow-2xs">
                            ${escapeHtml(s)}
                        </button>
                    `).join('')}
                </div>
            `;
        }

        div.innerHTML = `
            <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 font-bold shadow-xs">
                🤖
            </div>
            <div class="space-y-2 max-w-[85%]">
                <div class="p-3.5 bg-white text-slate-800 rounded-2xl rounded-tl-xs shadow-xs border border-slate-200/80 leading-relaxed text-xs sm:text-sm">
                    ${formatMarkdown(text)}
                    ${productsHtml}
                </div>
                ${suggestionsHtml}
            </div>
        `;

        messagesContainer.insertBefore(div, typingIndicator);
        scrollToBottom();
    }

    function showTyping(show) {
        if (show) {
            typingIndicator.classList.remove('hidden');
            typingIndicator.classList.add('flex');
        } else {
            typingIndicator.classList.add('hidden');
            typingIndicator.classList.remove('flex');
        }
    }

    function scrollToBottom() {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 50);
    }

    function saveHistory() {
        try {
            sessionStorage.setItem('giadung_ai_chat_history', JSON.stringify(chatHistory.slice(-10)));
        } catch (e) {}
    }

    function renderSavedHistory() {
        chatHistory.forEach(item => {
            if (item.role === 'user') {
                appendUserMessage(item.content);
            } else {
                appendAiMessage(item.content);
            }
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/[&<>"']/g, function (m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[m];
        });
    }

    function formatMarkdown(str) {
        if (!str) return '';
        let formatted = escapeHtml(str);

        // Bold: **text**
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900">$1</strong>');

        // Italic: *text* (excluding bullet lines)
        formatted = formatted.replace(/(?<!\n|^)\*([^*\n]+)\*/g, '<em class="italic">$1</em>');

        // Bullet points: * or •
        formatted = formatted.replace(/(?:^|\n)[•*]\s*(.*?)(?=(?:\n[•*]|\n\n|$))/g, function(match, content) {
            return `<div class="flex items-start gap-1.5 my-1"><span class="text-orange-500 font-bold shrink-0">•</span><span>${content}</span></div>`;
        });

        // Numbered list: 1. 2.
        formatted = formatted.replace(/(?:^|\n)(\d+)\.\s*(.*?)(?=(?:\n\d+\.|\n\n|$))/g, function(match, num, content) {
            return `<div class="flex items-start gap-1.5 my-1"><span class="text-orange-500 font-bold shrink-0">${num}.</span><span>${content}</span></div>`;
        });

        // Paragraph line breaks
        formatted = formatted.replace(/\n\n/g, '<div class="h-2"></div>');
        formatted = formatted.replace(/\n/g, '<br>');

        return formatted;
    }
});
</script>
