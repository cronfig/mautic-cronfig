<?php
/**
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$view->extend('MauticCoreBundle:Default:content.html.php');

$view['slots']->set('mauticContent', 'cronfig');

$view['slots']->set('headerTitle', $view['translator']->trans('cronfig.title'));

?>
<!--[if lt IE 8]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<div id="cronfig-wrapper" class="col-md-12">
    <p>If you can see this, something is broken (or JS is not enabled)!!.</p>
</div>
<script type="text/javascript">
    document.cronfigConfig = {
        platform: 'mautic',
        tasks: <?php echo json_encode($commands) ?>,
        email: '<?php echo $email ?>',
        apiKey: '<?php echo $apiKey ?>',
        rememberApiKey: function(apiKey) {
            Mautic.ajaxActionRequest('plugin:cronfig:saveApiKey', 'apiKey=' + apiKey, function(response) {
                if (typeof response.secret_key !== 'undefined') {
                    for (var i = 0; i < document.cronfigConfig.tasks.length; i++) {
                        document.cronfigConfig.tasks[i].url += '?secret_key=' + response.secret_key;
                    }
                }
            }, true);
        }
    }
</script>
<script type="text/javascript" src="//cdn.cronfig.io/cronfig.js"></script>
