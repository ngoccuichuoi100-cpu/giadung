<?php

namespace App\Services;

class DeliveryService
{
    /**
     * Compute estimated delivery date range based on province code.
     * Returns an array with start date, end date, and user-friendly text description.
     */
    public function getEstDeliveryRange($provinceCode)
    {
        $minDays = 3;
        $maxDays = 5;

        // Nhóm Miền Bắc (Hà Nội, Hải Phòng, Bắc Ninh, Hưng Yên...)
        $northernCodes = ['01', '02', '04', '06', '08', '10', '11', '12', '14', '15', '17', '19', '20', '22', '24', '25', '26', '27', '30', '31', '33', '34', '35', '36', '37'];
        // Nhóm Miền Trung & Tây Nguyên (Đà Nẵng, Huế, Quảng Nam, Khánh Hòa...)
        $centralCodes = ['38', '40', '42', '44', '45', '46', '48', '49', '51', '52', '54', '56', '58', '60', '62', '64', '66', '67', '68'];
        // Nhóm Miền Nam (TP.HCM, Bình Dương, Đồng Nai, Cần Thơ...)
        $southernCodes = ['70', '72', '74', '75', '77', '79', '80', '82', '83', '84', '86', '87', '89', '91', '92', '93', '94', '95', '96'];

        if ($provinceCode === '01' || $provinceCode === '79') {
            // Hà Nội hoặc TP.HCM: Giao hỏa tốc 1 - 2 ngày
            $minDays = 1;
            $maxDays = 2;
        } elseif (in_array($provinceCode, $northernCodes)) {
            $minDays = 2;
            $maxDays = 3;
        } elseif (in_array($provinceCode, $centralCodes)) {
            $minDays = 2;
            $maxDays = 4;
        } elseif (in_array($provinceCode, $southernCodes)) {
            $minDays = 2;
            $maxDays = 4;
        } else {
            $minDays = 3;
            $maxDays = 5;
        }

        $start = now()->addDays($minDays);
        $end = now()->addDays($maxDays);

        return [
            'start' => $start,
            'end' => $end,
            'text' => 'Dự kiến giao hàng từ ngày ' . $start->format('d/m/Y') . ' đến ngày ' . $end->format('d/m/Y') . ' (Thời gian dự kiến, không cam kết chính xác)'
        ];
    }
}
