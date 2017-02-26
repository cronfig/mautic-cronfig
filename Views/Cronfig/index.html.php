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

echo $view['assets']->includeStylesheet('plugins/CronfigBundle/Assets/css/cronfig.css');

?>
<!--[if lt IE 8]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<div id="cronfig-wrapper" class="col-md-12">
    <img 
        class="loading" 
        src="<?php echo $view['assets']->getUrl('plugins/CronfigBundle/Assets/img/ring.svg'); ?>" 
        onerror="this.src='<?php echo $view['assets']->getUrl('plugins/CronfigBundle/Assets/img/ring.gif'); ?>'; this.onerror=null;" 
        alt="loading..." />
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
                        if (document.cronfigConfig.tasks[i].url.indexOf('?secret_key=') === -1) {
                            document.cronfigConfig.tasks[i].url += '?secret_key=' + response.secret_key;
                        }
                    }
                }
            }, true);
        }
    }
</script>
<script type="text/javascript" src="https://cdn.cronfig.io/cronfig.js"></script>
