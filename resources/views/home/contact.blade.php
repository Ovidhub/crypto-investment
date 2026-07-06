@php
    if ($settings->redirect_url != null or !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')

@section('title', 'Contact us')

@inject('content', 'App\Http\Controllers\FrontController')
@section('content')

    <main class="flex-grow">
        <div class="animate-fade-in">

    <section class="relative min-h-screen flex items-center justify-center overflow-hidden" style="background-image: url('{{asset('images/landing-page-cover-shadow.svg')}}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="absolute inset-0" style="background: linear-gradient(226deg, rgb(1, 5, 34) 50.68%, rgb(234, 88, 12) 84.75%);"></div>
        
        <div class="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white space-y-8">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight" style="font-family: 'Poppins', sans-serif;">
                        Get In <span style="color: #F97316;">Touch</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-200 leading-relaxed" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                        Have questions about our platform? Need assistance with your account? Our dedicated support team is here to help you 24/7. Reach out to us through any of our channels and experience professional service that puts you first.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#contact-form" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white rounded-lg transition-all duration-300 hover:scale-105" style="background-color: #F97316; font-family: 'Poppins', sans-serif;">
                            Send Message
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#contact-info" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white border-2 rounded-lg transition-all duration-300 hover:bg-white/10" style="border-color: #F97316; font-family: 'Poppins', sans-serif;">
                            Contact Info
                        </a>
                    </div>
                </div>
                
                <div class="relative hidden lg:block">
                    <img src="{{asset('temp/assets/images/man-shades.webp')}}" alt="Contact Support" class="w-full h-auto relative z-20">
                    <img src="{{asset('temp/assets/images/not.webp')}}" alt="Support Notification" class="absolute top-10 right-0 w-64 animate-float">
                    <img src="{{asset('temp/assets/images/smaller-not.webp')}}" alt="Message Alert" class="absolute bottom-10 left-0 w-48 animate-float-delayed">
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="font-family: 'Poppins', sans-serif; color: #010522;">
                    Why <span style="color: #F97316;">Contact Us</span>
                </h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                    Our support team is committed to providing exceptional service across all communication channels
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-6">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-full" style="background-color: #FFF7ED;">
                        <i class="fas fa-clock text-2xl" style="color: #F97316;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3" style="font-family: 'Poppins', sans-serif; color: #010522;">24/7 Support</h3>
                    <p class="text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                        Round-the-clock assistance for all your inquiries and concerns
                    </p>
                </div>

                <div class="text-center p-6">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-full" style="background-color: #FFF7ED;">
                        <i class="fas fa-bolt text-2xl" style="color: #F97316;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3" style="font-family: 'Poppins', sans-serif; color: #010522;">Fast Response</h3>
                    <p class="text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                        Average response time of under 1 hour for urgent matters
                    </p>
                </div>

                <div class="text-center p-6">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-full" style="background-color: #FFF7ED;">
                        <i class="fas fa-users text-2xl" style="color: #F97316;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3" style="font-family: 'Poppins', sans-serif; color: #010522;">Expert Team</h3>
                    <p class="text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                        Knowledgeable professionals ready to solve your challenges
                    </p>
                </div>

                <div class="text-center p-6">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-full" style="background-color: #FFF7ED;">
                        <i class="fas fa-shield-alt text-2xl" style="color: #F97316;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3" style="font-family: 'Poppins', sans-serif; color: #010522;">Secure Communication</h3>
                    <p class="text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                        Your information is protected with end-to-end encryption
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact-form" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4" style="font-family: 'Poppins', sans-serif; color: #010522;">
                        Send Us a <span style="color: #F97316;">Message</span>
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                        Fill out the form below and we'll get back to you as soon as possible
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <div class="lg:col-span-2">
                        <form id="formPos" action="{{route('enquiry')}}" method="post" class="bg-white rounded-lg shadow-lg p-8">
                            @csrf

                            @if (Session::has('message'))
                                <div class="mb-6 px-4 py-3 rounded" style="background-color: #FFF7ED; border: 1px solid #F97316; color: #9A3412;">
                                    {{ Session::get('message') }}
                                </div>
                            @endif

                            <div class="space-y-6">
                                <div>
                                    <label for="loginq" class="block text-sm font-semibold mb-2" style="font-family: 'Poppins', sans-serif; color: #010522;">
                                        Name
                                    </label>
                                    <input
                                        type="text"
                                        id="loginq"
                                        name="username"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 transition-all duration-300"
                                        style="font-family: 'Poppins', sans-serif; --tw-ring-color: #F97316;"
                                        placeholder="Your name"
                                    >
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-semibold mb-2" style="font-family: 'Poppins', sans-serif; color: #010522;">
                                        Email Address *
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 transition-all duration-300"
                                        style="font-family: 'Poppins', sans-serif; --tw-ring-color: #F97316;"
                                        placeholder="your.email@example.com"
                                    >
                                </div>

                                <div>
                                    <label for="text" class="block text-sm font-semibold mb-2" style="font-family: 'Poppins', sans-serif; color: #010522;">
                                        Message *
                                    </label>
                                    <textarea
                                        id="text"
                                        name="text"
                                        required
                                        rows="6"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 transition-all duration-300 resize-none"
                                        style="font-family: 'Poppins', sans-serif; --tw-ring-color: #F97316;"
                                        placeholder="How can we help you?"
                                    ></textarea>
                                </div>

                                <div>
                                    <button
                                        type="button"
                                        id="button"
                                        class="w-full text-white px-6 py-4 rounded-lg font-semibold transition-all duration-300 hover:scale-[1.02]"
                                        style="background-color: #F97316; font-family: 'Poppins', sans-serif;"
                                    >
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="contact-info" class="lg:col-span-1">
                        <div class="text-white rounded-lg shadow-lg p-8 h-full" style="background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);">
                            <h3 class="text-xl font-bold mb-6" style="font-family: 'Poppins', sans-serif;">Contact Information</h3>

                            <div class="space-y-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full" style="background-color: rgba(255, 255, 255, 0.2);">
                                            <i class="fas fa-phone text-white"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-semibold text-white/80" style="font-family: 'Poppins', sans-serif;">Phone</p>
                                        <p class="mt-1 font-medium" style="font-family: 'Poppins', sans-serif;">{{$settings->whatsapp}}</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full" style="background-color: rgba(255, 255, 255, 0.2);">
                                            <i class="fas fa-envelope text-white"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-semibold text-white/80" style="font-family: 'Poppins', sans-serif;">Email</p>
                                        <p class="mt-1 font-medium break-all" style="font-family: 'Poppins', sans-serif;">{{$settings->contact_email}}</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full" style="background-color: rgba(255, 255, 255, 0.2);">
                                            <i class="fas fa-map-marker-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-semibold text-white/80" style="font-family: 'Poppins', sans-serif;">Address</p>
                                        <p class="mt-1 font-medium" style="font-family: 'Poppins', sans-serif;">{{$settings->address_o}}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-10 pt-8 border-t border-white/20">
                                <h4 class="text-lg font-bold mb-4" style="font-family: 'Poppins', sans-serif;">Follow Us</h4>
                                <div class="flex space-x-4">
                                    <a href="#" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 transition-all duration-300">
                                        <i class="fab fa-facebook-f text-white"></i>
                                    </a>
                                    <a href="#" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 transition-all duration-300">
                                        <i class="fab fa-twitter text-white"></i>
                                    </a>
                                    <a href="#" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 transition-all duration-300">
                                        <i class="fab fa-linkedin-in text-white"></i>
                                    </a>
                                    <a href="#" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 transition-all duration-300">
                                        <i class="fab fa-instagram text-white"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 relative overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);"></div>
        
        <div class="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white space-y-6">
                    <h2 class="text-3xl md:text-4xl font-bold leading-tight" style="font-family: 'Poppins', sans-serif;">
                        Ready to Start Your Investment Journey?
                    </h2>
                    <p class="text-lg text-white/90" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                        Join thousands of satisfied investors who trust our platform for secure and profitable trading. Get started today with our comprehensive suite of investment tools and expert support.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{route('register')}}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-lg transition-all duration-300 hover:scale-105" style="background-color: white; color: #F97316; font-family: 'Poppins', sans-serif;">
                            Get Started Now
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="{{route('about')}}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white border-2 border-white rounded-lg transition-all duration-300 hover:bg-white/10" style="font-family: 'Poppins', sans-serif;">
                            Learn More
                        </a>
                    </div>
                </div>
                
                
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4" style="font-family: 'Poppins', sans-serif; color: #010522;">
                        Frequently Asked <span style="color: #F97316;">Questions</span>
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto" style="font-family: 'Poppins', sans-serif; font-weight: 300;">
                        Find answers to common questions about our services and support
                    </p>
                </div>

                <div class="space-y-4" x-data="{selected:null}">
                    <div class="border-2 rounded-lg overflow-hidden transition-all duration-300" style="border-color: #FFF7ED;">
                        <button
                            @click="selected !== 1 ? selected = 1 : selected = null"
                            class="flex justify-between items-center w-full px-6 py-5 text-left font-semibold hover:bg-gray-50 focus:outline-none transition-all duration-300"
                            style="font-family: 'Poppins', sans-serif; color: #010522;"
                        >
                            <span>How quickly will I receive a response to my inquiry?</span>
                            <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': selected === 1}" style="color: #F97316;"></i>
                        </button>
                        <div
                            x-show="selected === 1"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-4"
                            class="px-6 py-5 text-gray-600"
                            style="background-color: #FFF7ED; font-family: 'Poppins', sans-serif; font-weight: 300;"
                        >
                            We aim to respond to all inquiries within 24 hours. For urgent matters, we recommend contacting us directly by phone for immediate assistance from our support team.
                        </div>
                    </div>

                    <div class="border-2 rounded-lg overflow-hidden transition-all duration-300" style="border-color: #FFF7ED;">
                        <button
                            @click="selected !== 2 ? selected = 2 : selected = null"
                            class="flex justify-between items-center w-full px-6 py-5 text-left font-semibold hover:bg-gray-50 focus:outline-none transition-all duration-300"
                            style="font-family: 'Poppins', sans-serif; color: #010522;"
                        >
                            <span>What information should I include in my message?</span>
                            <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': selected === 2}" style="color: #F97316;"></i>
                        </button>
                        <div
                            x-show="selected === 2"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-4"
                            class="px-6 py-5 text-gray-600"
                            style="background-color: #FFF7ED; font-family: 'Poppins', sans-serif; font-weight: 300;"
                        >
                            To help us assist you more efficiently, please include your account username (if applicable), a detailed description of your inquiry or issue, and any relevant information such as transaction IDs or screenshots.
                        </div>
                    </div>

                    <div class="border-2 rounded-lg overflow-hidden transition-all duration-300" style="border-color: #FFF7ED;">
                        <button
                            @click="selected !== 3 ? selected = 3 : selected = null"
                            class="flex justify-between items-center w-full px-6 py-5 text-left font-semibold hover:bg-gray-50 focus:outline-none transition-all duration-300"
                            style="font-family: 'Poppins', sans-serif; color: #010522;"
                        >
                            <span>Is there a dedicated support team for premium accounts?</span>
                            <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': selected === 3}" style="color: #F97316;"></i>
                        </button>
                        <div
                            x-show="selected === 3"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-4"
                            class="px-6 py-5 text-gray-600"
                            style="background-color: #FFF7ED; font-family: 'Poppins', sans-serif; font-weight: 300;"
                        >
                            Yes, premium account holders have access to a dedicated support team with priority response times and personalized assistance. Please mention your premium status in your message for expedited service.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20" style="background-color: #F97316;">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <div class="text-5xl font-bold mb-2" style="font-family: 'Poppins', sans-serif;">
                        <span class="counter" data-target="50000">5800</span>+
                    </div>
                    <p class="text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 300;">Active Users</p>
                </div>
                <div>
                    <div class="text-5xl font-bold mb-2" style="font-family: 'Poppins', sans-serif;">
                        $<span class="counter" data-target="500">500,000</span>+
                    </div>
                    <p class="text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 300;">Total Transactions</p>
                </div>
                <div>
                    <div class="text-5xl font-bold mb-2" style="font-family: 'Poppins', sans-serif;">
                        <span class="counter" data-target="150">12</span>+
                    </div>
                    <p class="text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 300;">Countries Served</p>
                </div>
                <div>
                    <div class="text-5xl font-bold mb-2" style="font-family: 'Poppins', sans-serif;">
                        <span class="counter" data-target="99">80</span>%
                    </div>
                    <p class="text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 300;">Satisfaction Rate</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8 max-w-md w-full mx-4 relative z-10">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" style="background-color: #E8F5F0;">
                    <i class="fas fa-check text-2xl" style="color: #0F9D58;"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="font-family: 'Poppins', sans-serif; color: #010522;">Message Sent Successfully</h3>
                <p class="text-gray-600 mb-6" id="infs" style="font-family: 'Poppins', sans-serif; font-weight: 300;">Thank you for contacting us. We will get back to you shortly.</p>
                <div>
                    <button type="button" class="close-modal px-6 py-3 text-sm font-semibold text-white rounded-lg transition-all duration-300 hover:scale-105" style="background-color: #0F9D58; font-family: 'Poppins', sans-serif;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="error-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8 max-w-md w-full mx-4 relative z-10">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="font-family: 'Poppins', sans-serif; color: #010522;">Error</h3>
                <p class="text-gray-600 mb-6" id="inf" style="font-family: 'Poppins', sans-serif; font-weight: 300;">There was an error sending your message. Please try again.</p>
                <div>
                    <button type="button" class="close-modal px-6 py-3 text-sm font-semibold text-white rounded-lg transition-all duration-300 hover:scale-105" style="background-color: #0F9D58; font-family: 'Poppins', sans-serif;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="temp/custom/js/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // Modal functionality
            window.Modal = {
                openModal: function(type) {
                    if(type === 'success') {
                        $('#success-modal').removeClass('hidden');
                    } else if(type === 'error') {
                        $('#error-modal').removeClass('hidden');
                    }
                },
                closeModal: function() {
                    $('#success-modal, #error-modal').addClass('hidden');
                }
            };

            $('.close-modal').click(function() {
                window.Modal.closeModal();
            });

            // Form submission
            function butt(){
                loginq = $("input[name=username]").val().trim();
                email = $("input[name=email]").val().trim();
                message = $("textarea[name=text]").val().trim();

                if(!email){
                    $("#inf").text("Email entered incorrectly");
                    window.Modal.openModal('error');
                    return;
                } else if(!message){
                    $("#inf").text("The message field is empty, please fill it in and repeat the procedure");
                    window.Modal.openModal('error');
                    return;
                }

                $.ajax({
                    url:'/ajax/php/support.php',
                    type:'POST',
                    cache:false,
                    data:{'loginq':loginq,'email':email,'message':message},
                    dataType: 'text',
                    beforeSend:function(){
                        $("#button").prop("disabled", true);
                    },
                    success:function(data){
                        $("#button").prop("disabled", false);
                        dat = data.substr(1);
                        if(data[0]==0){
                            $("#inf").text(dat);
                            window.Modal.openModal('error');
                        } else if(data[0]==1){
                            $("#formPos").trigger("reset");
                            $("#infs").text(dat);
                            window.Modal.openModal('success');
                        }
                    }
                });
            }

            $("#button").click(function(){
                butt();
            });

            $('input[name=username],input[name=email],textarea[name=text]').keydown(function(e) {
                if(e.keyCode === 13) {
                    butt();
                }
            });

            // Counter animation
            $('.counter').each(function() {
                const $this = $(this);
                const countTo = $this.attr('data-target');
                
                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'linear',
                    step: function() {
                        $this.text(Math.floor(this.countNum).toLocaleString());
                    },
                    complete: function() {
                        $this.text(this.countNum.toLocaleString());
                    }
                });
            });

            // Close modals when clicking outside
            $(document).mouseup(function(e) {
                var successModal = $("#success-modal > div:last-child");
                var errorModal = $("#error-modal > div:last-child");
                if (!successModal.is(e.target) && successModal.has(e.target).length === 0 &&
                    !errorModal.is(e.target) && errorModal.has(e.target).length === 0) {
                    window.Modal.closeModal();
                }
            });
        });
    </script>
        </div>
    </main>

@endsection
