@php
    if ($settings->redirect_url != null or !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')

@section('title', 'Contact Us')

@section('content')

<section class="relative overflow-hidden bg-ink pt-20 pb-16">
    <div class="pointer-events-none absolute inset-0 -z-0">
        <div class="absolute inset-0 grid-bg"></div>
        <div class="absolute -top-32 left-1/2 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-brand-2/20 blur-[130px]"></div>
    </div>

    <div class="container relative z-10 mx-auto max-w-6xl px-6">
        <div class="text-center mb-12">
            <span class="text-brand font-semibold tracking-widest text-xs uppercase">Contact</span>
            <h1 class="mt-3 font-display text-4xl md:text-5xl font-bold text-white">Get in touch</h1>
            <p class="mt-4 max-w-xl mx-auto text-[#9aa3b2]">
                Questions about your account or our services? Send us a message and our support team will get back to you.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-line bg-card p-6 md:p-8">

                    @if (session('success'))
                    <div class="mb-6 rounded-lg border border-accent/30 bg-accent/10 px-4 py-3 text-sm text-accent">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('enquiry') }}" method="post" class="space-y-5">
                        @csrf
                        <div>
                            <label for="username" class="block text-sm font-medium text-[#c7cedb] mb-2">Name</label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required
                                   class="w-full px-4 py-3 rounded-lg bg-panel border border-line text-white placeholder-[#6b7280] focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-colors"
                                   placeholder="Your name">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-[#c7cedb] mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 rounded-lg bg-panel border border-line text-white placeholder-[#6b7280] focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-colors"
                                   placeholder="you@example.com">
                        </div>
                        <div>
                            <label for="text" class="block text-sm font-medium text-[#c7cedb] mb-2">Message</label>
                            <textarea id="text" name="text" rows="6" required
                                      class="w-full px-4 py-3 rounded-lg bg-panel border border-line text-white placeholder-[#6b7280] focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-colors resize-none"
                                      placeholder="How can we help you?">{{ old('text') }}</textarea>
                        </div>
                        <button type="submit" class="brand-btn inline-flex items-center gap-2 rounded-xl px-7 py-3.5 text-sm font-semibold text-white">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            {{-- Contact info --}}
            <div>
                <div class="rounded-2xl border border-line bg-panel-2 p-6 md:p-8 h-full">
                    <h3 class="font-display text-lg font-bold text-white mb-6">Contact Information</h3>
                    <div class="space-y-5 text-sm">
                        @if(!empty($settings->contact_email))
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-brand/10 text-brand"><i class="fas fa-envelope"></i></span>
                            <div>
                                <p class="text-[#9aa3b2] text-xs">Email</p>
                                <a href="mailto:{{ $settings->contact_email }}" class="text-white break-all hover:text-brand">{{ $settings->contact_email }}</a>
                            </div>
                        </div>
                        @endif
                        @if(!empty($settings->whatsapp))
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-brand/10 text-brand"><i class="fas fa-phone"></i></span>
                            <div>
                                <p class="text-[#9aa3b2] text-xs">Phone / WhatsApp</p>
                                <p class="text-white">{{ $settings->whatsapp }}</p>
                            </div>
                        </div>
                        @endif
                        @if(!empty($settings->address_o))
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-brand/10 text-brand"><i class="fas fa-map-marker-alt"></i></span>
                            <div>
                                <p class="text-[#9aa3b2] text-xs">Address</p>
                                <p class="text-white">{{ $settings->address_o }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-line">
                        <p class="text-xs text-[#9aa3b2]">We typically respond within 24 hours on business days.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
