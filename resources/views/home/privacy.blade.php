@php
    if ($settings->redirect_url != null or !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp

@extends('layouts.base')
@section('title', 'Terms and Privacy And Policy')
@section('styles')
@parent
@endsection
@inject('content', 'App\Http\Controllers\FrontController')
@section('content')


    <!-- Page Content -->
    <main class="flex-grow">
        <div class="animate-fade-in">
                <!-- Hero Section -->
    <section class="relative bg-horizon-700 py-16 md:py-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-horizon-800 to-horizon-700 opacity-90"></div>
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-24 h-24 bg-gold-500 rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-16 h-16 bg-gold-400 rounded-full animate-pulse delay-1000"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-display font-bold text-white leading-tight mb-4">
                    Privacy <span class="text-gold-500">Policy</span>
                </h1>
                <nav class="flex justify-center mt-6" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-white text-opacity-80">
                        <li>
                            <a href="index" class="hover:text-gold-400 transition-colors duration-200">{{$settings->site_name}}</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-gold-500 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gold-400">Privacy Policy</span>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
    </section>

    <!-- Privacy Policy Content -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6 md:p-8">
                        <!-- Our Commitment -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                Our Commitment to You
                            </h2>
                            <p class="text-secondary-700 mb-4">
                                Thank you for showing interest in our service. In order for us to provide you with our service, we are required to collect and process certain personal data about you and your activity.
                            </p>
                            <p class="text-secondary-700 mb-4">
                                By entrusting us with your personal data, we would like to assure you of our commitment to keep such information private and to operate in accordance with all regulatory laws and all EU data protection laws, including General Data Protection Regulation (GDPR) 679/2016 (EU).
                            </p>
                            <p class="text-secondary-700">
                                We have taken measurable steps to protect the confidentiality, security and integrity of this data. We encourage you to review the following information carefully.
                            </p>
                        </div>

                        <!-- Grounds for data collection -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                Grounds for Data Collection
                            </h2>
                            <p class="text-secondary-700 mb-4">
                                Processing of your personal information (meaning, any data which may potentially allow your identification with reasonable means; hereinafter "Personal Data") is necessary for the performance of our contractual obligations towards you and providing you with our services, to protect our legitimate interests and for compliance with legal and financial regulatory obligations to which we are subject.
                            </p>
                            <p class="text-secondary-700">
                                When you use our services, you consent to the collection, storage, use, disclosure and other uses of your Personal Data as described in this Privacy Policy.
                            </p>
                        </div>

                        <!-- How do we receive data -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                How Do We Receive Data About You?
                            </h2>
                            <p class="text-secondary-700 mb-4">
                                We receive your Personal Data from various sources:
                            </p>
                            <ol class="list-decimal pl-6 space-y-2 text-secondary-700">
                                <li>When you voluntarily provide us with your personal details in order to create an account (for example, your name and email address)</li>
                                <li>When you use or access our site and services, in connection with your use of our services (for example, your financial transactions)</li>
                            </ol>
                        </div>

                        <!-- What type of data we collect -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                What Type of Data We Collect?
                            </h2>
                            <p class="text-secondary-700 mb-4">
                                In order to open an account, and in order to provide you with our services we will need you to collect the following data:
                            </p>

                            <h3 class="text-xl font-semibold text-horizon-700 mb-3">Personal Data</h3>
                            <p class="text-secondary-700 mb-4">We collect the following Personal Data about you:</p>

                            <ul class="list-disc pl-6 space-y-3 text-secondary-700 mb-6">
                                <li>
                                    <span class="font-medium text-horizon-800">Registration data</span> – your name, email address, phone number, occupation, country of residence, and your age (in order to verify you are over 18 years of age and eligible to participate in our service).
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Voluntary data</span> – when you communicate with us (for example when you send us an email or use a "contact us" form on our site) we collect the personal data you provided us with.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Financial data</span> – by its nature, your use of our services includes financial transactions, thus requiring us to obtain your financial details, which includes, but not limited to your payment details (such as bank account details and financial transactions performed through our services).
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Technical data</span> – we collect certain technical data that is automatically recorded when you use our services, such as your IP address, MAC address, device approximate location.
                                </li>
                            </ul>

                            <h3 class="text-xl font-semibold text-horizon-700 mb-3">Non-Personal Data</h3>
                            <p class="text-secondary-700 mb-4">
                                We record and collect data from or about your device (for example your computer or your mobile device) when you access our services and visit our site. This includes, but not limited to: your login credentials, UDID, Google advertising ID, IDFA, cookie identifiers, and may include other identifiers such your operating system version, browser type, language preferences, time zone, referring domains and the duration of your visits. This will facilitate our ability to improve our service and personalize your experience with us.
                            </p>
                            <p class="text-secondary-700">
                                If we combine Personal Data with non-Personal Data about you, the combined data will be treated as Personal Data for as long as it remains combined.
                            </p>
                        </div>

                        <!-- Tracking Technologies -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                Tracking Technologies
                            </h2>
                            <p class="text-secondary-700">
                                When you visit or access our services we use (and authorize 3rd parties to use) pixels, cookies, events and other technologies ("Tracking Technologies"). Those allow us to automatically collect data about you, your device and your online behavior, in order to enhance your navigation in our services, improve our site's performance, perform analytics and customize your experience on it. In addition, we may merge data we have with data collected through said tracking technologies with data we may obtain from other sources and, as a result, such data may become Personal Data.
                            </p>
                        </div>

                        <!-- How do we use the data -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                How Do We Use the Data We Collect?
                            </h2>
                            <ul class="list-disc pl-6 space-y-3 text-secondary-700">
                                <li>
                                    <span class="font-medium text-horizon-800">Provision of service</span> – we will use your Personal Data you provide us for the provision and improvement of our services to you.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Marketing purposes</span> – we will use your Personal Data (such as your email address or phone number). For example, by subscribing to our newsletter you will receive tips and announcements straight to your email account. We may also send you promotional material concerning our services or our partners' services (which we believe may interest you), including but not limited to, by building an automated profile based on your Personal Data, for marketing purposes. You may choose not to receive our promotional or marketing emails (all or any part thereof) by clicking on the "unsubscribe" link in the emails that you receive from us. Please note that even if you unsubscribe from our newsletter, we may continue to send you service-related updates and notifications or reply to your queries and feedback you provide us.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Opt-out of receiving marketing materials</span> – If you do not want us to use or share your personal data for marketing purposes, you may opt-out in accordance with this "Opt-out" section. Please note that even if you opt-out, we may still use and share your personal information with third parties for non-marketing purposes (for example to fulfill your requests, communicate with you and respond to your inquiries, etc.). In such cases, the companies with whom we share your personal data are authorized to use your Personal Data only as necessary to provide these non-marketing services.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Analytics, surveys and research</span> – we are always trying to improve our services and think of new and exciting features for our users. From time to time, we may conduct surveys or test features, and analyze the information we have to develop, evaluate and improve these features.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Protecting our interests</span> – we use your Personal Data when we believe it's necessary in order to take precautions against liabilities, investigate and defend ourselves against any third-party claims or allegations, investigate and protect ourselves from fraud, protect the security or integrity of our services and protect the rights and property of the company, its users and/or partners.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Enforcing of policies</span> – we use your Personal Data in order to enforce our policies, including but limited to our Terms & Conditions.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Compliance with legal and regulatory requirements</span> – we also use your Personal Data to investigate violations and prevent money laundering and perform due-diligence checks, and as required by law, regulation or other governmental authority, or to comply with a subpoena or similar legal process.
                                </li>
                            </ul>
                        </div>

                        <!-- With whom do we share your Personal Data -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                With Whom Do We Share Your Personal Data
                            </h2>
                            <ul class="list-disc pl-6 space-y-3 text-secondary-700">
                                <li>
                                    <span class="font-medium text-horizon-800">Internal concerned parties</span> – we share your data with companies in our group, as well as our employees limited to those employees or partners who need to know the information in order to provide you with our services.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Financial providers and payment processors</span> – we share your financial data about you for purposes of accepting deposits or performing risk analysis.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Business partners</span> – we share your data with business partners, such as storage providers and analytics providers who help us provide you with our service.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Legal and regulatory entities</span> – we may disclose any data in case we believe, in good faith, that such disclosure is necessary in order to enforce our Terms & Conditions take precautions against liabilities, investigate and defend ourselves against any third party claims or allegations, protect the security or integrity of the site and our servers and protect the rights and property, our users and/or partners. We may also disclose your personal data where requested any other regulatory authority having control or jurisdiction over us, you or our associates or in the territories we have clients or providers, as a broker.
                                </li>
                                <li>
                                    <span class="font-medium text-horizon-800">Merger and acquisitions</span> – we may share your data if we enter into a business transaction such as a merger, acquisition, reorganization, bankruptcy, or sale of some or all of our assets. Any party that acquires our assets as part of such a transaction may continue to use your data in accordance with the terms of this Privacy Policy.
                                </li>
                            </ul>
                        </div>

                        <!-- Transfer of data outside the EEA -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                Transfer of Data Outside the EEA
                            </h2>
                            <p class="text-secondary-700">
                                Please note that some data recipients may be located outside the EEA. In such cases, we will transfer your data only to such countries as approved by the European Commission as providing an adequate level of data protection or enter into legal agreements ensuring an adequate level of data protection.
                            </p>
                        </div>

                        <!-- How we protect your data -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                How We Protect Your Data
                            </h2>
                            <p class="text-secondary-700 mb-4">
                                We have implemented administrative, technical, and physical safeguards to help prevent unauthorized access, use, or disclosure of your personal data. Your data is stored on secure servers and isn't publicly available. We limit access of your data only to those employees or partners that need to know the information in order to enable the carrying out of the agreement between us.
                            </p>
                            <p class="text-secondary-700 mb-4">
                                You need to help us prevent unauthorized access to your account by protecting your password appropriately and limiting access to your account (for example, by signing off after you have finished accessing your account). You will be solely responsible for keeping your password confidential and for all use of your password and your account, including any unauthorized use.
                            </p>
                            <p class="text-secondary-700">
                                While we seek to protect your data to ensure that it is kept confidential, we cannot absolutely guarantee its security. You should be aware that there is always some risk involved in transmitting data over the internet. While we strive to protect your Personal Data, we cannot ensure or warrant the security and privacy of your personal Data or other content you transmit using the service, and you do so at your own risk.
                            </p>
                        </div>

                        <!-- Retention -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                Retention
                            </h2>
                            <p class="text-secondary-700">
                                We will retain your personal data for as long as necessary to provide our services, and as necessary to comply with our legal obligations, resolve disputes, and enforce our policies. Retention periods will be determined taking into account the type of data that is collected and the purpose for which it is collected, bearing in mind the requirements applicable to the situation and the need to destroy outdated, unused data at the earliest reasonable time. Under applicable regulations, we will keep records containing client personal data, trading information, account opening documents, communications and anything else as required by applicable laws and regulations.
                            </p>
                        </div>

                        <!-- User Rights -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                User Rights
                            </h2>
                            <ol class="list-decimal pl-6 space-y-2 text-secondary-700 mb-4">
                                <li>Receive confirmation as to whether or not personal data concerning you is being processed, and access your stored personal data, together with supplementary data.</li>
                                <li>Receive a copy of personal data you directly volunteer to us in a structured, commonly used and machine-readable format.</li>
                                <li>Request rectification of your personal data that is in our control.</li>
                                <li>Request erasure of your personal data.</li>
                                <li>Object to the processing of personal data by us.</li>
                                <li>Request to restrict processing of your personal data by us.</li>
                            </ol>
                            <p class="text-secondary-700">
                                However, please note that these rights are not absolute, and may be subject to our own legitimate interests and regulatory requirements.
                            </p>
                        </div>

                        <!-- How to Contact us -->
                        <div class="mb-10">
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                How to Contact Us?
                            </h2>
                            <p class="text-secondary-700 mb-4">
                                If you wish to exercise any of the aforementioned rights, or receive more information, please contact our General Data Protection Officer ("GDPO") using the details provided below:
                            </p>
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <p class="text-horizon-800">Email: <a  class="text-gold-600 hover:text-gold-700"><span class="__cf_email__" data-cfemail="473432373728353307222b2e3322252233263022262b332f6924282a">{{$settings->contact_email}}</span></a></p>
                                <p class="text-horizon-800">Attn. GDPO Compliance Officer</p>
                            </div>
                            <p class="text-secondary-700">
                                If you decide to terminate your account, you may do so by emailing us at <a  class="text-gold-600 hover:text-gold-700"><span class="__cf_email__" data-cfemail="3a494f4a4a55484e7a5f56534e5f585f4e5b4d5f5b564e5214595557">{{$settings->contact_email}}</span></a>. If you terminate your account, please be aware that personal information that you have provided us may still be maintained for legal and regulatory reasons (as described above), but it will no longer be accessible via your account.
                            </p>
                        </div>

                        <!-- Updates to this Policy -->
                        <div>
                            <h2 class="text-2xl font-display font-bold text-horizon-800 mb-4 pb-2 border-b border-gray-100">
                                Updates to this Policy
                            </h2>
                            <p class="text-secondary-700">
                                This Privacy Policy is subject to changes from time to time, at our sole discretion. The most current version will always be posted on our website (as reflected in the "Last Updated" heading). You are advised to check for updates regularly. In the event of material changes, we will provide you with a notice. By continuing to access or use our services after any revisions become effective, you agree to be bound by the updated Privacy Policy.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Top Button -->
    <div id="button-up" class="hidden fixed right-6 bottom-16 bg-white text-horizon-700 rounded-full shadow-lg p-3 cursor-pointer hover:bg-gray-100 transition-all duration-300 z-50">
        <i class="fa fa-chevron-up text-xl"></i>
    </div>

    <script data-cfasync="false" src="https://cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        $(document).ready(function() {
            // Back to top button
            var button = $('#button-up');
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    button.fadeIn();
                } else {
                    button.fadeOut();
                }
            });

            button.on('click', function(){
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });
        });
    </script>
        </div>
    </main>


@endsection

@section('scripts')
@parent

@endsection
