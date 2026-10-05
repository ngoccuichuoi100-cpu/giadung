@php
    $deliveryIconSrc = isset($message) ? $message->embed(public_path('images/email/icon-delivery.png')) : asset('images/email/icon-delivery.png');
    $qualityIconSrc = isset($message) ? $message->embed(public_path('images/email/icon-quality.png')) : asset('images/email/icon-quality.png');
    $supportIconSrc = isset($message) ? $message->embed(public_path('images/email/icon-support.png')) : asset('images/email/icon-support.png');
@endphp
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="vi">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GiaDụngPro - Khởi Nguồn Tiện Nghi, Vẹn Tròn Đẳng Cấp</title>
  <!--[if mso]>
  <style type="text/css">
    table, td, p, a, span { font-family: Arial, Helvetica, sans-serif !important; }
  </style>
  <![endif]-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
  <style type="text/css">
    body,
    table,
    td,
    a {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table,
    td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      -ms-interpolation-mode: bicubic;
      border: 0;
      outline: none;
      text-decoration: none;
    }

    body {
      margin: 0;
      padding: 0;
      width: 100% !important;
      min-width: 100%;
      background-color: #eef5f7;
      font-family: 'Inter', Arial, Helvetica, sans-serif;
    }

    @media only screen and (max-width: 620px) {
      .page-wrapper {
        padding: 0 !important;
      }

      .email-shell {
        width: 100% !important;
        border-radius: 0 !important;
      }
    }

    @media only screen and (max-width: 480px) {
      .header-pad {
        padding: 20px 24px !important;
      }

      .hero-pad {
        padding: 28px 24px 34px !important;
      }

      .welcome-pad {
        padding: 24px 20px !important;
        margin: 0 20px !important;
      }

      .benefits-pad {
        padding: 36px 24px !important;
      }

      .cta-pad {
        padding: 28px 20px !important;
        margin: 0 20px 30px !important;
      }

      .footer-pad {
        padding: 28px 20px !important;
      }

      .headline {
        font-size: 26px !important;
        line-height: 1.25 !important;
      }

      .brand-note {
        display: none !important;
      }

      .benefit-cell {
        display: block !important;
        width: 100% !important;
        padding-bottom: 12px !important;
      }
    }
  </style>
</head>

<body
  style="margin: 0; padding: 0; background-color: #eef5f7; background-image: radial-gradient(circle at 12% 10%, rgba(66, 192, 251, 0.14), transparent 25rem);">
  <center>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="page-wrapper"
      style="background-color: transparent; padding: 48px 20px;">
      <tr>
        <td align="center" valign="top">
          <!-- Main Card Container -->
          <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" class="email-shell"
            style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 18px; overflow: hidden; box-shadow: 0 18px 60px rgba(37, 52, 59, 0.10);">

            <!-- Top Cyan Accent Line -->
            <tr>
              <td height="5" style="background-color: #42c0fb; font-size: 1px; line-height: 5px;">&nbsp;</td>
            </tr>

            <!-- Hero Section -->
            <tr>
              <td class="hero-pad" align="center" style="padding: 34px 42px 30px;">

                <!-- Eyebrow -->
                <div
                  style="font-family: 'Inter', Arial, sans-serif; font-size: 10px; font-weight: 700; color: #159edc; letter-spacing: 0.18em; text-transform: uppercase;">
                  CHÀO MỪNG BẠN TRỞ THÀNH THÀNH VIÊN GIADUNGPRO
                </div>

                <!-- Headline -->
                <h1 class="headline"
                  style="margin: 12px 0 0; font-family: 'Inter', Arial, sans-serif; font-size: 34px; font-weight: 700; color: #25343b; line-height: 1.2; letter-spacing: -0.035em;">
                  Nâng Niu Tổ Ấm,<br />Trọn Vẹn Từng Khoảnh Khắc
                </h1>

                <!-- Hero Copy -->
                <p
                  style="max-width: 440px; margin: 18px auto 0; font-family: 'Inter', Arial, sans-serif; font-size: 15px; color: #607078; line-height: 1.72;">
                  Nơi mang đến những giải pháp chăm sóc gia đình thông minh, an toàn và ngập tràn cảm hứng yêu thương.
                </p>
              </td>
            </tr>

            <!-- Welcome Box -->
            <tr>
              <td style="padding: 0 42px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                  style="background-color: #f6fcff; border-radius: 20px;">
                  <tr>
                    <td class="welcome-pad" style="padding: 36px 38px;">
                      <h2
                        style="margin: 0 0 10px; font-family: 'Inter', Arial, sans-serif; font-size: 24px; font-weight: 700; color: #25343b; line-height: 1.25; letter-spacing: -0.02em;">
                        Thân gửi {{ $user->name ?? $user->username }},
                      </h2>
                      <p
                        style="margin: 0; font-family: 'Inter', Arial, sans-serif; font-size: 15px; color: #607078; line-height: 1.72;">
                        GiaDụngPro cảm ơn bạn đã lựa chọn tin tưởng. Ngôi nhà luôn là nơi bình yên
                        nhất để trở về, và sứ mệnh của chúng tôi là san sẻ bớt nỗi vất vả trong công việc gia đình
                        thường nhật. Bằng những sản phẩm gia dụng chất lượng cao, bền bỉ và dễ sử dụng, GiaDụngPro hy
                        vọng sẽ cùng bạn thắp lửa cho căn bếp gia đình và vun vén từng khoảnh khắc an vui bên những
                        người thân yêu.
                      </p>

                      <!-- Account Summary Card -->
                      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                        style="margin-top: 24px; background-color: #ffffff; border-radius: 14px; border: 1px solid #d9edf7; overflow: hidden;">
                        <tr>
                          <td style="padding: 12px 18px; font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; color: #607078; border-bottom: 1px solid #edf5f9;">
                            Tên đăng nhập:
                          </td>
                          <td align="right" style="padding: 12px 18px; font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; font-weight: 700; color: #25343b; border-bottom: 1px solid #edf5f9;">
                            {{ $user->username }}
                          </td>
                        </tr>
                        @if(!empty($user->email))
                        <tr>
                          <td style="padding: 12px 18px; font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; color: #607078; border-bottom: 1px solid #edf5f9;">
                            Email đăng ký:
                          </td>
                          <td align="right" style="padding: 12px 18px; font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; font-weight: 700; color: #25343b; border-bottom: 1px solid #edf5f9;">
                            {{ $user->email }}
                          </td>
                        </tr>
                        @endif
                        <tr>
                          <td style="padding: 12px 18px; font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; color: #607078;">
                            Trạng thái tài khoản:
                          </td>
                          <td align="right" style="padding: 12px 18px; font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; font-weight: 700; color: #0284c7;">
                            ✓ Đã kích hoạt sẵn sàng
                          </td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Benefits Section -->
            <tr>
              <td class="benefits-pad" style="padding: 52px 42px 48px;">
                <div align="center"
                  style="font-family: 'Inter', Arial, sans-serif; font-size: 10px; font-weight: 700; color: #159edc; letter-spacing: 0.18em; text-transform: uppercase;">
                  ĐẶC QUYỀN DÀNH CHO BẠN
                </div>
                <h2 align="center"
                  style="margin: 10px 0 28px; font-family: 'Inter', Arial, sans-serif; font-size: 24px; font-weight: 700; color: #25343b; line-height: 1.25; letter-spacing: -0.02em;">
                  Trọn vẹn an tâm &middot; Đẳng cấp trong từng trải nghiệm
                </h2>

                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                  style="border-top: 1px solid #e6eef1;">

                  <!-- Benefit 1 -->
                  <tr>
                    <td style="padding: 22px 4px; border-bottom: 1px solid #e6eef1;">
                      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <td width="58" valign="middle" style="width: 58px;">
                            <div
                              style="width: 58px; height: 58px; border-radius: 18px; background-color: #eaf8ff; text-align: center; line-height: 58px; display: inline-block;">
                              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="58"
                                height="58">
                                <tr>
                                  <td align="center" valign="middle">
                                    <img src="{{ $deliveryIconSrc }}" alt="Giao hàng" width="30" height="30" style="display: block; width: 30px; height: 30px;" />
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </td>
                          <td width="18" style="width: 18px;">&nbsp;</td>
                          <td valign="middle">
                            <div
                              style="font-family: 'Inter', Arial, sans-serif; font-size: 17px; font-weight: 700; color: #25343b; margin-bottom: 4px;">
                              Đặc Quyền Giao Hàng Chu Đáo
                            </div>
                            <div
                              style="font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; color: #607078; line-height: 1.55;">
                              Tận hưởng dịch vụ vận chuyển hoàn toàn miễn phí, cẩn trọng trao gửi đến tận cửa nhà bạn
                              trên mỗi đơn hàng.
                            </div>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <!-- Benefit 2 -->
                  <tr>
                    <td style="padding: 22px 4px; border-bottom: 1px solid #e6eef1;">
                      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <td width="58" valign="middle" style="width: 58px;">
                            <div
                              style="width: 58px; height: 58px; border-radius: 18px; background-color: #eaf8ff; text-align: center; line-height: 58px; display: inline-block;">
                              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="58"
                                height="58">
                                <tr>
                                  <td align="center" valign="middle">
                                    <img src="{{ $qualityIconSrc }}" alt="Chất lượng" width="30" height="30" style="display: block; width: 30px; height: 30px;" />
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </td>
                          <td width="18" style="width: 18px;">&nbsp;</td>
                          <td valign="middle">
                            <div
                              style="font-family: 'Inter', Arial, sans-serif; font-size: 17px; font-weight: 700; color: #25343b; margin-bottom: 4px;">
                              Chất Lượng Thượng Hạng
                            </div>
                            <div
                              style="font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; color: #607078; line-height: 1.55;">
                              Từng sản phẩm được tuyển chọn khắt khe, đạt chuẩn mực cao nhất về tính thẩm mỹ tinh tế và
                              độ bền vượt thời gian.
                            </div>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <!-- Benefit 3 -->
                  <tr>
                    <td style="padding: 22px 4px; border-bottom: 1px solid #e6eef1;">
                      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <td width="58" valign="middle" style="width: 58px;">
                            <div
                              style="width: 58px; height: 58px; border-radius: 18px; background-color: #eaf8ff; text-align: center; line-height: 58px; display: inline-block;">
                              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="58"
                                height="58">
                                <tr>
                                  <td align="center" valign="middle">
                                    <img src="{{ $supportIconSrc }}" alt="Hỗ trợ" width="30" height="30" style="display: block; width: 30px; height: 30px;" />
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </td>
                          <td width="18" style="width: 18px;">&nbsp;</td>
                          <td valign="middle">
                            <div
                              style="font-family: 'Inter', Arial, sans-serif; font-size: 17px; font-weight: 700; color: #25343b; margin-bottom: 4px;">
                              Chăm Sóc Chuyên Biệt 24/7
                            </div>
                            <div
                              style="font-family: 'Inter', Arial, sans-serif; font-size: 13.5px; color: #607078; line-height: 1.55;">
                              Đội ngũ chuyên gia không gian sống giàu kinh nghiệm luôn sẵn sàng đồng hành, tư vấn và hỗ
                              trợ bạn trong mọi khoảnh khắc.
                            </div>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                </table>
              </td>
            </tr>

            <!-- CTA Section -->
            <tr>
              <td style="padding: 0 42px 42px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                  style="background-color: #25343b; border-radius: 24px;">
                  <tr>
                    <td class="cta-pad" align="center" style="padding: 38px 24px;">
                      <h2
                        style="margin: 0; font-family: 'Inter', Arial, sans-serif; font-size: 25px; font-weight: 700; color: #ffffff; line-height: 1.25; letter-spacing: -0.02em;">
                        Bắt đầu trải nghiệm mua sắm tiện nghi ngay hôm nay!
                      </h2>
                      <p
                        style="margin: 10px 0 24px; font-family: 'Inter', Arial, sans-serif; font-size: 15px; color: #c9d4d9;">
                        Hàng trăm sản phẩm chính hãng cùng ngập tràn ưu đãi độc quyền dành riêng cho thành viên mới đang
                        chờ bạn.
                      </p>
                      <!-- Button -->
                      <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td align="center"
                            style="border-radius: 999px; background-color: #42c0fb; box-shadow: 0 8px 20px rgba(66, 192, 251, 0.22);">
                            <a href="{{ url('/') }}"
                              style="display: inline-block; padding: 15px 28px; font-family: 'Inter', Arial, sans-serif; font-size: 14px; font-weight: 700; color: #25343b; text-decoration: none; border-radius: 999px;">
                              Mua Sắm & Nhận Ưu Đãi &rarr;
                            </a>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

          </table>
        </td>
      </tr>
    </table>
  </center>
</body>

</html>
