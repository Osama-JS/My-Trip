@extends('frontend.customer.layouts.customer-layout')

@section('title', __('فتح تذكرة جديدة'))

@section('content')
<style>
    .support-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding-bottom: 50px;
    }
    .page-head {
        margin-bottom: 35px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .back-btn {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e293b;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        text-decoration: none;
        transition: 0.3s;
        border: 1px solid #f1f5f9;
    }
    .back-btn:hover { background: #0f172a; color: white; transform: translateX(-5px); }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 20px 40px rgba(0,0,0,0.04);
        padding: 40px;
    }
    
    .input-group-custom { margin-bottom: 25px; }
    .label-custom {
        display: block;
        font-weight: 700;
        font-size: 0.9rem;
        color: #334155;
        margin-bottom: 10px;
        padding-inline-start: 5px;
    }
    .input-custom {
        width: 100%;
        padding: 14px 20px;
        border-radius: 15px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        font-size: 0.95rem;
        transition: all 0.3s;
        color: #1e293b;
    }
    .input-custom:focus {
        border-color: #2563eb;
        background: white;
        outline: none;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
    }
    
    .select-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .file-drop-zone {
        border: 2px dashed #e2e8f0;
        border-radius: 20px;
        padding: 40px 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: 0.3s;
        position: relative;
    }
    .file-drop-zone:hover { border-color: #2563eb; background: #eff6ff; }
    .file-drop-zone i { font-size: 2.5rem; color: #2563eb; margin-bottom: 15px; opacity: 0.7; }
    .file-drop-zone h5 { font-weight: 700; color: #1e293b; margin-bottom: 5px; }
    .file-drop-zone p { font-size: 0.8rem; color: #64748b; margin: 0; }
    .file-drop-zone input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    
    .submit-bar {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 40px;
        padding-top: 25px;
        border-top: 1px solid #f1f5f9;
    }
    .btn-premium {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        padding: 15px 35px;
        border-radius: 15px;
        font-weight: 800;
        border: none;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        transition: 0.3s;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-premium:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3); }
    
    body.dark-mode .glass-card { background: rgba(30, 41, 59, 0.8); border-color: rgba(255,255,255,0.05); }
    body.dark-mode .back-btn { background: #1e293b; border-color: #334155; color: #f1f5f9; }
    body.dark-mode .label-custom { color: #f1f5f9; }
    body.dark-mode .input-custom { background: #0f172a; border-color: #334155; color: #f1f5f9; }
    body.dark-mode .file-drop-zone { background: #0f172a; border-color: #334155; }
    body.dark-mode .file-drop-zone h5 { color: #f1f5f9; }
</style>

<div class="support-wrapper">
    <div class="page-head">
        <a href="{{ route('customer.support.index') }}" class="back-btn">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h2 class="m-0 font-weight-black">{{ __('فتح تذكرة دعم جديدة') }}</h2>
            <p class="text-muted m-0 small">{{ __('فريقنا متواجد دائماً لمساعدتك في أسرع وقت') }}</p>
        </div>
    </div>

    <div class="glass-card">
        <form action="{{ route('customer.support.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="input-group-custom">
                <label class="label-custom">{{ __('عنوان التذكرة') }}</label>
                <input type="text" name="subject" class="input-custom" placeholder="{{ __('مثال: مشكلة في الدفع، استفسار عن رحلة...') }}" required>
            </div>

            <div class="select-grid">
                <div class="input-group-custom">
                    <label class="label-custom">{{ __('القسم المختص') }}</label>
                    <select name="category" class="input-custom" required>
                        <option value="technical">🛠️ {{ __('الدعم الفني') }}</option>
                        <option value="financial">💰 {{ __('الحسابات والمدفوعات') }}</option>
                        <option value="booking">✈️ {{ __('استفسارات الحجوزات') }}</option>
                        <option value="general">💬 {{ __('استفسارات عامة') }}</option>
                    </select>
                </div>
                
                <div class="input-group-custom">
                    <label class="label-custom">{{ __('أهمية التذكرة') }}</label>
                    <select name="priority" class="input-custom" required>
                        <option value="low">{{ __('منخفضة - غير مستعجل') }}</option>
                        <option value="medium" selected>{{ __('متوسطة - عادي') }}</option>
                        <option value="high">{{ __('عالية - هام') }}</option>
                        <option value="urgent">{{ __('قصوى - طارئ جداً') }}</option>
                    </select>
                </div>
            </div>

            <div class="input-group-custom">
                <label class="label-custom">{{ __('تفاصيل المشكلة أو الطلب') }}</label>
                <textarea name="message" class="input-custom" rows="6" placeholder="{{ __('يرجى كتابة التفاصيل هنا لمساعدتنا في فهم طلبك بشكل أفضل...') }}" required></textarea>
            </div>

            <div class="input-group-custom">
                <label class="label-custom">{{ __('المرفقات (اختياري)') }}</label>
                <div class="file-drop-zone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h5>{{ __('اسحب الملفات هنا أو اضغط للرفع') }}</h5>
                    <p>{{ __('يمكنك رفع صور أو مستندات (الحد الأقصى 5 ميجابايت)') }}</p>
                    <input type="file" name="attachments[]" multiple id="file_input">
                </div>
                <div id="file_status" class="mt-2 small text-primary font-weight-bold"></div>
            </div>

            <div class="submit-bar">
                <button type="submit" class="btn-premium">
                    <span>{{ __('إرسال التذكرة الآن') }}</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('file_input').addEventListener('change', function() {
        const status = document.getElementById('file_status');
        if(this.files.length > 0) {
            status.innerHTML = `<i class="fas fa-check-circle"></i> تم اختيار ${this.files.length} ملفات`;
        } else {
            status.innerHTML = '';
        }
    });
</script>
@endsection


