Add-Type -AssemblyName System.Drawing

$brandDir = "c:\laragon\www\giadung\storage\app\public\brands"

function Process-BrandLogo {
    param (
        [string]$ImagePath,
        [int]$Padding = 20,
        [double]$ScanHeightRatio = 0.65 # Giới hạn quét chiều cao (nếu logo có chữ phụ ở dưới cần loại bỏ)
    )

    if (-not (Test-Path $ImagePath)) {
        Write-Warning "Không tìm thấy file: $ImagePath"
        return
    }

    Write-Host "Đang xử lý ảnh: $ImagePath"

    # 1. Đọc ảnh vào MemoryStream để không khóa (file lock) file gốc trên ổ đĩa
    $bytes = [System.IO.File]::ReadAllBytes($ImagePath)
    $ms = New-Object System.IO.MemoryStream(,$bytes)
    $srcImage = [System.Drawing.Image]::FromStream($ms)
    
    # Tạo bản sao Bitmap 32-bit ARGB độc lập
    $bmp = New-Object System.Drawing.Bitmap($srcImage)
    $srcImage.Dispose()
    $ms.Dispose()

    $w = $bmp.Width
    $h = $bmp.Height
    Write-Host "Kích thước ảnh gốc: ${w}x${h}"

    $scanLimitY = if ($ScanHeightRatio -gt 0 -and $ScanHeightRatio -le 1.0) {
        [int]($h * $ScanHeightRatio)
    } else {
        $h
    }

    # 2. Tìm Bounding Box của logo (bỏ qua nền trắng và pixel đã trong suốt)
    $minX = $w; $maxX = -1
    $minY = $h; $maxY = -1

    for ($y = 0; $y -lt $scanLimitY; $y++) {
        for ($x = 0; $x -lt $w; $x++) {
            $pixel = $bmp.GetPixel($x, $y)

            # Chỉ xét các pixel có hiển thị (Alpha > 30) và không phải nền trắng sáng
            if ($pixel.A -gt 30) {
                $brightness = ($pixel.R + $pixel.G + $pixel.B) / 3.0
                if ($brightness -lt 220) {
                    if ($x -lt $minX) { $minX = $x }
                    if ($x -gt $maxX) { $maxX = $x }
                    if ($y -lt $minY) { $minY = $y }
                    if ($y -gt $maxY) { $maxY = $y }
                }
            }
        }
    }

    Write-Host "Bounding box: minX=$minX, maxX=$maxX, minY=$minY, maxY=$maxY"

    if ($maxX -ge $minX -and $maxY -ge $minY) {
        # 3. Tính toán vùng Crop cân xứng (Symmetrical Padding) và không vượt khung
        $cropX = [Math]::Max(0, $minX - $Padding)
        $cropY = [Math]::Max(0, $minY - $Padding)
        $cropRight = [Math]::Min($w - 1, $maxX + $Padding)
        $cropBottom = [Math]::Min($h - 1, $maxY + $Padding)

        $cropW = $cropRight - $cropX + 1
        $cropH = $cropBottom - $cropY + 1

        $croppedBmp = New-Object System.Drawing.Bitmap($cropW, $cropH, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)

        # 4. Chuyển đổi nền trong suốt và khử viền trắng hào quang (Un-blend White Halo)
        for ($cy = 0; $cy -lt $cropH; $cy++) {
            for ($cx = 0; $cx -lt $cropW; $cx++) {
                $srcX = $cropX + $cx
                $srcY = $cropY + $cy
                $p = $bmp.GetPixel($srcX, $srcY)

                if ($p.A -le 30) {
                    # Nếu điểm ảnh gốc đã trong suốt, giữ nguyên trong suốt
                    $croppedBmp.SetPixel($cx, $cy, [System.Drawing.Color]::FromArgb(0, 0, 0, 0))
                    continue
                }

                $brightness = ($p.R + $p.G + $p.B) / 3.0

                if ($brightness -ge 245) {
                    # Nền trắng hoàn toàn -> trong suốt
                    $croppedBmp.SetPixel($cx, $cy, [System.Drawing.Color]::FromArgb(0, 0, 0, 0))
                } elseif ($brightness -gt 195) {
                    # Vùng biên chuyển tiếp (Anti-aliased edge):
                    # Tính toán tỷ lệ alpha và giải phóng màu nền trắng (Un-premultiply white)
                    $alphaRatio = (245.0 - $brightness) / 50.0
                    $alpha = [int][Math]::Round(255 * $alphaRatio)
                    $alpha = [Math]::Max(0, [Math]::Min(255, $alpha))

                    # Khử đóng góp của nền trắng (255) vào màu pixel gốc:
                    # C_fg = (C_blended - 255 * (1 - alphaRatio)) / alphaRatio
                    $fgR = [int][Math]::Max(0, [Math]::Min(255, [Math]::Round(($p.R - 255 * (1.0 - $alphaRatio)) / [Math]::Max(0.01, $alphaRatio))))
                    $fgG = [int][Math]::Max(0, [Math]::Min(255, [Math]::Round(($p.G - 255 * (1.0 - $alphaRatio)) / [Math]::Max(0.01, $alphaRatio))))
                    $fgB = [int][Math]::Max(0, [Math]::Min(255, [Math]::Round(($p.B - 255 * (1.0 - $alphaRatio)) / [Math]::Max(0.01, $alphaRatio))))

                    $croppedBmp.SetPixel($cx, $cy, [System.Drawing.Color]::FromArgb($alpha, $fgR, $fgG, $fgB))
                } else {
                    # Vùng chữ/logo đậm màu -> giữ nguyên màu với alpha tối đa
                    $croppedBmp.SetPixel($cx, $cy, [System.Drawing.Color]::FromArgb(255, $p.R, $p.G, $p.B))
                }
            }
        }

        # Giải phóng bộ nhớ của ảnh gốc trước khi ghi đè
        $bmp.Dispose()

        # 5. Lưu ra file tạm và ghi đè an toàn
        $tempPath = [System.IO.Path]::Combine([System.IO.Path]::GetDirectoryName($ImagePath), ([System.IO.Path]::GetFileNameWithoutExtension($ImagePath) + "_temp.png"))
        $croppedBmp.Save($tempPath, [System.Drawing.Imaging.ImageFormat]::Png)
        $croppedBmp.Dispose()

        # Di chuyển đè vào file gốc
        Move-Item -Path $tempPath -Destination $ImagePath -Force
        Write-Host "=> Đã xử lý và tối ưu xong: $ImagePath" -ForegroundColor Green
    } else {
        $bmp.Dispose()
        Write-Warning "Không tìm thấy nội dung logo hợp lệ trong phạm vi quét."
    }
}

# Ví dụ thực thi với cuckoo.png
$cuckooPath = Join-Path $brandDir "cuckoo.png"
if (Test-Path $cuckooPath) {
    Process-BrandLogo -ImagePath $cuckooPath -Padding 20 -ScanHeightRatio 0.65
} else {
    Write-Host "Không tìm thấy $cuckooPath để xử lý. Bạn có thể gọi: Process-BrandLogo -ImagePath '<đường_dẫn_ảnh>'" -ForegroundColor Cyan
}
