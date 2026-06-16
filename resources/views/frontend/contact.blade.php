@extends('frontend.layouts.app')

@section('title', __('Contact Us'))
@section('meta_description', __('Get in touch with our team for any inquiries, support, or feedback.'))

@section('content')
    {{-- Premium Hero --}}
    <div class="fe-about-hero" style="position: relative; padding: 100px 0; background: linear-gradient(135deg, var(--primary) 0%, #1e3a8a 100%); color: white; text-align: center; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('https://images.unsplash.com/photo-1534536281715-e28d76689b4d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; opacity: 0.2;"></div>
        <div class="fe-container" style="position: relative; z-index: 1;">
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 20px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);" data-aos="fade-up">{{ __('Contact Us') }}</h1>
            <div class="fe-breadcrumb fe-breadcrumb-center fe-animate" data-aos="fade-up" data-aos-delay="100" style="justify-content: center; background: rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 50px; display: inline-flex; backdrop-filter: blur(5px); margin-top: 10px;">
                <a href="{{ route('home') }}" style="color: white; opacity: 0.8;">{{ __('Home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.8;"><path d="m9 18 6-6-6-6"/></svg>
                <span class="current" style="color: white; font-weight: 600;">{{ __('Contact Us') }}</span>
            </div>
        </div>
    </div>

    @php
        $phone = \App\Models\Setting::get('contact_phone', config('app.name'));
        $email = \App\Models\Setting::get('contact_email', config('app.name'));
        $address = \App\Models\Setting::get('contact_address_' . app()->getLocale(), config('app.name'));
    @endphp

    <div class="fe-section" style="background: var(--gray-50); padding-top: var(--space-12); padding-bottom: var(--space-12);">
        <div class="fe-container">
            @if(session('success'))
                <div class="fe-alert fe-alert-success" style="margin-bottom: var(--space-8); background: #d1fae5; color: #065f46; padding: 15px 20px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 10px;" data-aos="fade-up">
                    <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="fe-search-layout" style="grid-template-columns: 1fr 1.2fr; gap: var(--space-12); align-items: start;">
                
                {{-- Contact Info & Map --}}
                <div class="fe-contact-info-wrapper" data-aos="fade-right">
                    <h2 style="font-size: 2rem; font-weight: 700; color: var(--dark); margin-bottom: var(--space-3);">{{ __('Get In Touch') }}</h2>
                    <p style="color: var(--gray-600); margin-bottom: var(--space-8); line-height: 1.6;">{{ __('Have questions or feedback? We\'re here to help. Reach out to us through any of the channels below or fill the form.') }}</p>

                    <div class="fe-contact-details" style="display: grid; gap: var(--space-6); margin-bottom: var(--space-8);">
                        <div class="fe-contact-item fe-animate fe-hover-float" style="display: flex; gap: var(--space-4); align-items: flex-start; background: white; padding: var(--space-5); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--gray-100);">
                            <div class="fe-icon-wrapper" style="width: 48px; height: 48px; background: rgba(var(--primary-rgb), 0.1); color: var(--primary); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 5px; color: var(--dark); font-weight: 600;">{{ __('Our Address') }}</h4>
                                <p style="color: var(--gray-600); line-height: 1.6; font-size: 0.95rem;">{{ $address ?: 'Dubai, United Arab Emirates' }}</p>
                            </div>
                        </div>

                        <div class="fe-contact-item fe-animate fe-hover-float" style="display: flex; gap: var(--space-4); align-items: flex-start; background: white; padding: var(--space-5); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--gray-100);">
                            <div class="fe-icon-wrapper" style="width: 48px; height: 48px; background: rgba(var(--primary-rgb), 0.1); color: var(--primary); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 5px; color: var(--dark); font-weight: 600;">{{ __('Email Address') }}</h4>
                                <p style="color: var(--gray-600); font-size: 0.95rem;"><a href="mailto:{{ $email }}" style="color: inherit; text-decoration: none;">{{ $email ?: 'support@mytrip.com' }}</a></p>
                            </div>
                        </div>

                        <div class="fe-contact-item fe-animate fe-hover-float" style="display: flex; gap: var(--space-4); align-items: flex-start; background: white; padding: var(--space-5); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--gray-100);">
                            <div class="fe-icon-wrapper" style="width: 48px; height: 48px; background: rgba(var(--primary-rgb), 0.1); color: var(--primary); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 5px; color: var(--dark); font-weight: 600;">{{ __('Phone Number') }}</h4>
                                <p style="color: var(--gray-600); font-size: 0.95rem;"><a href="tel:{{ $phone }}" style="color: inherit; text-decoration: none;" dir="ltr">{{ $phone ?: '+971 50 123 4567' }}</a></p>
                            </div>
                        </div>
                    </div>

                    {{-- Map Embed (Example Google Maps) --}}
                    <div class="fe-map-wrapper fe-hover-zoom" style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--gray-200); height: 280px;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d115408.06429215014!2d55.20782354714083!3d25.076326164070007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43496ad9c645%3A0xbde66e5084295162!2sDubai%20-%20United%20Arab%20Emirates!5e0!3m2!1sen!2s!4v1714488358485!5m2!1sen!2s" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="fe-contact-form-wrapper" style="background: white; border-radius: var(--radius-2xl); padding: var(--space-10); box-shadow: var(--shadow-xl); border: 1px solid var(--gray-100);" data-aos="fade-left">
                    <h3 style="margin-bottom: var(--space-2); font-size: 1.8rem; font-weight: 700; color: var(--dark);">{{ __('Send us a Message') }}</h3>
                    <p style="color: var(--gray-500); margin-bottom: var(--space-8);">{{ __('Fill out the form below and we will get back to you as soon as possible.') }}</p>
                    
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="fe-form-group" style="margin-bottom: var(--space-5);">
                            <label class="fe-form-label" style="font-weight: 600; color: var(--dark); margin-bottom: 8px; display: block;">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="fe-form-input" required placeholder="{{ __('Enter your full name') }}" value="{{ old('name') }}" style="border-radius: var(--radius-lg); padding: 12px 16px; border: 1px solid var(--gray-200); width: 100%;">
                            @error('name') <span style="color: red; font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="fe-form-group" style="margin-bottom: var(--space-5);">
                            <label class="fe-form-label" style="font-weight: 600; color: var(--dark); margin-bottom: 8px; display: block;">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="fe-form-input" required placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}" dir="ltr" style="border-radius: var(--radius-lg); padding: 12px 16px; border: 1px solid var(--gray-200); width: 100%;">
                            @error('email') <span style="color: red; font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="fe-form-group" style="margin-bottom: var(--space-5);">
                            <label class="fe-form-label" style="font-weight: 600; color: var(--dark); margin-bottom: 8px; display: block;">{{ __('Subject') }} <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="fe-form-input" required placeholder="{{ __('How can we help you?') }}" value="{{ old('subject') }}" style="border-radius: var(--radius-lg); padding: 12px 16px; border: 1px solid var(--gray-200); width: 100%;">
                            @error('subject') <span style="color: red; font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="fe-form-group" style="margin-bottom: var(--space-6);">
                            <label class="fe-form-label" style="font-weight: 600; color: var(--dark); margin-bottom: 8px; display: block;">{{ __('Message') }} <span class="text-danger">*</span></label>
                            <textarea name="message" class="fe-form-input" required rows="5" placeholder="{{ __('Type your message here...') }}" style="border-radius: var(--radius-lg); padding: 12px 16px; border: 1px solid var(--gray-200); width: 100%; resize: vertical;">{{ old('message') }}</textarea>
                            @error('message') <span style="color: red; font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg" style="width: 100%; border-radius: var(--radius-lg); padding: 14px; font-weight: 600; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.3s ease;">
                            {{ __('Send Message') }} <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
