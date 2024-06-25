<?php if (!get_option('users_can_register')) : ?>

  <div class="notification error closeable" style="display: block">
    <p><?php esc_html_e('Registration is disabled', 'listeo_core') ?></p>
  </div>

<?php else : ?>

  <!-- Show errors if there are any -->
  <?php if (count($attributes['errors']) > 0) : ?>
    <?php foreach ($attributes['errors'] as $error) : ?>
      <div class="login-error tw-mb-8 tw-py-4 tw-px-6 tw-rounded-md tw-bg-red-100 tw-border-red-300 tw-text-red-500">
        <?php echo $error; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <form enctype="multipart/form-data" class="register listeo-registration-form" id="register" action="<?php echo wp_registration_url(); ?>" method="post">

    <div class="listeo-register-form-fields-container">

      <?php
      $default_role = get_option('listeo_registration_form_default_role', 'guest');
      if (!get_option('listeo_registration_hide_role')) : ?>
        <div class="account-type">
          <div>
            <input type="radio" name="user_role" id="freelancer-radio" value="guest" class="account-type-radio" <?php if ($default_role == 'guest') { ?> checked <?php  } ?> />
            <label for="freelancer-radio"><i class="sl sl-icon-user"></i> <?php esc_html_e('Guest', 'listeo_core') ?></label>
          </div>
          <?php if (class_exists('WeDevs_Dokan')  && get_option('listeo_role_dokan') == 'seller') : ?>
            <div>
              <input type="radio" name="user_role" id="employer-radio" value="seller" class="account-type-radio" <?php if ($default_role == 'owner') { ?> checked <?php  } ?> />
              <label for="employer-radio"><i class="sl sl-icon-briefcase"></i> <?php esc_html_e('Owner', 'listeo_core') ?></label>
            </div>
          <?php else : ?>
            <div>
              <input type="radio" name="user_role" id="employer-radio" value="owner" class="account-type-radio" <?php if ($default_role == 'owner') { ?> checked <?php  } ?> />
              <label for="employer-radio"><i class="sl sl-icon-briefcase"></i> <?php esc_html_e('Owner', 'listeo_core') ?></label>
            </div>
          <?php endif; ?>
        </div>
        <div class="clearfix"></div>
      <?php endif; ?>

      <?php if (!get_option('listeo_registration_hide_username')) : ?>
        <p class="form-row form-row-wide">
          <label for="username2" class="tw-relative tw-block tw-m-0">
            <i class="sl sl-icon-user tw-text-base tw-absolute tw-top-3 tw-left-4 tw-text-gray-400"></i>
            <input required placeholder="<?php esc_html_e('Username', 'listeo_core'); ?>" type="text" class="!tw-pl-11 !tw-text-base !tw-leading-normal !tw-h-12" name="username" id="username2" value="" />
          </label>
        </p>
      <?php endif; ?>

      <p class="form-row form-row-wide">
        <label for="email" class="tw-relative tw-block tw-m-0">
          <i class="sl sl-icon-envelope-open tw-text-base tw-absolute tw-top-3 tw-left-4 tw-text-gray-400"></i>
          <input required type="email" placeholder="<?php esc_html_e('Email Address', 'listeo_core'); ?>" class="!tw-pl-11 !tw-text-base !tw-leading-normal !tw-h-12" name="email" id="email" value="" />
        </label>
      </p>

      <?php if (get_option('listeo_display_password_field')) : ?>
        <p class="form-row form-row-wide" id="password-row">
          <label for="password1" class="tw-relative tw-block tw-m-0">
            <i class="sl sl-icon-lock tw-text-base tw-absolute tw-top-3 tw-left-4 tw-text-gray-400"></i>
            <input required placeholder="<?php esc_html_e('Password', 'listeo_core'); ?>" class="!tw-pl-11 !tw-text-base !tw-leading-normal !tw-h-12" type="password" name="password" id="password1" />
            <span class="pwstrength_viewport_progress"></span>

          </label>
        </p>
      <?php endif; ?>

      <?php if (get_option('listeo_display_first_last_name')) : ?>
        <p class="form-row form-row-wide">
          <label for="first-name" class="tw-relative tw-block tw-m-0">
            <i class="sl sl-icon-pencil tw-text-base tw-absolute tw-top-3 tw-left-4 tw-text-gray-400"></i>
            <input <?php if (get_option('listeo_display_first_last_name_required')) { ?>required <?php } ?> placeholder="<?php esc_html_e('First Name', 'listeo_core'); ?>" type="text" name="first_name" id="first-name" class="!tw-pl-11 !tw-text-base !tw-leading-normal !tw-h-12"></label>
        </p>

        <p class="form-row form-row-wide">
          <label for="last-name" class="tw-relative tw-block tw-m-0">
            <i class="sl sl-icon-pencil tw-text-base tw-absolute tw-top-3 tw-left-4 tw-text-gray-400"></i>
            <input <?php if (get_option('listeo_display_first_last_name_required')) { ?>required <?php } ?> placeholder="<?php esc_html_e('Last Name', 'listeo_core'); ?>" type="text" name="last_name" id="last-name" class="!tw-pl-11 !tw-text-base !tw-leading-normal !tw-h-12">
          </label>
        </p>
      <?php endif; ?>


      <?php if (get_option('listeo_otp_status')) : ?>
        <p class="form-row form-row-wide">
          <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.3/build/js/intlTelInput.min.js"></script>
          <script>
            document.addEventListener('DOMContentLoaded', (event) => {
              const input = document.querySelector("#phone");
              const form = document.querySelector("#register");
              const otpSubmitButton = document.querySelector("#otp_submit");

              if (input) {
                const iti = window.intlTelInput(input, {
                  initialCountry: "auto",
                  nationalMode: true,
                  hiddenInput: () => "full_phone",
                  geoIpLookup: callback => {
                    fetch("https://ipapi.co/json")
                      .then(res => res.json())
                      .then(data => callback(data.country_code))
                      .catch(() => callback("us"));
                  },
                  utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.3/build/js/utils.js" // just for formatting/placeholders etc
                });
                const fullPhoneInput = document.querySelector("input[name='full_phone']");
                // check if the number is valid on focus out
                input.addEventListener('blur', () => {

                  if (!iti.isValidNumber()) {
                    // handle error
                    input.classList.add("error");
                  } else {
                    // if number is valid, submit the form
                    input.classList.add("validphone");
                    fullPhoneInput.value = iti.getNumber();
                  }
                });
                otpSubmitButton.addEventListener('click', (event) => {
                  event.preventDefault();
                  if (!iti.isValidNumber()) {
                    // handle error
                    input.classList.add("error");

                  } else {
                    // if number is valid, submit the form
                    input.classList.add("validphone");
                    fullPhoneInput.value = iti.getNumber();
                  }
                });

                input.addEventListener('input', () => {
                  if (input.classList.contains("error")) {
                    input.classList.remove("error");
                  }
                });
              };

              // listen to "keyup", but also "change" to update when the user selects a country


            });
          </script>
          <label for="phone" class="tw-relative tw-block tw-m-0">
            <i class="sl sl-icon-phone tw-text-base tw-absolute tw-top-3 tw-left-4 tw-text-gray-400"></i>
            <input required type="tel" placeholder="<?php esc_html_e('Phone', 'listeo_core'); ?>" class="input-text" name="phone" id="phone" value="" />
          </label>
        </p>
      <?php endif ?>

      <!-- //extra fields -->
      <div id="listeo-core-registration-fields">
        <?php echo listeo_get_extra_registration_fields($default_role); ?>
      </div>

      <!-- eof custom fields -->
      <?php if (!get_option('listeo_display_password_field')) : ?>
        <p class="form-row form-row-wide margin-top-30 margin-bottom-30"><?php esc_html_e('Note: Your password will be generated automatically and sent to your email address.', 'listeo_core'); ?>
        </p>
      <?php endif; ?>

      <?php
      $recaptcha = get_option('listeo_recaptcha');
      $recaptcha_version = get_option('listeo_recaptcha_version', 'v2');
      if ($recaptcha && $recaptcha_version == 'v2') : ?>
        <p class="form-row captcha_wrapper">
        <div class="g-recaptcha" data-sitekey="<?php echo get_option('listeo_recaptcha_sitekey'); ?>"></div>
        </p>
      <?php elseif ($recaptcha && $recaptcha_version == 'v3') : ?>
        <input type="hidden" id="rc_action" name="rc_action" value="ws_register">
        <input type="hidden" id="token" name="token">
      <?php endif ?>

      <?php
      $privacy_policy_status = get_option('listeo_privacy_policy');
      if ($privacy_policy_status && function_exists('the_privacy_policy_link')) : ?>
        <p class="form-row margin-top-10 checkboxes margin-bottom-10">
          <input type="checkbox" id="privacy_policy" name="privacy_policy">
          <label for="privacy_policy"><?php esc_html_e('I agree to the', 'listeo_core'); ?> <a target="_blank" href="<?php echo get_privacy_policy_url(); ?>"><?php esc_html_e('Privacy Policy', 'listeo_core'); ?></a> </label>
        </p>
      <?php endif ?>

      <?php
      $terms_and_condition_status = get_option('listeo_terms_and_conditions_req');
      $terms_and_condition_status_page = get_option('listeo_terms_and_conditions_page');
      if ($terms_and_condition_status) : ?>
        <p class="form-row margin-top-10 checkboxes margin-bottom-10">
          <input type="checkbox" id="terms_and_conditions" name="terms_and_conditions">
          <label for="terms_and_conditions"><?php esc_html_e('I agree to the', 'listeo_core'); ?> <a target="_blank" href="<?php echo get_permalink($terms_and_condition_status_page); ?>"><?php esc_html_e('Terms and Conditions', 'listeo_core'); ?></a> </label>
        </p>
      <?php endif ?>

    </div>

    <?php if (get_option('listeo_otp_status')) : ?>
      <div class="otp_registration-wrapper" style="display: none">
        <?php do_action('listeo_before_otp_form'); ?>
        <div class="otp_registration">
          <h4><?php esc_html_e('Verification Code', 'listeo_core'); ?></h4>

          <p class="form-row margin-top-10 otp_code margin-bottom-10">
            <?php esc_html_e('Please enter the 4 digit code sent to your mobile number.', 'listeo_core'); ?>
            <span class="otp-countdown-valid-text"><?php esc_html_e('The code is valid for', 'listeo_core'); ?> </span> <span class="otp-countdown"></span>
            <a id="resend_otp" class="hidden" href=" #"><?php esc_html_e('Time has passed, click here to resend code', 'listeo_core'); ?></a>
          </p>



          <div id="listeo_otp-inputs">
            <input type=" tel" name="listeo_otp[token][code_1]" required="required" class="field__token" autocomplete="off" data-error="error_second_step_authentication_token" data-input-type="user">
            <input type="tel" name="listeo_otp[token][code_2]" required="required" class="field__token" autocomplete="off" data-error="error_second_step_authentication_token" data-input-type="user">
            <input type="tel" name="listeo_otp[token][code_3]" required="required" class="field__token" autocomplete="off" data-error="error_second_step_authentication_token" data-input-type="user">
            <input type="tel" name="listeo_otp[token][code_4]" required="required" class="field__token" autocomplete="off" data-error="error_second_step_authentication_token" data-input-type="user">

          </div>

          <span id="error_listeo_otp" class="hidden"><?php esc_html_e('Wrong authorization code. Try again later.', 'listeo_core'); ?></span>
          <span id="error_listeo_otp_time" class="hidden"><?php esc_html_e('The code has expired. Try again later.', 'listeo_core'); ?></span>
          <span id="error_listeo_otp_general" class="hidden"><?php esc_html_e('Something went wrong. Try again later.', 'listeo_core'); ?></span>


        </div>
        <input type="submit" class="button border fw margin-top-10" name="register" value="<?php esc_html_e('Register', 'listeo_core'); ?>" />
        <?php do_action('listeo_after_otp_form'); ?>
      </div>
    <?php endif ?>

    <?php wp_nonce_field('listeo-ajax-login-nonce', 'register_security'); ?>

    <?php if (get_option('listeo_otp_status')) : ?>
      <a class="button fw margin-top-10" id="otp_submit" name="otp"><?php esc_html_e('Register', 'listeo_core'); ?></a>
    <?php else : ?>
      <input type="submit" class="button border fw margin-top-10" name="register" value="<?php esc_html_e('Register', 'listeo_core'); ?>" />
    <?php endif ?>

    <div class="notification error closeable" style="display: none;margin-top: 20px; margin-bottom: 0px;">
      <p></p>
    </div>

  </form>

  <div class="listeo-custom-fields-wrapper">
    <?php echo listeo_get_extra_registration_fields('owner'); ?>
    <?php echo listeo_get_extra_registration_fields('guest'); ?>
  </div>

<?php endif; ?>