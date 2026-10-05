<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameReward;
use App\Models\GameAttempt;
use App\Models\DailyCheckin;
use App\Models\Voucher;
use App\Models\UserVoucher;
use App\Services\PointService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class GameController extends Controller
{
    protected $pointService;
    protected $notificationService;

    public function __construct(PointService $pointService, NotificationService $notificationService)
    {
        $this->pointService = $pointService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();
        
        $hasCheckedInToday = DailyCheckin::where('user_id', $user->id)
            ->where('checkin_date', $today)
            ->exists();

        $streak = DailyCheckin::where('user_id', $user->id)
            ->orderBy('checkin_date', 'desc')
            ->value('streak') ?? 0;

        // Get games list
        $luckyWheelGame = Game::where('slug', 'vong-quay-may-man')->where('status', 'active')->first();
        $evenOddGame = Game::where('slug', 'doan-chan-le')->where('status', 'active')->first();

        // Attempts today
        $luckyWheelAttemptsToday = GameAttempt::where('user_id', $user->id)
            ->where('game_id', $luckyWheelGame?->id)
            ->whereDate('created_at', $today)
            ->count();

        $evenOddAttemptsToday = GameAttempt::where('user_id', $user->id)
            ->where('game_id', $evenOddGame?->id)
            ->whereDate('created_at', $today)
            ->count();

        // History
        $checkinHistory = DailyCheckin::where('user_id', $user->id)->latest()->limit(7)->get();
        $attemptHistory = GameAttempt::where('user_id', $user->id)->with(['game', 'reward'])->latest()->limit(10)->get();
        $pointHistory = $user->pointTransactions()->latest()->limit(10)->get();

        // Vouchers that can be claimed or user vouchers
        $myVouchers = UserVoucher::where('user_id', $user->id)->with('voucher')->latest()->get();
        
        // Reward items available for redemption
        $rewardItems = \App\Models\RewardItem::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return view('games.index', compact(
            'hasCheckedInToday',
            'streak',
            'luckyWheelGame',
            'evenOddGame',
            'luckyWheelAttemptsToday',
            'evenOddAttemptsToday',
            'checkinHistory',
            'attemptHistory',
            'pointHistory',
            'myVouchers',
            'rewardItems'
        ));
    }

    /**
     * Handle daily checkin.
     */
    public function checkin(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        try {
            $result = DB::transaction(function () use ($user, $today, $yesterday) {
                // Check if already checked in
                $existing = DailyCheckin::where('user_id', $user->id)
                    ->where('checkin_date', $today)
                    ->first();

                if ($existing) {
                    throw new Exception("Bạn đã điểm danh hôm nay rồi.");
                }

                // Check yesterday checkin to continue streak
                $lastCheckin = DailyCheckin::where('user_id', $user->id)
                    ->where('checkin_date', $yesterday)
                    ->first();

                $streak = 1;
                if ($lastCheckin) {
                    $streak = $lastCheckin->streak + 1;
                }

                // Reward points formula: 10 base + 2 per streak day, max 50 points
                $points = min(10 + ($streak * 2), 50);

                DailyCheckin::create([
                    'user_id' => $user->id,
                    'checkin_date' => $today,
                    'points_earned' => $points,
                    'streak' => $streak,
                ]);

                $this->pointService->addPoints(
                    $user,
                    $points,
                    'checkin',
                    null,
                    "Điểm danh ngày liên tiếp thứ {$streak}"
                );

                return ['points' => $points, 'streak' => $streak];
            });

            return back()->with('success', "Điểm danh thành công! Bạn nhận được +{$result['points']} điểm. (Chuỗi ngày: {$result['streak']} ngày)");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Spin the lucky wheel.
     */
    public function spin(Request $request)
    {
        $user = Auth::user();
        $game = Game::where('slug', 'vong-quay-may-man')->where('status', 'active')->first();

        if (!$game) {
            return response()->json(['success' => false, 'message' => 'Trò chơi này hiện đã đóng.']);
        }

        $today = now()->toDateString();
        $attemptsToday = GameAttempt::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->whereDate('created_at', $today)
            ->count();

        // 1 free spin per day, then 20 points per spin. Max 5 spins per day.
        if ($attemptsToday >= 5) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đạt giới hạn 5 lượt quay tối đa cho ngày hôm nay.']);
        }

        $cost = 0;
        if ($attemptsToday >= 1) {
            $cost = 20; // 20 points
            if ($user->points < $cost) {
                return response()->json(['success' => false, 'message' => 'Bạn không đủ điểm thưởng để thực hiện lượt quay này (Cần 20 điểm).']);
            }
        }

        try {
            $result = DB::transaction(function () use ($user, $game, $cost) {
                if ($cost > 0) {
                    $this->pointService->deductPoints($user, $cost, 'game_play', (string)$game->id, "Lượt quay vòng quay may mắn");
                }

                // Choose reward based on probability
                $rewards = GameReward::where('game_id', $game->id)->where('status', 'active')->get();
                if ($rewards->isEmpty()) {
                    throw new Exception("Hệ thống giải thưởng chưa được cấu hình.");
                }

                // Probability calculation
                $totalProb = $rewards->sum('probability');
                $rand = mt_rand(0, 100000) / 1000; // 0 to 100.000
                
                $chosenReward = null;
                $cumulative = 0;
                foreach ($rewards as $reward) {
                    $cumulative += $reward->probability;
                    if ($rand <= $cumulative) {
                        $chosenReward = $reward;
                        break;
                    }
                }

                if (!$chosenReward) {
                    $chosenReward = $rewards->last();
                }

                // Check limits: if chosen reward is exhausted
                if ($chosenReward->quantity_limit !== -1 && $chosenReward->quantity_claimed >= $chosenReward->quantity_limit) {
                    $availableReward = GameReward::where('game_id', $game->id)
                        ->where('status', 'active')
                        ->where(function($q) {
                            $q->where('quantity_limit', -1)
                              ->orWhereColumn('quantity_claimed', '<', 'quantity_limit');
                        })
                        ->where('type', 'none')
                        ->first();

                    if (!$availableReward) {
                        $availableReward = GameReward::where('game_id', $game->id)
                            ->where('status', 'active')
                            ->where(function($q) {
                                $q->where('quantity_limit', -1)
                                  ->orWhereColumn('quantity_claimed', '<', 'quantity_limit');
                            })
                            ->first();
                    }

                    if ($availableReward) {
                        $chosenReward = $availableReward;
                    }
                }

                // Process reward
                $rewardMsg = "";
                if ($chosenReward->type === 'points') {
                    $pointsAwarded = (int)$chosenReward->value;
                    $this->pointService->addPoints($user, $pointsAwarded, 'game_win', (string)$game->id, "Trúng thưởng vòng quay may mắn: {$chosenReward->name}");
                    $rewardMsg = "Bạn đã nhận được +{$pointsAwarded} điểm!";
                } elseif ($chosenReward->type === 'voucher') {
                    $voucher = Voucher::where('code', $chosenReward->value)->where('status', 'active')->first();
                    if ($voucher) {
                        UserVoucher::create([
                            'user_id' => $user->id,
                            'voucher_id' => $voucher->id,
                        ]);
                        $rewardMsg = "Bạn nhận được mã giảm giá: {$voucher->code}!";
                        $this->notificationService->notify($user, "Nhận voucher mới", "Chúc mừng! Bạn đã quay trúng voucher {$voucher->code} trị giá " . number_format($voucher->value, 0, ',', '.') . " ₫.", "voucher", route('profile.index'));
                    } else {
                        // Fallback if voucher is inactive/missing
                        $noneReward = GameReward::where('game_id', $game->id)->where('type', 'none')->first();
                        if ($noneReward) {
                            $chosenReward = $noneReward;
                        }
                        $rewardMsg = "Chúc bạn may mắn lần sau!";
                    }
                } else {
                    $rewardMsg = "Chúc bạn may mắn lần sau!";
                }

                if ($chosenReward && $chosenReward->quantity_limit !== -1) {
                    $chosenReward->increment('quantity_claimed');
                }

                GameAttempt::create([
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'reward_id' => $chosenReward ? $chosenReward->id : null,
                    'result' => $rewardMsg,
                    'points_spent' => $cost,
                ]);

                // Determine angle for frontend: each slice corresponds to index
                // Let's find index of this reward to return correct rotation angle
                $allActiveRewards = GameReward::where('game_id', $game->id)->where('status', 'active')->get();
                $index = 0;
                foreach ($allActiveRewards as $i => $r) {
                    if ($r->id === $chosenReward->id) {
                        $index = $i;
                        break;
                    }
                }
                $sliceCount = $allActiveRewards->count();
                $angle = 360 - ($index * (360 / ($sliceCount ?: 1))) - (360 / (($sliceCount ?: 1) * 2));

                return [
                    'success' => true,
                    'reward_name' => $chosenReward->name,
                    'reward_msg' => $rewardMsg,
                    'angle' => $angle,
                    'user_points' => $user->fresh()->points,
                ];
            });

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Guess Even/Odd Game.
     */
    public function playEvenOdd(Request $request)
    {
        $request->validate([
            'choice' => 'required|in:even,odd',
            'bet' => 'required|integer|in:10,20,50',
        ]);

        $user = Auth::user();
        $game = Game::where('slug', 'doan-chan-le')->where('status', 'active')->first();

        if (!$game) {
            return back()->with('error', 'Trò chơi này hiện đã đóng.');
        }

        $today = now()->toDateString();
        $attemptsToday = GameAttempt::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->whereDate('created_at', $today)
            ->count();

        if ($attemptsToday >= 10) {
            return back()->with('error', 'Bạn đã đạt giới hạn 10 lượt chơi chẵn lẻ tối đa hôm nay.');
        }

        $bet = $request->bet;
        if ($user->points < $bet) {
            return back()->with('error', 'Bạn không có đủ điểm thưởng để chơi.');
        }

        try {
            DB::transaction(function () use ($user, $game, $bet, $request) {
                // Deduct points
                $this->pointService->deductPoints($user, $bet, 'game_play', (string)$game->id, "Chơi game đoán chẵn lẻ");

                // Generate result
                $num = rand(1, 100);
                $isEven = ($num % 2 === 0);
                $resultStr = $isEven ? 'even' : 'odd';
                $won = ($request->choice === $resultStr);

                $rewardText = "";
                $rewardId = null;

                if ($won) {
                    $winAmount = $bet * 2;
                    $this->pointService->addPoints($user, $winAmount, 'game_win', (string)$game->id, "Thắng game đoán chẵn lẻ");
                    $rewardText = "Thắng! Dự đoán đúng số {$num} (" . ($isEven ? "Chẵn" : "Lẻ") . "), nhận được {$winAmount} điểm.";
                } else {
                    $rewardText = "Thua! Kết quả số {$num} (" . ($isEven ? "Chẵn" : "Lẻ") . "), mất {$bet} điểm.";
                }

                GameAttempt::create([
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'reward_id' => null,
                    'result' => $rewardText,
                    'points_spent' => $bet,
                ]);

                if ($won) {
                    session()->flash('success', "Chúc mừng bạn đã đoán đúng! Kết quả số {$num} là " . ($isEven ? "CHẴN" : "LẺ") . ". Bạn thắng +{$winAmount} điểm.");
                } else {
                    session()->flash('error', "Rất tiếc, dự đoán sai! Kết quả số {$num} là " . ($isEven ? "CHẴN" : "LẺ") . ". Bạn bị trừ -{$bet} điểm.");
                }
            });

            return back();
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Redeem point for rewards items (Voucher or Gift).
     */
    public function redeemReward(Request $request)
    {
        $request->validate([
            'reward_item_id' => 'required|exists:reward_items,id',
        ]);

        $user = Auth::user();
        $item = \App\Models\RewardItem::findOrFail($request->reward_item_id);

        if ($item->status !== 'active' || now()->lt($item->start_date) || now()->gt($item->end_date)) {
            return back()->with('error', 'Phần quà này hiện không có sẵn để đổi.');
        }

        if ($item->stock_quantity <= 0) {
            return back()->with('error', 'Phần quà này đã hết hàng.');
        }

        if ($user->points < $item->points_required) {
            return back()->with('error', 'Bạn không có đủ điểm thưởng để đổi phần quà này.');
        }

        try {
            DB::transaction(function () use ($user, $item) {
                // Deduct points
                $this->pointService->deductPoints(
                    $user,
                    $item->points_required,
                    'redeem',
                    (string)$item->id,
                    "Đổi quà tặng: {$item->name}"
                );

                // Decrement stock
                $item->decrement('stock_quantity');

                // Create redemption record
                \App\Models\RewardRedemption::create([
                    'user_id' => $user->id,
                    'reward_item_id' => $item->id,
                    'points_spent' => $item->points_required,
                    'status' => 'completed',
                ]);

                // If reward item is a voucher (check if it can map to a voucher code)
                // Let's see if there is an active voucher with the same code or name
                // To keep it simple, if name contains a code like "VOUCHER...", claim it
                $voucherCode = trim(explode('-', $item->name)[0]);
                $voucher = Voucher::where('code', $voucherCode)->where('status', 'active')->first();
                if ($voucher) {
                    UserVoucher::create([
                        'user_id' => $user->id,
                        'voucher_id' => $voucher->id,
                    ]);
                    $this->notificationService->notify($user, "Đổi voucher thành công", "Bạn đã đổi thành công voucher {$voucher->code} từ điểm tích lũy.", "voucher", route('profile.index'));
                } else {
                    $this->notificationService->notify($user, "Đổi quà thành công", "Bạn đã đổi quà {$item->name} thành công. Chúng tôi sẽ sớm liên hệ giao quà.", "system");
                }
            });

            return back()->with('success', 'Đổi quà thành công! Điểm thưởng đã được khấu trừ.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
