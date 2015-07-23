<?php defined('APPLICATION') or die;

$PluginInfo['Shariff'] = array(
    'Name' => 'Shariff',
    'Description' => '<a href="https://github.com/heiseonline/shariff">Implement social sharing buttons the private way.</a>',
    'Version' => '0.1',
    'RequiredApplications' => array('Vanilla' => '>= 2.2'),
    'RequiredTheme' => false,
    'SettingsPermission' => array('Garden.Settings.Manage'),
    'SettingsUrl' => '/dashboard/settings/shariff',
    'MobileFriendly' => true,
    'HasLocale' => true,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'http://vanillaforums.org/profile/44046/R_J',
    'License' => 'MIT'
);

/**
 *
 * @package Shariff
 * @author Robin Jurinka
 * @license MIT
 */
class ShariffPlugin extends Gdn_Plugin {
    protected $controllers = array('activitycontroller', 'discussioncontroller');

    public function setup() {
        if (!c('Shariff.Theme')) {
            saveToConfig('Shariff.Theme', 'Standard');
        }
        if (!c('Shariff.Services')) {
            saveToConfig('Shariff.Services', array('facebook', 'twitter'));
        }
        if (!c('Shariff.DataServices')) {
            saveToConfig('Shariff.DataServices', '[&quot;facebook&quot;,&quot;twitter&quot;]');
        }
    }

    /**
    *
     * @param  object $sender SettingsController.
     * @return void.
     * @package Shariff
     * @since 0.1
     */
    public function settingsController_shariff_create($sender) {
        $sender->permission('Garden.Settings.Manage');

        $sender->addSideMenu('dashboard/settings/plugins');
        $sender->title(t('Shariff Settings'));
        $sender->setData('Description', t('Shariff Setup Description'));
        
        // Save values to config.
        if ($sender->Form->authenticatedPostBack()) {
            $formPostValues = $sender->Form->formValues();
            $services = $formPostValues['Shariff.Services'];
            $dataServices = '[&quot;'.implode('&quot;,&quot;', $services).'&quot;]';
            saveToConfig('Shariff.Services', $services);
            saveToConfig('Shariff.DataServices', $dataServices);
            saveToConfig('Shariff.Theme', $formPostValues['Shariff.Theme']);
        }
        $sender->Form->setData(array(
            'Shariff.Services' => c('Shariff.Services'),
            'Shariff.Theme' => c('Shariff.Theme')
        ));
 
        $configurationModule = new ConfigurationModule($sender);

        // fill in all the info that is needed to build the settings view
        $configurationModule->schema(array(
            'Shariff.Services' => array(
                'Control' => 'CheckBoxList',
                'Description' => t('Check services that should be enabled.'),
                'Items' => array(
                    'facebook' => 'facebook',
                    'Google+' => 'googleplus',
                    'LinkedIn' => 'linkedin',
                    t('E-Mail') => 'mail',
                    'Twitter' => 'twitter',
                    'Pinterest' => 'pinterest',
                    'WhatsApp' => 'whatsapp',
                    'Xing' => 'xing',
                ),
                'LabelCode' => 'Services'
            ),
            'Shariff.Theme' => array(
                'Control' => 'DropDown',
                'Items' => array(
                    'standard' => t('Standard'),
                    'grey' => t('Grey'),
                    'white' => t('White')
                ),
                'LabelCode' => 'Theme',
                'Options' => array(
                    'IncludeNull' => false
                )
            )
        ));

        $configurationModule->renderAll();
    }

    /**
     * Remove config settings when disabling plugin.
     *
     * @return void.
     * @package Shariff
     * @since 0.1
     */
    public function onDisable() {
        // removeFromConfig('Shariff');
    }
    
    /**
     * Add css resource.
     *
     * If your theme already uses fontawesome, you can include
     * shariff.min.css instead of shariff.complete.css.
     *
     * @param $sender object GardenController.
     * @return void.
     * @package Shariff
     * @since 0.1
     */
    public function base_render_before($sender) {
        if (in_array($sender->ControllerName, $this->controllers)) {
            // $sender->addCssFile('shariff.min.css', 'plugins/Shariff');
            $sender->addCssFile('shariff.complete.css', 'plugins/Shariff');
            $sender->addCssFile('shariff.custom.css', 'plugins/Shariff');
        }
    }

    /**
     * Add js resources to end of html file.
     *
     * @param $sender object GardenController.
     * @return void.
     * @package Shariff
     * @since 0.1
     */
    public function base_afterBody_handler($sender) {
        if (in_array($sender->ControllerName, $this->controllers)) {
            echo '<script src="/', $this->getResource('js/shariff.min.js', false, false), '" type="text/javascript"></script>';
            // echo '<script src="/plugins/Shariff/github/shariff/build/shariff.complete.js" type="text/javascript"></script>';
        }
    }

    /**
     * Add buttons to reactions.
     *
     * @param $sender object GardenController.
     * @param $args mixed EventArguments.
     * @return void.
     * @package Shariff
     * @since 0.1
     */
    public function base_afterReactions_handler($sender, $args) {
        if (!in_array($sender->ControllerName, $this->controllers)) {
            return;
        }

        
        $url = 'testurl';
        // data-url 	The canonical URL of the page to check.
        // if sender = comment,  commenturl.
        // if sender = discussion, discussionurl
        // if sender = ? homepageurl
        echo '<span 
            class="shariff ReactMenu" 
            lang="', Gdn::Locale()->Locale, '" 
            data-theme="', c('Shariff.Theme'), '" 
            data-title="', c('Garden.Title'), '" 
            data-services="', c('Shariff.DataServices'), '" 
            ></span>';
        /*
        data-services="[&quot;facebook&quot;,&quot;googleplus&quot;]" Available service names: twitter, facebook, googleplus, linkedin, pinterest, xing, whatsapp, mail, info
       
        */
        
        
    }
}
