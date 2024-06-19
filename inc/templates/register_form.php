<div id="register-form" class="widecolumn">
  <?php
  // Retrieve possible errors from request parameters 
  $attributes['errors'] = array();
  if (isset($_REQUEST['register-errors'])) {
    $error_codes = explode(',', $_REQUEST['register-errors']);
    foreach ($error_codes as $error_code) {
      $attributes['errors'][] = iclub_get_error_message($error_code);
    }
  }

  // Check if the user just registered 
  $attributes['registered'] = isset($_REQUEST['registered']);

  if (count($attributes['errors']) > 0) : ?>
    <?php foreach ($attributes['errors'] as $error) : ?>
      <p>
        <?php echo $error; ?>
      </p>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if ($attributes['registered']) : ?>
    <p class="login-info">
      <?php
      printf(
        __('You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'personalize-login'),
        get_bloginfo('name')
      );
      ?>
    </p>
  <?php endif; ?>
  <?php if ($attributes['show_title']) : ?>
    <h3><?php _e('Register', 'personalize-login'); ?></h3>
  <?php endif; ?>
  <form id="register" class="register listeo-registration-form" action="<?php echo wp_registration_url(); ?>" method="post">

    <?php $default_role = get_option('listeo_registration_form_default_role', 'guest'); ?>

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

    <?php if (get_option('listeo_otp_status')) { ?>
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
          <input required type="tel" placeholder="<?php esc_html_e('Phone', 'listeo_core'); ?>" class="input-text !tw-pl-[65px] !tw-text-base !tw-leading-normal !tw-h-12" name="phone" id="phone" value="" />
        </label>
      </p>
    <?php } ?>

    <!-- //extra fields -->
    <div id="listeo-core-registration-fields">
      <?php echo listeo_get_extra_registration_fields($default_role); ?>
    </div>

    <!-- eof custom fields -->
    <?php if (!get_option('listeo_display_password_field')) : ?>
      <p class="form-row form-row-wide margin-top-30 margin-bottom-30"><?php esc_html_e('Note: Your password will be generated automatically and sent to your email address.', 'listeo_core'); ?>
      </p>
    <?php endif; ?>

    <?php $recaptcha = get_option('listeo_recaptcha');
    $recaptcha_version = get_option('listeo_recaptcha_version', 'v2');
    if ($recaptcha && $recaptcha_version == 'v2') { ?>

      <p class="form-row captcha_wrapper">
      <div class="g-recaptcha" data-sitekey="<?php echo get_option('listeo_recaptcha_sitekey'); ?>"></div>
      </p>
    <?php }

    if ($recaptcha && $recaptcha_version == 'v3') { ?>
      <input type="hidden" id="rc_action" name="rc_action" value="ws_register">
      <input type="hidden" id="token" name="token">
    <?php } ?>

    <?php
    $privacy_policy_status = get_option('listeo_privacy_policy');

    if ($privacy_policy_status && function_exists('the_privacy_policy_link')) { ?>
      <p class="form-row margin-top-10 checkboxes margin-bottom-10">
        <input type="checkbox" id="privacy_policy" name="privacy_policy">
        <label for="privacy_policy"><?php esc_html_e('I agree to the', 'listeo_core'); ?> <a target="_blank" href="<?php echo get_privacy_policy_url(); ?>"><?php esc_html_e('Privacy Policy', 'listeo_core'); ?></a> </label>

      </p>

    <?php } ?>

    <?php
    $terms_and_condition_status = get_option('listeo_terms_and_conditions_req');
    $terms_and_condition_status_page = get_option('listeo_terms_and_conditions_page');

    if ($terms_and_condition_status) { ?>
      <p class="form-row margin-top-10 checkboxes margin-bottom-10">
        <input type="checkbox" id="terms_and_conditions" name="terms_and_conditions">
        <label for="terms_and_conditions"><?php esc_html_e('I agree to the', 'listeo_core'); ?> <a target="_blank" href="<?php echo get_permalink($terms_and_condition_status_page); ?>"><?php esc_html_e('Terms and Conditions', 'listeo_core'); ?></a> </label>

      </p>
    <?php } ?>
</div>

<?php wp_nonce_field('listeo-ajax-login-nonce', 'register_security'); ?>

<p class="signup-submit">
  <input type="submit" name="register" class="register-button !tw-h-12 !tw-text-base !tw-px-10" value="<?php _e('Register', 'personalize-login'); ?>" />
</p>

<div class="listeo-custom-fields-wrapper">
  <?php echo listeo_get_extra_registration_fields('owner'); ?>
  <?php echo listeo_get_extra_registration_fields('guest'); ?>
</div>

</form>
</div>