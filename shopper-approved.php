<?php
/**
 * @package Shopper_Approved
 * @version 1.01
 */
/*
Plugin Name: Shopper Approved
Plugin URI: http://wordpress.org/plugins/shopper-approved/
Description: Collect and display ratings from Shopper Approved that actually show up in Google, Yahoo, and Bing to establish credibility before customers even enter your website!
Author: Shopper Approved
Version: 1.01
Author URI: http://www.shopperapproved.com/
*/


add_action('wp_footer', 'googleSchemaCode', 100);
function googleSchemaCode()
{
    $id = esc_attr(get_option('shopperApprovedID'));
    $token = esc_attr(get_option('shopperApprovedToken'));
    if (!is_front_page())
    {
        if ($token)
        {
            if(strlen($token)>2)
            {
                echo '<p align="center"><small>';
                ini_set('default_socket_timeout', 2);
                echo file_get_contents('https://www.shopperapproved.com/feeds/schema.php/?siteid=' . $id . '&token=' . $token);
                echo '</small></p>';
            }
        }
    }
}

add_action('wp_enqueue_script', 'load_jquery');
function load_jquery()
{
    wp_enqueue_script('jquery');
}

add_action('admin_menu', 'shopperApprovedMenu');

function shopperApprovedMenu()
{
    $icon_url = plugins_url('images/mini-logo-shopper-approved-22x22.png', __FILE__);
    add_menu_page('Shopper Approved', 'Shopper Approved', 'manage_options', 'shopper-approved', 'shopperApprovedOptions', $icon_url);

    //call register settings function
    add_action('admin_init', 'register_mysettings');
}

function register_mysettings()
{
    //register our settings
    register_setting('sa-settings-group', 'shopperApprovedSite');
    register_setting('sa-settings-group', 'shopperApprovedID');
    register_setting('sa-settings-group', 'shopperApprovedReviewURL');
    register_setting('sa-settings-group', 'shopperApprovedToken');
}

function shopperApprovedOptions()
{
    if (!current_user_can('manage_options'))
    {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>

    <style type='text/css'>
        .reg {
            font-size: 8pt;
        }
        
        .settingsLink {
            cursor:pointer;
        }
        
        .note {
            font-style:italic;
        }
    </style>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {

            if ($("#shopperApprovedSite").val() != "") {
                $(".paraIntroText").hide();
            }

            if (($("#shopperApprovedID").val() == "") && ($("#shopperApprovedReviewURL").val() == "")) {
                $(".rowShortcode").hide();
                $(".rowAdvancedSetting").hide();
                $(".rowGoogleSchemaCode").hide();
                $("#linkShowWebsiteSettings").hide();
            } else {
                $(".rowAdvancedSetting").hide();
                $(".rowSelectWebsite").hide();
                $("#linkShowWebsiteSettings").show();
            }

            $(".shopperApprovedTokenTemp").hide();

            if ($("#shopperApprovedToken").val() != "") {
                //$(".shopperApprovedTokenTemp").show();
                //$("#shopperApprovedToken").hide();
            }

            var site = $("#shopperApprovedSite").val();

            getSiteList(site);

            $("#shopperApprovedSite").on('keyup blur', function () {
                site = $("#shopperApprovedSite").val();
                getSiteList(site);
            });
            
            $("#selectSASiteList").change(function () {
                var saID = $("#selectSASiteList").val();
                var reviewURL = "http://www.shopperapproved.com/reviews/" + $("#selectSASiteList option:selected").text() + "/";

                saID = (saID != 0) ? saID : "";
                reviewURL = (saID != 0) ? reviewURL : "";

		$(".rowAdvancedSetting").hide();
		$("#linkAdv").show();
                $("#shopperApprovedID").val(saID);
                $("#shopperApprovedReviewURL").val(reviewURL);
                $("#shopperApprovedSite").val($("#selectSASiteList option:selected").attr('domain'));
            });

            $("#linkAdv").click(function () {
                $(".rowAdvancedSetting").show();
                $(this).hide();
            });
            
            $("#linkRemoveToken").click(function () {
                $('#shopperApprovedToken').val('');
                $('#submit').click();
            });
            
            $("#linkShowWebsiteSettings").click(function () {
                $('.rowSelectWebsite').show();
                $('#linkShowWebsiteSettings').hide();
            });
            
            $("#linkShow").click(function () {
                $(".shopperApprovedTokenTemp").hide();
                $("#shopperApprovedToken").show();
            });
        });

        function getSiteList(site) {

	    if(site.length < 6)
	    {
		return;
	    }

            var getSiteListUrl = "<?php echo plugins_url('shopper-approved-get-site-list.php', __FILE__); ?>?site=" + site + "&id=<?php echo esc_attr( get_option('shopperApprovedID') ); ?>";

            jQuery("#imgRefreshingSiteList").show();
            jQuery.ajax({
                url: getSiteListUrl,
                complete: function () {
                },
                success: function (data) {
                    jQuery("select#selectSASiteList").html(data);
                    jQuery("#imgRefreshingSiteList").hide();
                }
            })
        }
    </script>

    <div class="wrap">
        <h2>Shopper Approved<sup class='reg'>&reg;</sup> for Wordpress</h2>
        <h3>Boosting Your Conversion Rate and Traffic at the Same Time</h3>
        
        <?php if (isset($_GET['settings-updated']))
        { ?>
            <div id="message" class="updated">
                <p><strong><?php _e('Settings saved.') ?></strong></p>
            </div>
        <?php } ?>
        <a id="linkShowWebsiteSettings" class="settingsLink description">Change Website Settings</a>
        
        <p class="paraIntroText">

        Don't have a Shopper Approved<sup class='reg'>&reg;</sup> account yet? No problem, just <a href="http://www.shopperapproved.com/" target="_blank">click here</a> to set it up and then come back to this page.</p>

        <form id="settingsForm" method="post" action="options.php">
            <?php settings_fields('sa-settings-group'); ?>
            <?php do_settings_sections('sa-settings-group'); ?>


            <table class="form-table">
                <tr class="rowSelectWebsite" valign="top">
                    <th scope="row">Shopper Approved<sup class='reg'>&reg;</sup> Website:</th>
                    <td><input type="text" placeholder="Enter at least 6 characters..." class="regular-text" name="shopperApprovedSite" id="shopperApprovedSite" value="<?php echo esc_attr(get_option('shopperApprovedSite')); ?>"/></td>
                </tr>
                <tr class="rowSelectWebsite" valign="top">
                    <th scope="row">Site List</th>
                    <td>
                        <select name="selectSASiteList" id="selectSASiteList">
                            <option selected="selected" value="0">Loading...</option>
                        </select> <img id="imgRefreshingSiteList" style="display: none; margin: 8px 5px 0px 0px;" src="<?php echo plugins_url('/images/loading3.gif', __FILE__); ?>" alt="">

                        <p class="description paraAdv settingsLink"><a id="linkAdv">Advanced Settings</a></p>
                    </td>
                </tr>
                <tr valign="top" class="rowSaID rowAdvancedSetting rowSelectWebsite">
                    <th scope="row">Shopper Approved<sup class='reg'>&reg;</sup> ID :</th>
                    <td><input type="text" class="regular-text" name="shopperApprovedID" id="shopperApprovedID" value="<?php echo esc_attr(get_option('shopperApprovedID')); ?>"/></td>
                </tr>
                <tr valign="top" class="rowReviewURL rowAdvancedSetting rowSelectWebsite">
                    <th scope="row">Shopper Approved<sup class='reg'>&reg;</sup> Review URL :</th>
                    <td>
                        <input type="text" class="regular-text" name="shopperApprovedReviewURL" id="shopperApprovedReviewURL" value="<?php echo esc_attr(get_option('shopperApprovedReviewURL')); ?>"/>

                        <p class="description">e.g. : http://www.shopperapproved.com/reviews/sample/</p>
                    </td>
                </tr>

                <tr valign="top" class="rowShortcode">
                    <td colspan="2" style="padding: 15px 0px 0px 0px;">
                        <hr/>
                        <h3>Shopper Approved<sup class='reg'>&reg;</sup> Survey Code</h3>

                        <p>Place this code on your thank you page to display the survey to your customer once they have purchased.</p>

                        <p><input type="text" class="regular-text" value="[shopper-approved-survey]" readonly/></p>

                    </td>
                </tr>
                <tr valign="top" class="rowShortcode">
                    <td colspan="2" style="padding: 15px 0px 0px 0px;">
                        <hr/>
                        <h3>Shopper Approved<sup class='reg'>&reg;</sup> Seals</h3>

			<p class='note'>Note: When placing shortcode inside of a template (ie: the header of your website), you can add it like this: <span><strong><</span>?php echo do_shortcode('[shopper-approved-seal size="small"]'); ?<span>></strong></span><br />&nbsp;</p>

                        <p>
                            Place the seals in a prominent place on your website for best results.
                            <br /><span class="note">Note: The seals won't show up until you have at least 10 reviews.</span>
                        </p>

                        <p><input type="text" class="regular-text" value='[shopper-approved-seal size="large"]' readonly/></p>

                        <p><input type="text" class="regular-text" value='[shopper-approved-seal size="small"]' readonly/></p>
			
                    </td>
                </tr>

                <tr valign="top" class="rowGoogleSchemaCode">
                    <td colspan="2" style="padding: 15px 0px 0px 0px;">
                        <hr/>
                        <h3>Search Engine Schema Code</h3>
                        <p>This will add the Schema code to all pages except your homepage allowing the search engines to put your ratings and stars directly in the search.</p>
                        <p class="note">Note: You can find your token inside your Shopper Approved Member area under Advanced Tools --> API.</p>
                    </td>
                </tr>
                <tr valign="top" class="rowGoogleSchemaCode">
                    <th scope="row">Shopper Approved<sup class='reg'>&reg;</sup> Token :</th>
                    <td>
                        <span class="shopperApprovedTokenTemp"><input type="text" class="regular-text" name="shopperApprovedTokenTemp" id="shopperApprovedTokenTemp" value="" readonly/> <a id="linkShow">Show Token</a></span>
                        <input type="text" class="regular-text" name="shopperApprovedToken" id="shopperApprovedToken" value="<?php echo esc_attr(get_option('shopperApprovedToken')); ?>"/>
                        <p class="description paraAdv settingsLink"><a id="linkRemoveToken">Remove Token</a></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <td colspan="2" style="padding: 15px 0px 0px 0px;">
                        <hr/>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>
    </div>
<?php
}

/* Shortcode */
//[shopper-approved-survey]
function shopperApprovedSurveyShortCode($atts)
{
    $shopperApprovedID = esc_attr(get_option('shopperApprovedID'));
    $returnScript = '<script type="text/javascript"> ';
    $returnScript .= 'var sa_values = { "site":' . $shopperApprovedID . '}; ';
    $returnScript .= 'function saLoadScript(src) { var js = window.document.createElement("script"); js.src = src; js.type = "text/javascript"; document.getElementsByTagName("head")[0].appendChild(js); } var d = new Date(); if (d.getTime() - 172800000 > 1415251775000) ';
    $returnScript .= 'saLoadScript("//www.shopperapproved.com/thankyou/rate/' . $shopperApprovedID . '.js"); ';
    $returnScript .= 'else saLoadScript("//www.shopperapproved.com/thankyou/rate/' . $shopperApprovedID . '.js?d=" + d.getTime()); </script>';
    return $returnScript;
}

add_shortcode('shopper-approved-survey', 'shopperApprovedSurveyShortCode');

//[shopper-approved-seal size="large"] [shopper-approved-seal size="small"]
function shopperApprovedSealShortCode($atts)
{
    $shopperApprovedID = esc_attr(get_option('shopperApprovedID'));
    $shopperApprovedReviewURL = esc_attr(get_option('shopperApprovedReviewURL'));

    $a = shortcode_atts(array(
        'size' => 'large',
    ), $atts);
    $sizeAtts = $a['size'];

    if ($sizeAtts == "large")
    {
        $returnScript = "<a href=\"" . $shopperApprovedReviewURL . "\" onclick=\"var nonwin=navigator.appName!='Microsoft Internet Explorer'?'yes':'no'; var certheight=screen.availHeight-90; window.open(this.href,'shopperapproved','location='+nonwin+',scrollbars=yes,width=620,height='+certheight+',menubar=no,toolbar=no'); return false;\">";
        $returnScript .= "<img src=\"https://c683207.ssl.cf2.rackcdn.com/" . $shopperApprovedID . "-r.gif\" style=\"border: 0\" alt=\"\" oncontextmenu=\"var d = new Date(); alert('Copying Prohibited by Law - This image and all included logos are copyrighted by Shopper Approved \251 '+d.getFullYear()+'.'); return false;\" /></a>";
    }
    else
    {
        $returnScript = "<a href=\"" . $shopperApprovedReviewURL . "\" onclick=\"var nonwin=navigator.appName!='Microsoft Internet Explorer'?'yes':'no'; var certheight=screen.availHeight-90; window.open(this.href,'shopperapproved','location='+nonwin+',scrollbars=yes,width=620,height='+certheight+',menubar=no,toolbar=no'); return false;\">";
        $returnScript .= "<img src=\"https://c683207.ssl.cf2.rackcdn.com/" . $shopperApprovedID . "-m.gif\" style=\"border: 0\" alt=\"\" oncontextmenu=\"var d = new Date(); alert('Copying Prohibited by Law - This image and all included logos are copyrighted by Shopper Approved \251 '+d.getFullYear()+'.'); return false;\" /></a>";
    }
    return $returnScript;
}

add_shortcode('shopper-approved-seal', 'shopperApprovedSealShortCode');
/* End Shortcode */
?>