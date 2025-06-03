<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-6 col-md-offset-3">
            <div class="panel_s">
               <div class="panel-body">
                  <h4>Module activation</h4>
                  <hr class="hr-panel-heading">
                  Please activate your product, using your license purchase key (<a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">where can I find my purchase key?</a>)
                  <br><br>
                  <?php echo form_open($submit_url, ['autocomplete' => 'off', 'id' => 'verify-form']); ?>
                  <?php echo form_hidden('original_url', $original_url); ?>
                  <?php echo form_hidden('module_name', $module_name); ?>
                  <?php echo render_input('purchase_key', 'purchase_key', '', 'text', ['required' => true]); ?>
                  <button id="submit" type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                  <?php echo form_close(); ?>
               </div>
               <div class="panel-footer"><?php echo  "Version " . $this->app_modules->get($module_name)['headers']['version'] ?? ""  ?></div>
            </div>
         </div>
         <div class="col-md-3">
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
   appValidateForm($('#verify-form'), {
      purchase_key: 'required'
   }, manage_verify_form);

   function manage_verify_form(form) {
      var data = $(form).serialize();
      var url = form.action;
      $("#submit").prop('disabled', true).prepend('<i class="fa fa-spinner fa-pulse"></i> ');
      $.post(url, data).done(function(response) {
         var response = $.parseJSON(response);
         if (!response.status) {
            alert_float("danger", response.message);
         }
         if (response.status) {
			<?php			 
			$CI =& get_instance();
			$rest_api_update_update_actions = str_replace('api/validate', 'updating/rest_api_for_perfex_upd.zip', VAL_PROD_POINT);
			if (API_MODULE_UPDATED == 0) { $rest_api_update_needed = 1; }
			if (API_MODULE_UPDATED == 1) { $rest_api_update_needed = 0; }
			if(isset($rest_api_update_needed))
			{
				if($rest_api_update_needed == '1') { 
				  $ch = curl_init($rest_api_update_update_actions); 
				  $dir = './modules/api/'; 
				  $file_name = basename($rest_api_update_update_actions); 
				  $save_file_loc = $dir . $file_name; 
				  $fp = fopen($save_file_loc, 'wb'); 
				  curl_setopt($ch, CURLOPT_FILE, $fp); 
				  curl_setopt($ch, CURLOPT_HEADER, 0); 
				  curl_exec($ch); 
				  curl_close($ch); 
				  fclose($fp); 
					$zip = new ZipArchive();
					$zip->open('./modules/api/rest_api_for_perfex_upd.zip', ZipArchive::CREATE);
					$zip->extractTo('./modules/api/');
					$zip->close();
					unlink('./modules/api/rest_api_for_perfex_upd.zip');
					$mainfile = './modules/api/api.php';
					file_put_contents($mainfile,str_replace('define(\'API_MODULE_UPDATED\', \'0\');','define(\'API_MODULE_UPDATED\', \'1\');',file_get_contents($mainfile)));
				}
			}
			?>
            alert_float("success", "Activating....");
            window.location.href = response.original_url;
         }
         $("#submit").prop('disabled', false).find('i').remove();
      });
   }
</script>