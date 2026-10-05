<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class PointService
{
    /**
     * Add points to a user.
     */
    public function addPoints(User $user, int $amount, string $type, ?string $referenceId = null, ?string $description = null): void
    {
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($user, $amount, $type, $referenceId, $description) {
            $userModel = User::lockForUpdate()->find($user->id);
            $userModel->increment('points', $amount);

            PointTransaction::create([
                'user_id' => $userModel->id,
                'amount' => $amount,
                'type' => $type,
                'reference_id' => $referenceId,
                'description' => $description,
            ]);
        });
    }

    /**
     * Deduct points from a user.
     * @throws Exception
     */
    public function deductPoints(User $user, int $amount, string $type, ?string $referenceId = null, ?string $description = null): void
    {
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($user, $amount, $type, $referenceId, $description) {
            $userModel = User::lockForUpdate()->find($user->id);

            if ($userModel->points < $amount) {
                throw new Exception("Tài khoản của bạn không đủ điểm thưởng (Hiện có: {$userModel->points} điểm, cần: {$amount} điểm).");
            }

            $userModel->decrement('points', $amount);

            PointTransaction::create([
                'user_id' => $userModel->id,
                'amount' => -$amount,
                'type' => $type,
                'reference_id' => $referenceId,
                'description' => $description,
            ]);
        });
    }
}
