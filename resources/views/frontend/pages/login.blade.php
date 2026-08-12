@extends('frontend.layouts.app') 
@section('content') 
<div id="main-module-container">
  <div class="slideshow-wrapper main-slideshow wd_wide" style="min-height: 48px;">
    <div class="slideshow-sub-wrapper"></div>
  </div>
  <div class="breadcrumb-title-wrapper">
    <div class="breadcrumb-title">
      <h1 class="heading-title page-title">আমার অ্যাকাউন্ট</h1>
    </div>
  </div>
  <div id="container" class="page-template default-template wd_wide">
    <div id="content" class="container" role="main">
      <div id="main">
        <div id="container-main" class="col-sm-24">
          <div class="main-content">
            <article id="post-9" class="post-9 page type-page status-publish hentry">
              <div class="entry-content-post">
                <div class="woocommerce">
                  <div class="woocommerce-notices-wrapper"></div>
                  <div class="col2-set" id="customer_login">
                    <div class="wd_title_myaccount">
                      <h1 class="heading-title">আমার অ্যাকাউন্ট</h1>
                    </div>
                    <div class="col-1">
                      <h2>লগইন</h2>
                      <form method="post" action="{{route('customerLogin')}}" class="login">
                        @csrf
                        <p class="form-row form-row-first">
                          <label for="email">ইউজার নাম অথবা ইমেইল <span class="required">*</span>
                          </label>
                          <input type="text" class="input-text" name="email" id="email">
                        </p>
                        <p class="form-row form-row-last">
                          <label for="password">পাসওয়ার্ড <span class="required">*</span>
                          </label>
                          <input class="input-text" type="password" name="password" id="password">
                        </p>
                        <p class="form-row button-login">
                          <input type="hidden" id="_wpnonce" name="_wpnonce" value="e727563e5d">
                          <input type="hidden" name="_wp_http_referer" value="/my-account">
                          <input type="submit" class="button" name="login" value="লগইন">
                          <label for="rememberme" class="inline">
                          <input name="rememberme" type="checkbox" id="rememberme" value="forever"> মনে রাখুন </label>
                        </p>
                        <div class="wd_forgot_pass">
                          <p class="lost_password">
                            <a href="javascript:void(0)">আপনি কি পাসওয়ার্ড ভুলে গেছেন?</a>
                          </p>
                        </div>
                      </form>
                    </div>
                    <div class="col-2">
                      <h2>রেজিস্টার</h2>
                      <form method="post" action="{{route('customersignUp')}}" class="register">
                        @csrf
                        <p class="form-row form-row-first">
                          <label for="reg_billing_first_name">নাম <span class="required">*</span>
                          </label>
                          <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="">
                        </p>
                        <p class="form-row">
                          <label for="reg_billing_phone">ফোন নাম্বার <span class="required">*</span>
                          </label>
                          <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="">
                        </p>
                        <div class="clear"></div>
                        <p class="form-row form-row-first">
                          <label for="reg_email">ইমেইল <span class="required">*</span>
                          </label>
                          <input type="email" class="input-text" name="email" id="reg_email" value="">
                        </p>
                        <p class="form-row form-row-last">
                          <label for="reg_password">পাসওয়ার্ড <span class="required">*</span>
                          </label>
                          <input type="password" class="input-text" name="password" id="reg_password" value="">
                        </p>
                        <p class="form-row form-row-last">
                          <label for="reg_confirm_password">পাসওয়ার্ড নিশ্চিত করুন <span class="required">*</span>
                          </label>
                          <input type="password" class="input-text" name="confirm_password" id="reg_confirm_password" value="">
                        </p>
                        <div style="left:-999em; position:absolute;">
                          <label for="trap">Anti-spam</label>
                          <input type="text" name="email_2" id="trap" tabindex="-1">
                        </div>
                        <div class="woocommerce-privacy-policy-text"></div>
                        <p class="form-row">
                          <input type="hidden" id="_wpnonce" name="_wpnonce" value="605aa09c84">
                          <input type="hidden" name="_wp_http_referer" value="/my-account">
                          <input type="submit" class="button" name="register" value="রেজিস্টার">
                        </p>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <footer class="entry-meta"></footer>
            </article>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> 
@endsection