@php
    if ($settings->redirect_url != null or !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')


@inject('content', 'App\Http\Controllers\FrontController')
@section('content')

     

        <div class="stricky-header stricked-menu main-menu">
            <div class="sticky-header__content"></div><!-- /.sticky-header__content -->
        </div><!-- /.stricky-header -->
<!--Page Header Start-->
<section class="page-header">
	<div class="page-header-bg" style="background-image: url(public/images/page-header-bg.jpg)">
	</div>
	<div class="container">
		<div class="page-header__inner">
			<h2>F.A.Q</h2>
			<ul class="thm-breadcrumb list-unstyled">
				<li><a href="/">Home</a></li>
				<li class="active">Faq</li>
			</ul>
		</div>
	</div>
</section>

<!--Get To Know Start-->
<section class="get-to-know">
	<div class="container">
		<div class="row">
			<div class="col-xl-12">
				<div class="get-to-know__left">
					<div class="section-title text-left">
						<h2 class="section-title__title">
							Frequently Asked Questions
						</h2>
					</div>

					<div class="faqWrapper">
						<h5>What is {{$settings->site_name}}?</h5>
						<blockquote>
							<p>
								{{$settings->site_name}} is a strategy that targets investment opportunities intended to create both financial returns and positive social and/or environmental impact. The company is managed by a team of investment experts who work day and night to sure constant returns from our investments.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>Who may open an account with {{$settings->site_name}}?</h5>
						<blockquote>
							<p>
								Any individual 18 or over, The investor country is not limited to the United Kingdom, We allow investors from all parts of the world. The account cannot be created for anyone below 18 or legal investment age.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>What if I forgot my password?</h5>
						<blockquote>
							<p>
								Click the login button, On the form function, there is an option to reset your password if you forget your previous password.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>What is the minimum amount required to invest with {{$settings->site_name}}?</h5>
						<blockquote>
							<p>
								Our program minimum deposit is $10, You can invest $10 from your eWallet account. Profit from the $10 deposit can be withdrawn at the end of the investment period or you can re-invest from your account balance.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>Which e-currencies do you accept?</h5>
						<blockquote>
							<p>
								Members can invest with Bitcoin, Tron, Ethereum, Usdt, Perfect Money, Payeer, and Bitcoin Cash. Kindly note that you can contact us for other deposit options if you cannot use any of the options listed on this page.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>How can I withdraw funds?</h5>
						<blockquote>
							<p>
								Login to your account using your username and password and check the Withdraw section. Withdrawal are processed manually all 7 days of the week and within 0 - 24 hours after withdrawal submission. You must withdraw into same payment method use for the website (e.g Bitcoin Deposit must be withdrawn into Bitcoin wallet)
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>How long does it take for my deposit to be added to my account?</h5>
						<blockquote>
							<p>
								All deposits made on this site are logged instantly, you should also get an instant email notification about this deposit.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>Do you have any fees for my withdrawal?</h5>
						<blockquote>
							<p>
								We do not charge a fee for withdrawal, The payment fee for withdrawal is determined by your payment processor, we do not have control over such fee. We send exactly the amount submitted by you.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>What is the minimum and maximum Withdrawal amount?</h5>
						<blockquote>
							<p>
								The minimum withdrawal amount is only $0.10 for Perfectmoney and Payeer, and for crypto-currency like bitcoin and bitcoin cash minimum withdrawal is $0.5. The maximum withdrawal amount has no limit.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>How will I receive my referral commission?</h5>
						<blockquote>
							<p>
								The commission for a referral is added to the account balance as soon as they are earned. You can make withdraw of the commission from your account balance. Processing of commission takes between 0 - 24 hours as it is processed manually by our team.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>Can change my account Email and Wallet Addresses?</h5>
						<blockquote>
							<p>
								We always change account email manually from our office for security reason, You may need to contact us to change your email address. eWallets address/accounts can be modified directly from your account area by clicking the "Edit Account" button.
							</p>
						</blockquote>
					</div>

					<div class="faqWrapper">
						<h5>How Reliable is this program?</h5>
						<blockquote>
							<p>
								Our investment resource is equipped with advanced methods of protection against DDoS-attacks and reliable data encryption this is an assurance that this website will always available to member. You can be completely sure that our team of experts will always guarantee returns from an investment of all members without any form of risk.
							</p>
						</blockquote>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</section>
<!--Get To Know End-->
        
@endsection