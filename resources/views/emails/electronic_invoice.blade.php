@php
    $logoPath = public_path('images/logo.png');
    $logoSrc = (isset($message) && file_exists($logoPath)) ? $message->embed($logoPath) : asset('images/logo.png');
    $paidAt = optional($order->payment?->paid_at ?? $order->updated_at)->format('d/m/Y H:i');
    $customerCode = $order->user?->customer_code ?? ('KH-' . str_pad($order->user_id, 5, '0', STR_PAD_LEFT));
    
    $paymentMethodNames = [
        'payos' => 'Cổng thanh toán trực tuyến PayOS (QR / Thẻ ATM / Visa / Mastercard)',
        'bank_transfer' => 'Chuyển khoản ngân hàng trực tiếp',
        'cod' => 'Thanh toán tiền mặt khi nhận hàng (COD)',
        'installment' => 'Mua trước trả sau / Trả góp định kỳ',
    ];
    $paymentMethodLabel = $paymentMethodNames[$order->payment_method] ?? strtoupper($order->payment_method);
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="vi">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Biên lai thanh toán điện tử - {{ $order->order_code }}</title>
  <!--[if mso]>
  <style type="text/css">
    table, td, p, a, span { font-family: Arial, Helvetica, sans-serif !important; }
  </style>
  <![endif]-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style type="text/css">
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body {
      margin: 0;
      padding: 0;
      width: 100% !important;
      min-width: 100%;
      background-color: #f1f5f9;
      font-family: 'Inter', Arial, Helvetica, sans-serif;
      color: #334155;
    }
    @media only screen and (max-width: 620px) {
      .page-wrapper { padding: 12px !important; }
      .email-shell { width: 100% !important; border-radius: 12px !important; }
      .content-pad { padding: 20px 16px !important; }
      .mobile-stack { display: block !important; width: 100% !important; box-sizing: border-box !important; }
      .mobile-mb { margin-bottom: 16px !important; }
      .item-name { font-size: 13px !important; }
    }
  </style>
</head>

<body style="margin: 0; padding: 0; background-color: #f1f5f9;">
  <center>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="page-wrapper" style="background-color: #f1f5f9; padding: 36px 16px;">
      <tr>
        <td align="center" valign="top">
          <!-- Main Card Container -->
          <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="620" class="email-shell" style="max-width: 620px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0;">
            
            <!-- Top Orange Accent Bar -->
            <tr>
              <td height="6" style="background: linear-gradient(90deg, #ea580c 0%, #f97316 50%, #fb923c 100%); font-size: 1px; line-height: 6px;">&nbsp;</td>
            </tr>

            <!-- Header with Logo and Brand -->
            <tr>
              <td class="content-pad" style="padding: 28px 36px 20px; background-color: #ffffff; border-bottom: 1px solid #f1f5f9;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td valign="middle" align="left">
                      <div style="display: inline-block;">
                        <span style="font-size: 22px; font-weight: 800; color: #ea580c; letter-spacing: -0.5px; text-transform: uppercase;">GiaDụng<span style="color: #0f172a;">Pro</span></span>
                        <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500; margin-top: 2px;">Khởi Nguồn Tiện Nghi, Vẹn Tròn Đẳng Cấp</span>
                      </div>
                    </td>
                    <td valign="middle" align="right">
                      <span style="display: inline-block; padding: 6px 12px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 20px; font-size: 11px; font-weight: 700; color: #047857; text-transform: uppercase; letter-spacing: 0.5px;">
                        ✓ ĐÃ THANH TOÁN
                      </span>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Receipt Title & Hero Banner -->
            <tr>
              <td class="content-pad" style="padding: 24px 36px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td>
                      <div style="font-size: 12px; font-weight: 700; color: #f97316; text-transform: uppercase; letter-spacing: 1px;">HÓA ĐƠN ĐIỆN TỬ (E-RECEIPT)</div>
                      <h1 style="margin: 6px 0 4px; font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1.3;">Xác Nhận Thanh Toán Thành Công</h1>
                      <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.5;">
                        Cảm ơn quý khách <strong>{{ $order->receiver_name }}</strong> đã mua sắm tại GiaDụngPro. Dưới đây là biên lai thanh toán điện tử hợp lệ của quý khách.
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Order Key Meta Info Grid -->
            <tr>
              <td class="content-pad" style="padding: 24px 36px; background-color: #ffffff;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                  <tr>
                    <td class="mobile-stack mobile-mb" width="50%" valign="top" style="padding: 8px 12px;">
                      <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Mã đơn hàng:</div>
                      <div style="font-size: 15px; font-weight: 800; color: #ea580c; font-family: monospace; margin-top: 3px;">#{{ $order->order_code }}</div>
                      
                      <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-top: 14px;">Mã khách hàng:</div>
                      <div style="font-size: 13px; font-weight: 700; color: #0f172a; font-family: monospace; margin-top: 3px;">{{ $customerCode }}</div>
                    </td>
                    <td class="mobile-stack" width="50%" valign="top" style="padding: 8px 12px;">
                      <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Thời gian thanh toán:</div>
                      <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-top: 3px;">{{ $paidAt }}</div>
                      
                      <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-top: 14px;">Phương thức thanh toán:</div>
                      <div style="font-size: 12px; font-weight: 600; color: #0f172a; margin-top: 3px; line-height: 1.4;">{{ $paymentMethodLabel }}</div>
                      
                      @if($order->payment && $order->payment->transaction_reference)
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-top: 14px;">Mã giao dịch ngân hàng:</div>
                        <div style="font-size: 12px; font-weight: 700; color: #0369a1; font-family: monospace; margin-top: 3px;">{{ $order->payment->transaction_reference }}</div>
                      @endif
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Buyer & Shipping Info -->
            <tr>
              <td class="content-pad" style="padding: 0 36px 20px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px dashed #e2e8f0; padding-top: 16px;">
                  <tr>
                    <td>
                      <div style="font-size: 12px; font-weight: 700; color: #0f172a; text-transform: uppercase; margin-bottom: 8px;">Thông tin người nhận & Địa chỉ giao hàng:</div>
                      <div style="font-size: 13px; color: #334155; line-height: 1.6;">
                        <strong>Người nhận:</strong> {{ $order->receiver_name }} ({{ $order->receiver_phone }})<br>
                        <strong>Email nhận biên lai:</strong> {{ $order->receipt_email ?: $order->receiver_email }}<br>
                        <strong>Địa chỉ giao:</strong> {{ $order->address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}<br>
                        @if($order->note)
                          <strong>Ghi chú:</strong> <span style="font-style: italic; color: #64748b;">{{ $order->note }}</span>
                        @endif
                      </div>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Purchased Items Table -->
            <tr>
              <td class="content-pad" style="padding: 10px 36px 24px;">
                <div style="font-size: 13px; font-weight: 700; color: #0f172a; text-transform: uppercase; margin-bottom: 12px;">Chi tiết sản phẩm đã mua</div>
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                  <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                      <th align="left" style="padding: 12px 14px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Sản phẩm</th>
                      <th align="center" style="padding: 12px 10px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; width: 60px;">SL</th>
                      <th align="right" style="padding: 12px 14px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; width: 110px;">Thành tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($order->items as $item)
                      <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td align="left" style="padding: 12px 14px; vertical-align: top;">
                          <div class="item-name" style="font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.4;">
                            {{ $item->product_name }}
                          </div>
                          @if($item->color || $item->size)
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                              Phân loại:
                              @if($item->color) Màu: {{ $item->color->name }} @endif
                              @if($item->size) | Size: {{ $item->size->name }} @endif
                            </div>
                          @endif
                          @if($item->warranty)
                            <div style="font-size: 11px; color: #ea580c; font-weight: 600; margin-top: 2px;">
                              + Bảo hành: {{ $item->warranty->name }} (+{{ number_format($item->warranty->price, 0, ',', '.') }} ₫)
                            </div>
                          @endif
                          <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                            Đơn giá: {{ number_format($item->price, 0, ',', '.') }} ₫
                          </div>
                        </td>
                        <td align="center" style="padding: 12px 10px; vertical-align: top; font-size: 13px; font-weight: 700; color: #334155;">
                          {{ $item->quantity }}
                        </td>
                        <td align="right" style="padding: 12px 14px; vertical-align: top; font-size: 13px; font-weight: 700; color: #0f172a;">
                          {{ number_format($item->total, 0, ',', '.') }} ₫
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </td>
            </tr>

            <!-- Summary Calculations -->
            <tr>
              <td class="content-pad" style="padding: 0 36px 28px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td width="40%" class="mobile-stack">&nbsp;</td>
                    <td width="60%" class="mobile-stack">
                      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 13px; color: #475569;">
                        <tr>
                          <td align="left" style="padding: 6px 0;">Tổng tiền hàng (Tạm tính):</td>
                          <td align="right" style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</td>
                        </tr>
                        @if($order->discount_amount > 0)
                          <tr>
                            <td align="left" style="padding: 6px 0; color: #16a34a;">
                              Giảm giá sản phẩm {{ $order->coupon_code && (!$order->shipping_discount || $order->shipping_discount <= 0) ? '(' . $order->coupon_code . ')' : '' }}:
                            </td>
                            <td align="right" style="padding: 6px 0; font-weight: 700; color: #16a34a;">-{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</td>
                          </tr>
                        @endif
                        <tr>
                          <td align="left" style="padding: 6px 0;">Phí vận chuyển giao hàng:</td>
                          <td align="right" style="padding: 6px 0; font-weight: 600; color: #0f172a;">
                            {{ $order->shipping_fee > 0 ? number_format($order->shipping_fee, 0, ',', '.') . ' ₫' : 'Miễn phí' }}
                          </td>
                        </tr>
                        @if($order->shipping_discount > 0)
                          <tr>
                            <td align="left" style="padding: 6px 0; color: #16a34a;">
                              Giảm phí vận chuyển {{ $order->coupon_code ? '(' . $order->coupon_code . ')' : '' }}:
                            </td>
                            <td align="right" style="padding: 6px 0; font-weight: 700; color: #16a34a;">-{{ number_format($order->shipping_discount, 0, ',', '.') }} ₫</td>
                          </tr>
                        @endif
                        <tr>
                          <td colspan="2" style="padding: 8px 0;">
                            <div style="border-top: 2px solid #e2e8f0;"></div>
                          </td>
                        </tr>
                        <tr>
                          <td align="left" style="padding: 6px 0; font-size: 15px; font-weight: 800; color: #0f172a;">TỔNG CỘNG ĐÃ THANH TOÁN:</td>
                          <td align="right" style="padding: 6px 0; font-size: 18px; font-weight: 800; color: #ea580c;">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Call to action button -->
            <tr>
              <td align="center" style="padding: 0 36px 32px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td align="center" style="border-radius: 12px; background-color: #ea580c; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);">
                      <a href="{{ route('orders.show', $order->id) }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Tra cứu chi tiết đơn hàng trực tuyến →
                      </a>
                    </td>
                  </tr>
                </table>
                <p style="margin: 12px 0 0; font-size: 12px; color: #94a3b8;">
                  Quý khách có thể xem hành trình đóng gói và vận chuyển trực tiếp bằng cách nhấn nút trên.
                </p>
              </td>
            </tr>

            <!-- Legal / Electronic Invoice Guarantee Note -->
            <tr>
              <td style="padding: 20px 36px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td style="font-size: 11px; color: #64748b; line-height: 1.6;">
                      <strong style="color: #475569;">Ghi chú hóa đơn điện tử:</strong><br>
                      • Đây là biên lai thanh toán điện tử chính thức được tạo tự động từ hệ thống GiaDụngPro.<br>
                      • Biên lai này có giá trị xác nhận quý khách đã hoàn tất nghĩa vụ thanh toán cho đơn hàng #{{ $order->order_code }}.<br>
                      • Quý khách vui lòng lưu giữ email này để làm căn cứ bảo hành và đổi trả khi cần thiết.
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td style="padding: 28px 36px; background-color: #0f172a; color: #94a3b8; text-align: center;">
                <div style="font-size: 14px; font-weight: 800; color: #ffffff; letter-spacing: 0.5px; text-transform: uppercase;">
                  GiaDụng<span style="color: #f97316;">Pro</span> Official Store
                </div>
                <div style="font-size: 12px; color: #cbd5e1; margin-top: 6px;">
                  Hệ thống phân phối thiết bị gia dụng và đồ dùng nhà bếp cao cấp chính hãng
                </div>
                
                <div style="margin: 16px 0; border-top: 1px solid #334155;"></div>

                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td align="center" style="font-size: 11px; color: #94a3b8; line-height: 1.8;">
                      📞 Hotline hỗ trợ 24/7: <strong style="color: #ffffff;">1900 8888</strong> &nbsp;|&nbsp; ✉️ Email: <strong style="color: #ffffff;">hotro@giadungpro.vn</strong><br>
                      🏢 Địa chỉ: 123 Đường Công Nghệ, Khu Đô Thị Mới, Hà Nội / TP.HCM<br>
                      🌐 Website: <a href="{{ url('/') }}" style="color: #f97316; text-decoration: none; font-weight: 600;">giadungpro.vn</a>
                    </td>
                  </tr>
                </table>

                <div style="font-size: 10px; color: #64748b; margin-top: 16px;">
                  © {{ date('Y') }} GiaDụngPro. Tất cả các quyền được bảo lưu. Email này được gửi tự động do bạn chọn nhận biên lai điện tử khi thanh toán.
                </div>
              </td>
            </tr>

          </table>
        </td>
      </tr>
    </table>
  </center>
</body>
</html>
