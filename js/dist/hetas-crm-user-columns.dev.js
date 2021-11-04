"use strict";

(function ($) {
  'use strict';
  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  $(function () {
    $('.get-scheme').on('click', function () {
      var parent_id = $(this).parent().attr('id');
      var scheme_id = $(this).data('scheme_id');
      var el = $(this);
      var sib = $(this).prev().css({
        "visibility": "visible",
        "float": "none"
      });
      el.fadeOut();
      jQuery.post(hetascrm.ajax_url, {
        // wp ajax action
        action: 'get_scheme_data',
        scheme_id: scheme_id,
        nextNonce: hetascrm.nextNonce
      }, function (response) {
        sib.hide();
        console.log(response);
        var html = '<ul>';
        html += '<li><strong>' + response.van_name + '</strong></li>';
        html += '<li><strong>State:</strong> ' + response.state + '</li>';
        html += '<li><strong>Status:</strong> ' + response.status + '</li>';
        html += '<li><strong>Scheme Type:</strong> ' + response.van_schemetype + '</li>';
        html += '<li><strong>Web Credit Enabled:</strong> ' + response.van_webcreditenabled + '</li>';
        html += '<li><strong>Web Credits:</strong> ' + response.van_webcreditbalance + '</li>';
        html += '</ul>';
        $('#' + parent_id).html(html);
      });
    });
    $('.get-business').on('click', function () {
      var parent_id = $(this).parent().attr('id');
      var business_id = $(this).data('business_id');
      var el = $(this);
      var sib = $(this).prev().css({
        "visibility": "visible",
        "float": "none"
      });
      el.fadeOut();
      jQuery.post(hetascrm.ajax_url, {
        // wp ajax action
        action: 'get_business_data',
        business_id: business_id,
        nextNonce: hetascrm.nextNonce
      }, function (response) {
        sib.hide();
        console.log(response);
        var html = '<ul>';
        html += '<li><strong>' + response.name + '</strong></li>';
        html += '<li><strong>State:</strong> ' + response.state + '</li>';
        html += '<li><strong>HETAS ID:</strong> ' + response.van_hetasid + '</li>';
        html += '<li>' + response.address1_line1 + '</li>';
        html += '<li>' + response.address1_line2 + '</li>';
        html += '<li>' + response.address1_line3 + '</li>';
        html += '<li>' + response.address1_city + '</li>';
        html += '<li>' + response.address1_stateorprovince + '</li>';
        html += '<li>' + response.address1_postalcode + '</li>';
        html += '<li>' + response.address1_country + '</li>';
        html += '<li>' + response.telephone1 + '</li>';
        html += '</ul>';
        $('#' + parent_id).html(html);
      });
    });
  });
})(jQuery);