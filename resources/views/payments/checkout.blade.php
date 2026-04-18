<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إتمام عملية الدفع - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            font-family: 'Cairo', sans-serif !important;
            border-radius: 24px !important;
            background: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
        }
        .swal2-title, .swal2-html-container { color: #f8fafc !important; }
        .swal2-confirm { background: var(--primary) !important; border-radius: 12px !important; padding: 12px 30px !important; }
    </style>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --bg: #0f172a;
            --card: rgba(30, 41, 59, 0.7);
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255,255,255,0.1);
            --success: #10b981;
            --danger: #ef4444;
            --accent: #f59e0b;
        }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color: transparent; }
        body {
            font-family: 'Cairo', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .checkout-container { max-width: 500px; margin: 20px auto; padding: 20px; animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Header */
        .page-header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 2.2rem; font-weight: 800; letter-spacing: 2px; background: linear-gradient(to right, #fff, var(--primary-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 4px; text-transform: uppercase; }
        .page-header p { color: var(--text-muted); font-size: 0.95rem; }

        .step-indicator { display: flex; justify-content: center; gap: 10px; margin-top: 15px; }
        .step { width: 35px; height: 5px; border-radius: 10px; background: var(--border); transition: all 0.3s; }
        .step.active { background: var(--primary); box-shadow: 0 0 10px var(--primary); }

        /* Modern Glass Cards */
        .card {
            background: var(--card);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        }

        .summary-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); }
        .summary-title { font-weight: 700; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
        .trip-price { font-size: 1.4rem; font-weight: 800; color: #fff; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem; }
        .info-label { color: var(--text-muted); }
        .info-value { font-weight: 600; color: #e2e8f0; text-align: left; }

        /* Payment Section */
        .payment-section-title { font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 1.1rem; }

        .method-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
        }
        .gateway-logo {
            height: 45px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            transition: transform 0.3s;
        }
        .gateway-logo:hover { transform: scale(1.05); }

        /* HyperPay Widget Customization */
        .wpwl-container { direction: ltr !important; }
        .wpwl-form { background: transparent !important; border: none !important; padding: 0 !important; width: 100% !important; }
        .wpwl-label {
            color: var(--text-muted) !important;
            font-family: 'Cairo' !important;
            font-size: 0.8rem !important;
            margin-bottom: 8px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        .wpwl-control {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid var(--border) !important;
            color: white !important;
            border-radius: 16px !important;
            padding: 14px 16px !important;
            height: 54px !important;
            transition: all 0.3s !important;
        }
        .wpwl-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2) !important;
            outline: none !important;
        }
        .wpwl-group { margin-bottom: 20px !important; }

        .wpwl-button-pay {
            background: linear-gradient(135deg, var(--primary), var(--primary-light)) !important;
            border: none !important;
            border-radius: 18px !important;
            padding: 18px !important;
            font-family: 'Cairo' !important;
            font-weight: 700 !important;
            font-size: 1.1rem !important;
            cursor: pointer !important;
            width: 100% !important;
            margin-top: 15px !important;
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.4) !important;
            transition: all 0.3s !important;
            color: white !important;
        }
        .wpwl-button-pay:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.5) !important;
        }

        /* Redirect Button */
        .btn-redirect {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 18px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.4);
            transition: all 0.3s;
        }
        .btn-redirect:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.5);
        }

        /* Bank Transfer Styling */
        .bank-details { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 16px; margin-bottom: 20px; border: 1px dashed var(--border); }
        .upload-section { margin-top: 20px; }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .btn-upload {
            background: var(--card);
            border: 2px dashed var(--border);
            padding: 20px;
            border-radius: 16px;
            width: 100%;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-upload:hover { border-color: var(--primary); background: rgba(79, 70, 229, 0.1); }

        .secure-badge {
            text-align: center;
            margin-top: 30px;
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .security-icons { display: flex; gap: 15px; opacity: 0.6; grayscale: 1; }
        .security-icons img { height: 20px; }

        .loader-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--bg); display: none; flex-direction: column; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(10px); }
        .spinner { border: 4px solid rgba(255,255,255,0.1); border-top: 4px solid var(--primary); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin-bottom: 15px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="page-header">
        <div class="logo">{{ config('app.name') }}</div>
        <p>بوابة الدفع الآمنة</p>
        <div class="step-indicator">
            <div class="step active"></div>
            <div class="step active"></div>
            <div class="step active"></div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card">
        <div class="summary-header">
            <span class="summary-title"><i class="fas fa-file-invoice"></i> تفاصيل الحجز</span>
            <span class="trip-price">{{ number_format($amount, 2) }} ر.س</span>
        </div>
        <div class="info-row">
            <span class="info-label">الوصف</span>
            <span class="info-value">{{ $title }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">نوع الحجز</span>
            <span class="info-value">
                @if($booking_type === 'trip') رحلة
                @elseif($booking_type === 'hotel') فندق
                @elseif($booking_type === 'flight') طيران
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">اسم العميل</span>
            <span class="info-value">{{ $user->full_name ?? ($user->first_name . ' ' . $user->last_name) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">رقم المرجع المحلي</span>
            <span class="info-value">#{{ $booking->id }}</span>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="card">
        @if($method === 'bank_transfer')
            <div class="payment-section-title">
                <i class="fas fa-university"></i> اختر الحساب البنكي للتحويل
            </div>
            
            <style>
                .bank-cards { display: grid; gap: 15px; margin-bottom: 25px; }
                .bank-card { 
                    background: rgba(255,255,255,0.05); 
                    border: 1px solid var(--border); 
                    border-radius: 20px; 
                    padding: 15px; 
                    cursor: pointer; 
                    transition: all 0.3s ease;
                    position: relative;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }
                .bank-card:hover { border-color: var(--primary-light); background: rgba(255,255,255,0.08); }
                .bank-card.selected { 
                    border-color: var(--primary); 
                    background: rgba(79, 70, 229, 0.15); 
                    box-shadow: 0 0 15px rgba(79, 70, 229, 0.3);
                }
                .bank-card.selected::after {
                    content: '\f058';
                    font-family: 'Font Awesome 6 Free';
                    font-weight: 900;
                    color: var(--primary);
                    position: absolute;
                    top: 10px;
                    left: 10px;
                }
                .bank-logo-img { width: 50px; height: 50px; border-radius: 12px; object-fit: contain; background: white; padding: 5px; }
                .bank-info { flex: 1; }
                .bank-name-label { font-weight: 700; display: block; margin-bottom: 4px; font-size: 0.95rem; }
                .bank-iban-label { font-family: monospace; font-size: 0.85rem; color: var(--text-muted); }
                .copy-btn {
                    padding: 8px 12px;
                    border-radius: 10px;
                    background: rgba(255,255,255,0.1);
                    border: none;
                    color: white;
                    font-size: 0.8rem;
                    cursor: pointer;
                    transition: all 0.2s;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                .copy-btn:hover { background: var(--primary); }
                .copy-btn.copied { background: var(--success); }
            </style>

            <div class="bank-cards">
                @foreach($bankAccounts as $account)
                    <div class="bank-card @if($loop->first) selected @endif" data-id="{{ $account->id }}" onclick="selectBank(this)">
                        <img src="{{ $account->logo_path ? asset('storage/'.$account->logo_path) : 'https://cdn-icons-png.flaticon.com/512/2830/2830284.png' }}" class="bank-logo-img">
                        <div class="bank-info">
                            <span class="bank-name-label">{{ $account->bank_name }}</span>
                            <span class="bank-iban-label">{{ $account->iban }}</span>
                            <div class="mt-2 d-flex gap-2 align-items-center">
                                <span class="text-muted" style="font-size: 0.75rem;">{{ $account->beneficiary_name }}</span>
                                <button type="button" class="copy-btn" onclick="copyIBAN('{{ $account->iban }}', this); event.stopPropagation();">
                                    <i class="far fa-copy"></i> نسخ الآيبان
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="upload-section">
                <form id="bank-transfer-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <input type="hidden" name="type" value="{{ $booking_type }}">
                    <input type="hidden" name="bank_account_id" id="selected_bank_id" value="{{ $bankAccounts->first()->id ?? '' }}">
                    
                    <div class="mb-3">
                        <label class="info-label mb-2 d-block">اسم المحول</label>
                        <input type="text" name="sender_name" class="wpwl-control" style="width:100%" required placeholder="أدخل اسم المحول">
                    </div>

                    <div class="mb-3">
                        <label class="info-label mb-2 d-block">رقم الإيصال (اختياري)</label>
                        <input type="text" name="receipt_number" class="wpwl-control" style="width:100%" placeholder="رقم العملية البنكية">
                    </div>

                    <div class="mb-3">
                        <label class="info-label mb-2 d-block">إيصال التحويل</label>
                        <div class="btn-upload" onclick="document.getElementById('receipt_image').click()">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-primary"></i>
                            <p id="file-name">اضغط هنا لرفع الوثيقة (JPG, PNG, PDF)</p>
                            <input type="file" id="receipt_image" name="receipt_image" style="display:none" accept="image/*,.pdf" required onchange="updateFileName(this)">
                        </div>
                    </div>

                    <button type="submit" class="btn-redirect mt-4">
                        إرسال الإيصال للمراجعة
                    </button>
                </form>
            </div>
        @else
            <div class="method-display">
                @php
                    $logoUrl = match($method) {
                        'mada' => 'https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg',
                        'visa_master' => 'https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg',
                        'apple_pay' => 'https://upload.wikimedia.org/wikipedia/commons/b/b0/Apple_Pay_logo.svg',
                        'tamara' => 'https://cdn.tamara.co/assets/svg/tamara-logo-badge-ar.svg',
                        'tabby' => 'https://checkout.tabby.ai/images/logo.png',
                        default => null
                    };
                @endphp

                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $method }}" class="gateway-logo">
                @endif
                <div class="payment-section-title">
                    <span>إكمال الدفع عبر {{ strtoupper($method) }}</span>
                </div>
            </div>

            @if(in_array($method, ['mada', 'visa_master', 'apple_pay']))
                @if(isset($sim_mode) && $sim_mode)
                    {{-- LOCAL SIMULATION MODE --}}
                    <div style="background: rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.4); border-radius:16px; padding:20px; text-align:center;">
                        <div style="font-size:2rem; margin-bottom:10px;">🧪</div>
                        <p style="font-weight:700; color:#f59e0b; margin-bottom:5px;">وضع المحاكاة — بيئة التطوير</p>
                        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:20px;">
                            لا يمكن الاتصال بـ HyperPay من البيئة المحلية.<br>
                            المرجع التجريبي: <code style="color:#f59e0b;">{{ $sim_ref ?? 'SIM-LOCAL' }}</code>
                        </p>
                        <a href="{{ route('payments.web.success', ['booking_id' => $booking->id, 'transaction_id' => $sim_ref ?? 'SIM', 'source' => 'simulation', 'type' => $booking_type]) }}"
                           class="btn-redirect" style="background:linear-gradient(135deg,#10b981,#059669);">
                            ✅ محاكاة نجاح الدفع (للاختبار فقط)
                        </a>
                        <p style="font-size:0.75rem; color:var(--text-muted); margin-top:15px;">
                            ⚠️ هذا الزر لن يظهر في بيئة الإنتاج
                        </p>
                    </div>
                @elseif(isset($checkout_id))
                    <script src="https://{{ config('hyperpay.test_mode', true) ? 'eu-test.oppwa.com' : 'oppwa.com' }}/v1/paymentWidgets.js?checkoutId={{ $checkout_id }}"></script>
                    <form action="{{ route('payments.web.callback', ['payment_type' => $method, 'source' => $source]) }}" class="paymentWidgets" data-brands="{{ $method === 'mada' ? 'MADA' : ($method === 'apple_pay' ? 'APPLEPAY' : 'VISA MASTER') }}"></form>
                @else
                    <div style="text-align:center; padding:20px;">
                        <p style="color:var(--danger)">فشل تحميل طلب الدفع. يرجى المحاولة لاحقاً.</p>
                    </div>
                @endif
            @else
                <!-- Tamara / Tabby Logic -->
                <p style="font-size: 0.9rem; color: var(--text-muted); text-align: center; margin-bottom: 24px;">
                    سيتم توجيهك الآن إلى صفحة الدفع الرسمية لإكمال العملية بأمان.
                </p>
                <button id="btn-redirect" class="btn-redirect">
                    متابعة الدفع الآن 💳
                </button>
            @endif
        @endif
    </div>

    <div class="secure-badge">
        <p>🔒 جميع المدفوعات مشفرة وآمنة تماماً</p>
        <div class="security-icons">
            <img src="https://checkout.hyperpay.com/v1/paymentWidgets/img/pci-dss.png" alt="PCI DSS">
            <img src="https://checkout.hyperpay.com/v1/paymentWidgets/img/3d-secure.png" alt="3D Secure">
        </div>
    </div>
</div>

<div class="loader-overlay" id="loader">
    <div class="spinner"></div>
    <p>جاري تأمين الاتصال ومعالجة طلبك...</p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@if(in_array($method, ['mada', 'visa_master', 'apple_pay']) && isset($checkout_id))
    <script type="text/javascript">
        var wpwlOptions = {
            paymentTarget: "_top",
            locale: "ar",
            style: "plain",
            labels: {
                cvv: "رمز الأمان (CVV)",
                cardHolder: "اسم صاحب البطاقة",
                cardNumber: "رقم البطاقة",
                expiryDate: "تاريخ الانتهاء"
            }
        };
    </script>
@endif

<script>
    function updateFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : "اضغط هنا لرفع الوثيقة";
        document.getElementById('file-name').textContent = fileName;
    }

    function selectBank(element) {
        $('.bank-card').removeClass('selected');
        $(element).addClass('selected');
        $('#selected_bank_id').val($(element).data('id'));
    }

    function copyIBAN(iban, btn) {
        navigator.clipboard.writeText(iban).then(() => {
            const originalText = $(btn).html();
            $(btn).addClass('copied').html('<i class="fas fa-check"></i> تم النسخ');
            setTimeout(() => {
                $(btn).removeClass('copied').html(originalText);
            }, 2000);
        });
    }

    $(document).ready(function() {
        // Handle Redirect Payments (Tabby/Tamara)
        $('#btn-redirect').on('click', function() {
            const $btn = $(this);
            $('#loader').css('display', 'flex');
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('payments.web.initiate') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    booking_id: "{{ $booking->id }}",
                    method: "{{ $method }}",
                    type: "{{ $booking_type }}",
                    source: "{{ $source }}"
                },
                success: function(response) {
                    if (response.checkout_url || response.redirect_url) {
                        window.location.href = response.checkout_url || response.redirect_url;
                    } else if (response.payment_id || response.order_id) {
                         $('#loader').hide();
                         Swal.fire({
                            icon: 'info',
                            title: 'تم إصدار رقم الدفع',
                            text: (response.payment_id || response.order_id),
                         });
                    } else {
                        $('#loader').hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'خطأ في استلام رابط الدفع',
                        });
                        $btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    $('#loader').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'فشل التشغيل',
                        text: (xhr.responseJSON?.message || 'خطأ غير معروف في الاتصال بالخادم'),
                    });
                    $btn.prop('disabled', false);
                }
            });
        });

        // Handle Bank Transfer Form
        $('#bank-transfer-form').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const $btn = $(this).find('button');
            
            $('#loader').css('display', 'flex');
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('payments.web.bank_transfer') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#loader').hide();
                    if (!response.error) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الإرسال بنجاح',
                            text: response.message,
                            confirmButtonText: 'حسناً',
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.href = "{{ route('payments.web.success') }}?booking_id={{ $booking->id }}&type={{ $booking_type }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: response.message
                        });
                        $btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    $('#loader').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في النظام',
                        text: (xhr.responseJSON?.message || 'حدث خطأ غير متوقع أثناء إرسال البيانات')
                    });
                    $btn.prop('disabled', false);
                }
            });
        });
    });
</script>

</body>
</html>
